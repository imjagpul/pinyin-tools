<?php

class AnnotatorController extends Controller
{
	/**
	 * This method runs the annotator with hardcoded settings.
	 * The purpose is to generate a typical output that can server as a good demonstration of the annotator
	 * abilities.
	 *  
	 * It is intended to be run once, because the output is to be cached manually and linked directly from
	 * the main page.
	 *  
	 * @param Integer $id
	 * 		one of the hardcoded modes:
	 *					1 - default, simplified
	 *					2 - default, traditional
	 *					3 - no-js, simplified
	 *					4 - no-js, traditional
	 *					5 - mobi/epub, simplified
	 *					6 - mobi/epub, traditional
	 */
	 public function actionGenerateDemo($id) {
		//first load the predefined input into the engine
		$textManager=new TextManager();
		
		if($id<1 || $id>6)
			throw new Exception("ID has to be 0>ID>7");
	 	
		
		$textData=$textManager->getText('sanzijing');

		$isTraditional=($id==1 || $id==3 || $id==5);
		$isJsBased = ($id==1 ||$id==2);
		
		$annotatorEngine=new AnnotatorEngine();
		$annotatorEngine->parent=$this;
		$annotatorEngine->input=$isTraditional ? $textData->getTextSimplified() : $textData->getTextTraditional();
		$annotatorEngine->characterModeAnnotations=$isTraditional ? CharacterModeAnnotations::CHARMOD_SIMPLIFIED_ONLY : CharacterModeAnnotations::CHARMOD_TRADITIONAL_ONLY;
		
		$annotatorEngine->systemID=13; //hardcoded ("Explanation of characters by Herbert A. Giles")
		$annotatorEngine->dictionariesID=array(1); //hardcoded ("English")
		$annotatorEngine->parallel=$textData->getTextParallel();
		$annotatorEngine->audioURL=$textData->getTextAudioPath();
		$annotatorEngine->outputMode=AnnotatorMode::MODE_SHOW;
		$annotatorEngine->whitespaceToHTML=true;
		
		//choose the correct template
		if($isJsBased) {
			$annotatorEngine->mode=new AnnotatorModeOffline;
		} else {
			$annotatorEngine->mode=new AnnotatorModePortable;
		}
		
		$annotatorEngine->prependText=$this->renderPartial('demonstrationText',null,true,false);
		
		//then start the annotation
		$annotatorEngine->annotate();
	}
	
	public function actionIndex() {
		$this->actionInput(0);
	}
	
	public function actionInput($modeID)
	{		
		$mode=AnnotatorMode::parseMode($modeID);
		
		$systemList=System::getListedSystems();
		$systemListOwn=System::getWriteableSystems();
		
		$allDicts=Dictionary::model()->findAll();
		$defaultSelectedDict=UserSettings::getCurrentSettings()->lastDictionaryInAnnotator;
		$systemLast=UserSettings::getCurrentSettings()->lastSystemInAnnotator;
		$lastTemplate=UserSettings::getCurrentSettings()->lastTemplateInAnnotator;

		$this->render('input', array(
				'systemListOwn'=>$systemListOwn,
				'systemList'=>$systemList,
				'systemLast'=>$systemLast,
				'mode'=>$mode,
				'allDicts'=>$allDicts,
				'selectedDict'=>$defaultSelectedDict,
				));
	}
	
	/**
	 * Displays the pages that is displayed to the user when a process is being requested.
	 * 
	 * The view will send repeated keep-alive AJAX queries.
	 * 
	 * @param Integer $id
	 * 			ID of the Longtask
	 */
	public function actionProcess($id) {
		
		$model=Longtask::model()->findByAttributes(array('id'=>$id));
		
		if($model->next_chunk === $model->chunk_count) {
			//we are done already, return the result
			$this->actionGetTask($id);
			return;
		}
		
		$this->render('process', array(
				'id'=>$id
		));
	}
	
	/**
	 * Shows or downloads the results of a Longtask.
	 * 
	 * @param Integer $id
	 * 			ID of the Longtask
	 */
	public function actionGetTask($id) {
		$longtask = Longtask::model()->findByAttributes(array('id'=>$id));
		
		$longtask->finalOutput($this);		
	}

