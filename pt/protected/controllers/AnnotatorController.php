<?php

class AnnotatorController extends Controller
{
	//@TODO move this template list to a config or autodetect
	//@TODO convert to a constant
	private $templatesList=array(
			0 => 'jsbased', 
			1 => 'kindle',
			2 => 'dynamic'
			);
	private $templatesListLabels=array(
			0 => 'Default',
			1 => 'Optimized for Kindle',
			2 => 'Dynamic'
			);
	 
	public function actionDemo($id) {
		//
	}
	
	public function actionIndex()
	{
		$systemList=System::getReadableSystems();
		$allDicts=Dictionary::model()->findAll();
		$defaultSelectedDicts=UserSettings::getCurrentSettings()->lastAnnotatorDictionaries;
		$systemLast=UserSettings::getCurrentSettings()->lastSystemInAnnotator;
		$lastTemplate=UserSettings::getCurrentSettings()->lastTemplateInAnnotator;

		if($lastTemplate==NULL) { //@TODO move to defaults to settings
			$lastTemplate=0;
		}
		
		$this->render('index', array(
			'systemList'=>$systemList,
			'systemLast'=>$systemLast,
				'allDicts'=>$allDicts,
				'selectedDicts'=>$defaultSelectedDicts,
				'lastTemplate'=>$lastTemplate,
				'templatesList'=>$this->templatesListLabels,
				));
	}
	
	public function actionGo()
	{
		//@TODO check the right to read from the given system (obviously after the reading permissions have been implemented)
		
		$annotatorEngine=new AnnotatorEngine();
		
		if(isset($_POST['input']) && !empty($_POST['input'])) {
			$annotatorEngine->input=$_POST['input'];
		} else {
			$this->redirect(array('index'));
		}
		
		
		$annotatorEngine->parent=$this;
		$annotatorEngine->systemID=!empty($_POST['system']) ? ((int)$_POST['system']) : NULL;
		$annotatorEngine->dictionariesID=isset($_POST['selectedDictionaries']) ? ($_POST['selectedDictionaries']) : NULL;
		
		$annotatorEngine->parallel=isset($_POST['parallel']) ? $_POST['parallel'] : NULL;
		$annotatorEngine->audioURL=isset($_POST['audioURL']) ? $_POST['audioURL'] : NULL; //@TODO add URL validator
		$annotatorEngine->outputType=isset($_POST['type']) ? $_POST['type'] : NULL;
		$annotatorEngine->whitespaceToHTML=true;
		//@TODO set from settings
// 		$annotatorEngine->characterMode=UserSettings::getCurrentSettings()->variant;
		$annotatorEngine->characterMode=AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY;
		
		//check if the selected template exists
		$templateId=isset($_POST['template']) ? ((int)$_POST['template']) : NULL;
		$templateId=($templateId>=0 && $templateId<count($this->templatesList)) ? $templateId : 0;
		$annotatorEngine->template=$this->templatesList[$templateId];
		
		UserSettings::getCurrentSettings()->lastSystemInAnnotator=$annotatorEngine->systemID;
		UserSettings::getCurrentSettings()->lastAnnotatorDictionaries=$annotatorEngine->dictionariesID;
		UserSettings::getCurrentSettings()->lastTemplateInAnnotator=$templateId;
		UserSettings::getCurrentSettings()->saveSettings();
		
		ini_set('max_execution_time', 60000); //@TODO not sure if this is the best way
		$annotatorEngine->annotate();
	}	
	
