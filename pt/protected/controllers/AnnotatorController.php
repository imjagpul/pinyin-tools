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
		$annotatorEngine->characterMode=$isTraditional ? AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY : AnnotatorEngine::CHARMOD_TRADITIONAL_ONLY;
		
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
// 		$defaultSelectedDicts=UserSettings::getCurrentSettings()->lastAnnotatorDictionaries;
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
		
		$annotatorEngine=$longtask->createFinalAnnotatorEngine($this);
		$annotatorEngine->handleOutputMode();
		
		$annotatorEngine->finalOutputAnnotate();
		
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
			
		//@TODO : this could be extended by returning a "wait" command if there are too much other processes running
		//we do not have to be able to handle limited execution time after all
		/*
		$maxtime=30;
		if(!empty(ini_get('max_execution_time'))) { //@TODO move magic numbers to configuration
			$maxtime=min(30, ini_get('max_execution_time')-10);
		} 
		$finish=$_SERVER['REQUEST_TIME']+$maxtime;
		
		$done=false;

		while(time()<$finish && !$done) {
			//HERE implement
		}
*/
		$longtask=Longtask::model()->findByAttributes(array('id'=>$id));
		$chunkCount=$longtask->chunk_count;

		if($longtask->next_chunk == $chunkCount) {
			//we are done already, return the result
			echo CJSON::encode(array('status'=>'ok'));
			return;
		}
		
		//first make sure the dictionaries exist
		//@TODO generate cached dictionary only if using pregenerated dictionaries
// 		$worked=DictionaryCacheWorker::ensureCacheDictionary(false, $longtask->dict_id, $longtask->system_id);
// 		if($worked) {
// 			//if the charactes dictionary was generated, not further work will be done in this call
// 			echo CJSON::encode(array('status'=>'continueWordsDict'));
// 			return;
// 		}
		
// 		$worked=DictionaryCacheWorker::ensureCacheDictionary(true, $longtask->dict_id, $longtask->system_id);
// 		if($worked) {
// 			//if the phrases dictionary was generated, not further work will be done in this call
// 			echo CJSON::encode(array('status'=>'continuePhrasesDict'));
// 			return;
// 		}
		
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
	 * Creates a new Longtask and redirects to the processing page.
	 */
	public function actionGo()
	{
		if(!isset($_POST['input']) || empty($_POST['input'])) {
			$this->redirect(array('index'));
		}
		
		$annotationTask = new Longtask();
		
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
		
		$success=$annotationTask->insert();	//insert (to get the ID)
		
		if($success!==TRUE)
			$this->redirect(array('index')); //@TODO implement an error message

		//preprocess the input during this request
		//@TODO: check if it works fine with parallel; or skip during parallel
		$input=AnnotatorEngine::preprocessInput($_POST['input']);

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
	
	//@TODO delete (or maybe keep for the quick processing)
	public function goDirect()
	{
		//@TODO check the right to read from the given system (obviously after the reading permissions have been implemented)
		//@TODO to be tested
		
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

		//@TODO set from settings (after deciding if and how the convereting should be done)
// 		$annotatorEngine->characterMode=UserSettings::getCurrentSettings()->variant;
		$annotatorEngine->characterMode=AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY;
		
		//setup the mode
		$modeID=isset($_POST['mode']) ? ((int)$_POST['mode']) : NULL;
		$annotatorEngine->mode=AnnotatorMode::parseMode($modeID);

		//see which action the user has chosen
		if(array_key_exists('submit-download', $_POST)) {
			$annotatorEngine->outputMode=AnnotatorMode::MODE_DOWNLOAD;
		}  else {
			$annotatorEngine->outputMode=AnnotatorMode::MODE_SHOW;
		}
		
		UserSettings::getCurrentSettings()->lastSystemInAnnotator=$annotatorEngine->systemID;
		UserSettings::getCurrentSettings()->lastAnnotatorDictionaries=$annotatorEngine->dictionariesID;
		UserSettings::getCurrentSettings()->lastTemplateInAnnotator=$templateId;
		UserSettings::getCurrentSettings()->saveSettings();
		
// 		ini_set('max_execution_time', 60000); //@TODO not sure if this is the best way
// 		$annotatorEngine->annotate();
		$annotatorEngine->annotate2();
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
 		$characterMode=AnnotatorEngine::CHARMOD_ALLOW_BOTH; //@TODO (perhaps) load from request
				
		$transcriptionFormatters=AnnotatorEngine::createFormatters($dictionariesID);
		$result=null;
		$phrasesResult=array();
		
		//load the data from the dictionary (if required)
		if(!$compoundsOnly) {
			$system=System::model()->findByPk($systemID);
			$translations=AnnotatorEngine::loadTranslationsFromDictionaries($char, $dictionariesID, $characterMode);
			$mnemos=AnnotatorEngine::loadMnemonicsForSystem($char, $system);
			$result=$this->boxToArray($translations, $mnemos, $transcriptionFormatters, $characterMode);
		}
		
		//load the data for the phrases (if any specified)
		if(!empty($_GET['c'])) {
			$compounds=$_GET['c'];
			$phrases=AnnotatorEngine::loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, $characterMode);
			$phrasesResult=$this->phrasesToArray($phrases,$transcriptionFormatters, $characterMode);
		}
		
		echo json_encode(array($result, $phrasesResult));
	}
	
	public function actionOutputDictionary() {
// 		ini_set('max_execution_time', 60000); //@TODO not sure if this is the best way
		
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