	/**
	 * This is called from by AJAX from the client repeatedly, until the job is finished.
	 * 
	 * It starts or continues to process the task repeatedly
	 * @param Integer $id
	 * 				the Longtask id
	 */
	public function actionProcessBackground($id) {
		header('Content-Type: application/json; charset="UTF-8"');

		$longtask=Longtask::model()->findByAttributes(array('id'=>$id));
		$chunkCount=$longtask->chunk_count;

		if($longtask->next_chunk == $chunkCount) {
			//we are done already, return the result
			echo CJSON::encode(array('status'=>'ok'));
			return;
		}
				
		//create an AnnotatorEngine and annotate next chunk
		list($annotatorEngine, $chunk)=$longtask->createNextAnnotatorEngine($this);
		
		$result = $annotatorEngine->annotateChunk();
		list($chunk->result, $chunk->result2)=$result;

		$success=$chunk->update();
		
		if(!$success)
			$status='error';
		else {
			$longtask->next_chunk++;
			$longtask->update();
			
			if($longtask->next_chunk == $chunkCount) {
				$status='ok';
				
				//log total time
				if(YII_DEBUG) {
					$jobId=$longtask->id;
					$totalTime=time()-strtotime($longtask->submit_time);
					Yii::log("Job (id=$jobId) finished in $totalTime seconds.",CLogger::LEVEL_PROFILE);
				}
			} else {
				echo CJSON::encode(array('status'=>'progress', 'current'=>$longtask->next_chunk, 'count'=>$chunkCount));
				return;
			}
		}
		
		echo CJSON::encode(array('status'=>$status));
	}

	/**
	 * Creates and returns a new LongTask object, excluding input.
	 * @retunr LongTask
	 */
	private function createLongtaskFromPost() {
		$annotationTask = new Longtask();

		//system and dictionaries
		$annotationTask->system_id=!empty($_POST['system']) ? ((int)$_POST['system']) : NULL;
		$annotationTask->dict_id=isset($_POST['selectedDictionaries']) ? ($_POST['selectedDictionaries']) : NULL;
		
		//setup the mode
		$modeID=isset($_POST['mode']) ? ((int)$_POST['mode']) : NULL;
		$annotationTask->mode=$modeID;

		//see which action the user has chosen
		if(array_key_exists('submit-download', $_POST)) {
			$annotationTask->outputMode=AnnotatorMode::MODE_DOWNLOAD;
		}  else {
			$annotationTask->outputMode=AnnotatorMode::MODE_SHOW;
		}
				
		$annotationTask->parallelText=isset($_POST['parallel']) ? $_POST['parallel'] : NULL;
		$annotationTask->audioLink=isset($_POST['audioURL']) ? $_POST['audioURL'] : NULL; //@TODO add URL validator

		//apply globel settings to the current task
		//TODO - it would be more consequent to have it here; now it is done at annotator creating
// 		$annotationTask->
		
		return $annotationTask;
	}
	