	public function actionBox() {
		//$s, $d, $t
		$systemID=$_GET['s'];
		$dictionariesID=$_GET['d'];
		$char=$_GET['t'];
		$compoundsOnly=$_GET['o'];
 		$characterMode=AnnotatorEngine::CHARMOD_ALLOW_BOTH; //@TODO load from request
				
		$transcriptionFormatters=AnnotatorEngine::createFormatters($dictionariesID);
		$result=null;
		$phrasesResult=array();
		
		if(!$compoundsOnly) {
			$system=System::model()->findByPk($systemID);
			$translations=AnnotatorEngine::loadTranslationsFromDictionaries($char, $dictionariesID, $characterMode);
			$mnemos=AnnotatorEngine::loadMnemonicsForSystem($char, $system);
			$result=$this->boxToArray($translations, $mnemos, $transcriptionFormatters, $characterMode);
		}
		
		if(!empty($_GET['c'])) {
			$compounds=$_GET['c'];
			$phrases=AnnotatorEngine::loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, $characterMode);
			$phrasesResult=$this->phrasesToArray($phrases,$transcriptionFormatters, $characterMode);
		}
		if($result==null) {
			//
			$a=5;
		}
		echo json_encode(array($result, $phrasesResult));
	}
	
	
	//these two methods perhaps belong in the view
	/**
	 * Escapes dashes and removes newlines from the given text.
	 * @param string $text
	 */
	 public function prepare($text) {
	 	return str_replace(array("\r","\n"), "", str_replace(array('\'', '"'), "\\'", $text));
	 }
	
	 /**
	 *
	 * @param Composition[] $composition
	  */
	  public function outputKeywords($composition) {
	  	
	  	if(count ( $composition )==0)
	  		return "";
	  	
	  	$result = '<br>';
		
		for($i = 0; $i < count ( $composition ); $i ++) {
			$sub = $composition [$i]->subchar;
			
			$result .= $sub->keyword;
			$result .= ' ';
			$result .= $sub->chardef;
			
			if ($i != count ( $composition ) - 1)
				$result .= " + ";
		}
		return $result;
	}
	
	//shared between jsbased/perchar.php & dynamic (server-side in ajax)
	public function boxToDisplay($translations, $mnemos, $phrases, $transcriptionFormatters, $characterMode) {

		$result='';
		//phrases
		if($phrases!=null)
		foreach($phrases as $phrase) {
			$result.="'";	
			$result.=$phrase->getText($characterMode); 
			$result.="','";
			$result.=$transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription); 
			$result.="',new Array("; 

			//$result.="'".implode("','", $phrase->translationsArray)."'";
					
			foreach($phrase->translationsArray as $tr) {
				$result.="'"; 
				$result.=$this->prepare($tr); 
				$result.="',"; 
			} 
			$result.="''),"; 
// 			$result.="),"; 
		}
		
		//single character translations
		foreach($translations as $trans) { 
		
				$result.="'"; 
				$result.=$trans->getText($characterMode); 
				$result.="','"; 
				$result.=$transcriptionFormatters[$trans->dictionaryId]->format($trans->transcription); 
				$result.="',new Array("; 
				foreach($trans->translationsArray as $tr) {
					$result.="'"; 
					$result.=str_replace(array('\'', '"'), "\\'", $tr); 
					$result.="',"; 
				} 
				$result.="''),"; 				
		} 
		
		//tag (or empty dashes if none set)
		$result.="'";
				if(!empty($mnemos)) {
				 	$result.='<b>'.$this->prepare($mnemos->keyword).'</b><br>'; 
		// 		 	echo $mnemos->keyword; //no html, destroys the JS
				 	$result.=$this->prepare($mnemos->mnemonicsHTML);
				 	$result.=$this->outputKeywords($mnemos->components);
				 }
		$result.="'";
		return $result;				 
	}

	/**
	 * 
	 * @param DictEntryPhrase[] $phrases
	 * @param unknown $transcriptionFormatters
	 * @param Enum $characterMode
	 * @return Array
	 * 		the key is the full phrase text
	 *      the value is an array (with one item for each different transcription)
	 *        whose first item is the transcription
	 *        and second item is the array of translations  
	 */
	public function phrasesToArray($phrases,$transcriptionFormatters, $characterMode) {
		$result=array();
		
		if(!empty($phrases))
		foreach($phrases as $phrase) {
			$text=$phrase->getText($characterMode); //note there are sometimes multiple results with one text (with different pronunciation)
			
			$result[$text][]=
			array($transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription),
			$phrase->translationsArray);
		}
		
		return $result;
	}
	
	public function boxToArray($translations, $mnemos, $transcriptionFormatters, $characterMode) {
		$result=array();
		//phrases
		/*
		if(!empty($phrases))
		foreach($phrases as $phrase) {
			$result[]=$phrase->getText($characterMode);
			$result[]=$transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription);
			$result[]=$phrase->translationsArray;
		}*/
		
		//single character translations
		foreach($translations as $trans) {
			$result[]=$trans->getText($characterMode);
			$result[]=$transcriptionFormatters[$trans->dictionaryId]->format($trans->transcription);
			$result[]=$trans->translationsArray ;
		}
		
		//tag (or empty dashes if none set)
		if(!empty($mnemos)) {
			$htmlMnemo=$mnemos->mnemonicsHTML;
			if(!empty($htmlMnemo)) $htmlMnemo='<br>'.$htmlMnemo;
			
			$result[]=
			'<b>'.$mnemos->keyword.'</b>'.
			$htmlMnemo.
			$this->outputKeywords($mnemos->components);
		} else {
			$result[]=''; //if no mnemos, just add an empty entry (too keep the count correct)
		}
		
		return $result;
		}	
}