	/**
	 * Either creates a new Longtask and redirects to the processing page
	 * or processes the input directly.
	 */
	public function actionGo()
	{
		if(!isset($_POST['input']) || empty($_POST['input'])) {
			$this->redirect(array('index'));
		}
		
		//first process the input form
		$annotationTask = $this->createLongtaskFromPost();
		$annotationTask->saveAsLastUsedToSettings(); //TODO implment
		$parallelMode=isset($_POST['parallel']);		
		
		//preprocess the input always during this request
		if(!$parallelMode)
			$input=AnnotatorEngine::preprocessInput($_POST['input']);
		else
			$input=$_POST['input'];
			
		//now there are two options: the input gets annotated 
		//either directly in this request, or saved as a background task		
		if(mb_strlen($input)<Yii::app()->params ['annotatorChunkInputSizeAlwaysDirectMax'] || $annotationTask->getModeParsed()->getAlwaysDirectProcessing() || $parallelMode) {
			//if the input is short; or if using the Quick mode, we can handle it in this request
			$annotatorEngine=$annotationTask->createEmptyAnnotatorEngine($this);
			$annotatorEngine->input=$input;
			$annotatorEngine->annotateDirect();
			return;
		}
		//@TODO implement background mode for $parallelMode
		//(perhaps could modify to line per chunk?)

		//insert the task data to DB and then split into chunks
		$success=$annotationTask->insert();	//insert (to get the ID)
		if($success!==TRUE)
			$this->redirect(array('index')); //@TODO implement an error message

		//now we need to split the input into chunks and save into the DB
		$encoding = Yii::app()->params['annotatorEncoding'];
		$chunksizeMin=Yii::app()->params ['annotatorChunkInputSizeMin'];
		$chunksizeMax=Yii::app()->params ['annotatorChunkInputSizeMax'];
		$len=mb_strlen($input, $encoding);
		
		$lastId=0;
		for($i=0; $i < $len; $i+=$chunksizeMax) {
			$chunk = new LongtaskChunk();
			$chunk->longtask_id=$annotationTask->id;
			$chunk->id=$lastId++;
			$chunk->startIndex=$i;
			
			//split at an "ignored char"
			$nextChunkText = mb_substr($input, $i, $chunksizeMax, $encoding);
			for($j=$chunksizeMax;$j>$chunksizeMin;$j--) {
				if(AnnotatorEngine::isIgnoredChar(mb_substr($nextChunkText, $j, 1, $encoding))) {
					//we have found a good splitting point
					$nextChunkText=mb_substr($input, $i, $j, $encoding);
					
					//also need to adjust the starting point for next chunk
					$i+=$j;
					$i-=$chunksizeMax;
					break;
				}
			}
			//if there is no "ignored char" within the limits, just keep the maximum size chunk
				
			$chunk->input=$nextChunkText;
			$chunk->insert();
		}
		
		$annotationTask->chunk_count=$lastId; //it got increased once too often (so this is not actually and ID, but the total count of chunks)
		$annotationTask->update();
		
		$this->redirect(array('process', 'id'=>$annotationTask->id));
			
	}

	/**
	 * Called via AJAX when the user points on a character.
	 * 
	 * This method outputs JSON-encoded two member array 
	 * (first item is the data for the character, the second item the data for the compositions).
	 */
	public function actionBox() {
		$systemID=$_GET['s'];
		$dictionariesID=$_GET['d'];
		$char=$_GET['t'];
		$compoundsOnly=$_GET['o'];
 		$characterMode=UserSettings::getCurrentSettings()->characterModeAnnotationsParsed;
				
		$transcriptionFormatters=AnnotatorEngine::createFormatters($dictionariesID);
		$result=null;
		$phrasesResult=array();
		
		//load the data from the dictionary (if required)
		if(!$compoundsOnly) {
			$system=System::model()->findByPk($systemID);
			$translations=AnnotatorEngine::loadTranslationsFromDictionaries($char, $dictionariesID);
			$mnemos=AnnotatorEngine::loadMnemonicsForSystem($char, $system);
			$result=$this->boxToArray($translations, $mnemos, $transcriptionFormatters, $characterMode);
		}
		
		//load the data for the phrases (if any specified)
		if(!empty($_GET['c'])) {
			$compounds=$_GET['c'];
			$phrasesSimpl=AnnotatorEngine::loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, CharacterModeInput::CHARMOD_CONVERT_TO_SIMPLIFIED);
			$phrasesTrad=AnnotatorEngine::loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, CharacterModeInput::CHARMOD_CONVERT_TO_TRADITIONAL);
			
			$phrasesResult=$this->phrasesToArray($phrasesSimpl, $phrasesTrad, $transcriptionFormatters, $characterMode);
			
// 			$phrases=AnnotatorEngine::loadPhrasesFromDictionaries($char, $compounds, $dictionariesID);
// 			$phrasesResult=$this->phrasesToArray($phrases,$transcriptionFormatters, $characterMode);
		}
		
		echo json_encode(array($result, $phrasesResult));
	}
	
	public function actionOutputDictionary() {
		$dictID=1;
		$systemID=null; 
		AnnotatorEngine::outputDictionary($this, false, $dictID, $systemID);
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
	  public static function outputKeywords($composition) {
	  	
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
	
	/**
	 * Joins the elements of the given array so that 
	 * the results of calling #prepare() on each element is surronded with single quotes
	 * and separated with commas.
	 * 
	 * @param String[] $arr
	 */
	private function implodeWithPrepare($arr) {
		$result='';
		
		$first=true;
		foreach($arr as $tr) {
			//separate with commas
			if(!$first)
				$result.=",";
			else 
				$first=false;
		
			//every entry in single quotes
			$result.="'";
			$result.=$this->prepare($tr);
			$result.="'";
		}
		
		return $result;
	}
	
	/**
	 * This method returns a javascript array that is used in offline js-based template.
	 */	
	public function boxToDisplay($translations, $mnemos, $phrases, $transcriptionFormatters, $characterModeAnnotations) {
		//format (has to be synchronized with translationsBox.js):
		//boxdata is an array of lengeth = n*3 + 1
		//quadruples of cn-primary, cn-alt, transcription, translations
		//last element is the tags
				
		$result='';
		
		//TODO remove code duplicity
		//phrases
		if($phrases!=null)
		foreach($phrases as $phrase) {
			$result.="'";	
			$result.=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $phrase->getText(true), $phrase->getText(false));
			$result.="','";
			$result.=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $phrase->getText(true), $phrase->getText(false));
			$result.="','";
			$result.=$transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription); 
			$result.="',new Array("; 
			$result.=$this->implodeWithPrepare($phrase->translationsArray);
			$result.="),"; 
		}
		
		//single character translations
		foreach($translations as $trans) { 
		
				$result.="'"; 
				$result.=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $trans->getText(true), $trans->getText(false));
				$result.="','";
				$result.=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $trans->getText(true), $trans->getText(false));
				$result.="','"; 
				$result.=$transcriptionFormatters[$trans->dictionaryId]->format($trans->transcription); 
				$result.="',new Array("; 
				$result.=$this->implodeWithPrepare($trans->translationsArray);
				$result.="),"; 				
		} 
		
		//tag (or empty dashes if none set)
		$result.="'";
				if(!empty($mnemos)) {
				 	$result.='<b>'.$this->prepare($mnemos->keyword).'</b><br>'; 
		// 		 	echo $mnemos->keyword; //no html, destroys the JS
				 	$result.=$this->prepare($mnemos->mnemonicsHTML);
				 	$result.=$this->prepare($this->outputKeywords($mnemos->components));
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
	public function phrasesToArray($phrasesSimpl, $phrasesTrad, $transcriptionFormatters, $characterModeAnnotations) {
		$result=array();
		
		if(!empty($phrasesSimpl))
			
		foreach($phrasesSimpl as $phrase) {
			$simpl=$phrase->getText(true);
			$trad=$phrase->getText(false);
			
			$primary=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $simpl, $trad);
			$alt=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $simpl, $trad);
			
			$result[$simpl][]=
			array(
				$primary,
				$alt,
				$transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription),
				$phrase->translationsArray);
		}
		
		if(!empty($phrasesTrad))
		foreach($phrasesTrad as $phrase) {
			$simpl=$phrase->getText(true);
			$trad=$phrase->getText(false);

			$primary=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $simpl, $trad);
			$alt=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $simpl, $trad);
				
			if($simpl!=$trad || empty($phrasesSimpl)) { //return no phrases twice
				
				$result[$trad][]=
				array(
					$primary,
					$alt,
					$transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription),
					$phrase->translationsArray);
				 }
		}
		
		return $result;
	}
	
	public function boxToArray($translations, $mnemos, $transcriptionFormatters, $characterModeAnnotations) {
		$result=array();
		
		//single character translations
		foreach($translations as $trans) {
			$result[]=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $trans->getText(true), $trans->getText(false));
			$result[]=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $trans->getText(true), $trans->getText(false));
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
		
	public function echoVariants($phrase, $characterModeAnnotations) {
		$simplified=$phrase->getText(true);
		$traditional=$phrase->getText(false);
		
		$primary=CharacterModeAnnotations::getPrimary($characterModeAnnotations, $simplified, $traditional); 
		$alt=CharacterModeAnnotations::getAlternate($characterModeAnnotations, $simplified, $traditional);
		
		echo $primary;
		if(!empty($alt) && $alt!=$primary) {
			echo '[';
			echo $alt;
			echo ']';			
		}		
	}
}