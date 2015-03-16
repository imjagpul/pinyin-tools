<?php
class AnnotatorEngine {
	const ignoredCharsJs=" \\\",.;'!。，《》…：“”？！　0"; //withonut newlines and with escaped quote
	const ignoredChars=" \n\t\r\",.;'!。，《》…：“”？！　0"; // @TODO complement special asian characters

	const CHARMOD_SIMPLIFIED_ONLY=1;
	const CHARMOD_TRADITIONAL_ONLY=2;
	const CHARMOD_CONVERT_TO_SIMPLIFIED=3;
	const CHARMOD_CONVERT_TO_TRADITIONAL=4;
	const CHARMOD_ALLOW_BOTH_PREFER_SIMP=5;
	const CHARMOD_ALLOW_BOTH=5;
	//const CHARMOD_ALLOW_BOTH=5;

	public $parent;
	public $input;
	public $systemID;
	public $dictionariesID;
	public $characterMode;
	public $template="jsbased";
	public $whitespaceToHTML=true; 
	public $parallel;
	public $audioURL;
	/** @var String If the output is intended for downloading or for viewing (affects the mimetype). */
	public $outputType;
	/** @var String Text to be included as is at the beginning of the output. Useful for the demo page. */
	public $prependText=NULL;
	
	private $encoding;
	private $colors;
	private $system;
	private $transcriptionFormatters;
	private $parallelLines;
	private $len;
// 	private $currentTemplateFull;

	/**
	 *
	 * @throws CException
	 */
	public function annotate() {
		$startTime=time();
		
		$this->prepare();
		$this->outputHeader();
		$this->outputAudioPlayer();
		$this->outputParallelBeforeChars();
		$this->goTemplates();
		$this->outputParallelAfterChars();
		$this->outputFooter();
		
		$totalTime=time()-$startTime;
		echo "<!-- took $totalTime s -->";
	}
/*	
	public static function getBoxData($systemID, $dictionariesID, $char) {
		self::loadTranslationsFromDictionaries($char, $dictionariesID);
		self::loadMnemonicsForSystem($char, $systemID);
		$transcriptionFormatters=self::createFormatters($dictionariesID);
		
	}*/
	
	private function prepare() {
		$this->encoding=Yii::app()->params['annotatorEncoding'];
		$this->colors=UserSettings::getCurrentSettings()->annotatorColors; //@TODO load from GUI instead
		$this->len=mb_strlen($this->input,$this->encoding);
		
		if($this->len==0) {
			//no input given - even though that should be checked already
			throw new CException("No input given.");
		}
		
		$this->transcriptionFormatters=$this->createFormatters($this->dictionariesID);
		if(!is_null($this->systemID))
			$this->system=System::model()->findByPk($this->systemID);
		else
			$this->system=NULL;
		
	}
	
	private function handleOutputType() {
		if($this->outputType==='download') {
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="export.html"');
		}
	}
	
	private function outputHeader() {
		//note we cannot use render() because the output files might be large (and with render() we could run out of memory)
		//so instead we use renderPartial several times
	
		$data=array('charset'=>$this->encoding, 'colors'=>$this->colors, 'prependText'=>$this->prependText);

		$this->handleOutputType();
		
		if(!empty($this->parallel)) {
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/colResizable-1.3.min.js');
			Yii::app()->clientScript->registerScript('resizableCol','$("table.parallel").colResizable({gripInnerHtml:"<div class=\'grip\'></div>"});');
		}
		
		$this->parent->renderPartial($this->template.'/header', $data, false, true) ;
	}
	
	/**
	 * 
	 * @param string $char
	 * @param DictEntryChar $translations
	 * @param Char $mnemos
	 * @param DictEntryPhrase $phrases
	 * @param int $index
	 * @param string $templateFull
	 */
	private function outputChar($char, $translations, $mnemos, $phrases, $index, $templateFull) {
		$data=array(
				'char'=>$char,
				'translations'=>$translations,
				'mnemos'=>$mnemos,
				'phrases'=>$phrases,
				'index'=>$index,
				'characterMode'=>$this->characterMode,
				'transcriptionFormatters'=>$this->transcriptionFormatters
		);
		$this->parent->renderPartial($templateFull, $data) ;
	}
	
	private function outputAudioPlayer() {
		if(empty($this->audioURL))
			return;
		
		$this->parent->widget('ext.jouele.Jouele', array(
				'file' => $this->audioURL,
				'name' => '三字经',
				'htmlOptions' => array(
						'class' => 'jouele-skin-silver',
				)
		));
	}
	
	private function goTemplates() {
		$templateCount=$this->detectTemplateCount($this->template);
		
		if($templateCount==="DUMP") { //if no templates are set, just dump the whole input as-is
			//echo $this->input;
			
			//@TODO move this processing to a method in the "dumpoutput.php"
			if(!$this->whitespaceToHTML)
				echo $this->input;
			else
				echo implode('</div><br><div class="x">', split("(\r\n){1,}", $this->input));
			
// 			echo implode('</div><br><div class="x">', split("\r\n\r\n", $this->input));
// 			echo implode('</div><div class="x">', split("(\r\n){1,}", $this->input));
			
			//str_split($this->input);
			//but convert the newlines to line breaks
// 			echo str_replace("\r\n\r\n", "<br>", $this->input);
// 			echo str_replace("\n\n", "<p>", $this->input);
			return;
		}
		
		//if $templateCount is false, run the following loop once
		//else ($templateCount is number), run it $templateCount times
		for($iTemplate=1;$templateCount===FALSE || $iTemplate<$templateCount+1;$iTemplate++) {
				
			if($templateCount===FALSE)
				$templateFull=$this->template.'/perchar';
			else
				$templateFull=$this->template.'/perchar'.$iTemplate."of$templateCount";
		
			$lineIndex=0;
				
			//loop for every character
			for($i=0; $i<$this->len; $i++) {
				$char=mb_substr($this->input, $i, 1, $this->encoding);
		
				if($this->checkNewline($char)) {
					//if the line ended, we need to output another line of parallel text if present
					$this->outputParallelAfterLine($lineIndex, $iTemplate);
					$lineIndex++;
					continue;
				}
		
				if($this->isIgnoredChar($char)) {
					echo $char;
					continue;
				}
		
				$translations=$this->loadTranslations($char);
				$mnemos=$this->loadMnemonics($char);
				$phrases=$this->loadPhrases($char, $i);
		
				$this->outputChar($char, $translations, $mnemos, $phrases, $i, $templateFull);
			} //end of character loop
		
			if($templateCount===FALSE)
				break;
		}
		
		
	}
	
	private function outputFooter() {
		
		$this->parent->renderPartial($this->template."/footer", array(
				 'systemID'=>$this->systemID,
				 'dictionariesID'=>$this->dictionariesID
		), false, true) ;
	}
	
	public static function loadTranslationsFromDictionaries($char, $dictionariesID, $characterMode) {
		$criteria=new CDbCriteria();

		if($characterMode!=self::CHARMOD_TRADITIONAL_ONLY)
			$criteria->compare('simplified', $char);
		if($characterMode!=self::CHARMOD_SIMPLIFIED_ONLY)
			$criteria->compare('traditional', $char, false, 'OR');
		//in other cases than CHARMOD_SIMPLIFIED_ONLY and CHARMOD_TRADITIONAL_ONLY both are searched
		
		$criteria->addInCondition('dictionaryId', $dictionariesID);
		return DictEntryChar::model()->findAll($criteria);
	}
	
	private function loadTranslations($char) {
// 		return AnnotatorEngine::loadTranslationsFromDictionaries($char, $this->dictionariesID);
 		return AnnotatorEngine::loadTranslationsFromDictionaries($char, $this->dictionariesID, $this->characterMode);
	}

	
	public static function loadMnemonicsForSystem($char, $system) {
		if(empty($system))
			return NULL;
	
		$criteria=new CDbCriteria();
		$criteria->compare('chardef', $char);
		$criteria->addInCondition('system', $system->allInheritedIds);
		// 		$criteria->order='id'; //@TODO prefer the topmost system (this is only a hack, not very robust)
		return Char::model()->find($criteria);
	}
	
	/**
	 * 
	 * @param string $char
	 * @return Char
	 * 		the corresponding char for the given string 
	 */
	private function loadMnemonics($char) {
		return AnnotatorEngine::loadMnemonicsForSystem($char, $this->system);
	}
	
	public static function loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, $characterMode) {
		if(empty($compounds))
			return null;

		$criteria=new CDbCriteria();
		
		foreach($compounds as $search) {
			if($characterMode!=self::CHARMOD_SIMPLIFIED_ONLY) //in all other modes have to search both
				$criteria->compare('traditional_rest', $search, false, 'OR');
			if($characterMode!=self::CHARMOD_TRADITIONAL_ONLY)
				$criteria->compare('simplified_rest', $search, false, 'OR');
		}
		
		//note by adding the other conditions after the _rest column search makes the OR and AND in the correct brackets
		$criteria->addInCondition('dictionaryId', $dictionariesID); //it has to be in one of the chosen dictionaries
		
		//@TODO the condition is not escaped (so it would fail if $char would be an apostrophe)
		//the first letter (because of the DB structure)
		if($characterMode==self::CHARMOD_SIMPLIFIED_ONLY)
			$criteria->addCondition("simplified_begin='$char'");
		if($characterMode==self::CHARMOD_TRADITIONAL_ONLY)
			$criteria->addCondition("traditional_begin='$char'");
		else
			$criteria->addCondition("simplified_begin='$char' OR traditional_begin='$char'");
		
		$results=array();
		$results=DictEntryPhrase::model()->findAll($criteria);
		
		//sort to put the longest first
		usort($results,function($a,$b) {
			$encoding=Yii::app()->params['annotatorEncoding'];
			return mb_strlen($b->traditional_rest, $encoding)- mb_strlen($a->traditional_rest, $encoding);
		} );
		
			return $results;	
	}
	
	
	/**
	 * Loads the phrases based on a point in the text.
	 * 
	 * @param String $char  
	 * 					the first char of the phrase (the char being pointed at)
	 * @param Integer $offset
	 * 					the offset in the text 
	 * @return DictEntryPhrase[]
	 */
	private function loadPhrases($char, $offset) {
		$limit=Yii::app()->params['staticAnnotatorCompositionLengthLimit'];

		//get the characters following the current one, in order to search for the phrases
		//step through the text we are annotating to reach first boundary character or the limit length of a composition
		$search='';
		$compounds=array();
		$j=1;
		for(;$j<=$limit;$j++) {
			//stop at ignored char
			$sub=mb_substr($this->input, $offset+$j, 1, $this->encoding);
			if(empty($sub) || $this->isIgnoredChar($sub)) {
				$j--;
				break;
			}
							
			$search.=$sub;
			$compounds[]=$search;
// 			$criteria->compare('traditional_rest', $search, false, 'OR');
// 			$criteria->compare('simplified_rest', $search, false, 'OR');
// 			$criteria->addCondition("$restColumnName='$search'", 'OR');
		}
		return self::loadPhrasesFromDictionaries($char, $compounds, $this->dictionariesID, $this->characterMode);
	}
	
	/**
	 *
	 * @param array $dictionariesID
	 * @return array (dictionaryID => Formatter)
	 */
	public static function createFormatters($dictionariesID) {
		$result=array();
	
		$criteria=new CDbCriteria();
		$criteria->addInCondition('id', $dictionariesID);
		$dicts=Dictionary::model()->findAll($criteria);
	
		foreach($dicts as $dict) {
			$result[$dict->id]=FormattersFactory::getFormatterForDictionaryWidget($dict->transcriptionName, PINYIN_FORMAT_MARKS);
		}
	
		return $result;
	}
	
	private function detectTemplateCount($template) {
		if(($viewFile=$this->parent->getViewFile("$template/dumpoutput"))!==false) {
			return "DUMP";
		}
		
		if(($viewFile=$this->parent->getViewFile("$template/perchar"))!==false) {
			return FALSE;
		} else {
			for($i=0;$i<Yii::app()->params['maxTemplateParts'];$i++) {
				if(($viewFile=$this->parent->getViewFile("$template/perchar1of$i"))!==false) {
					return $i;
				}
			}
		}
		throw new CException("Invalid template: $template");
	}
	

	
	/**
	 *
	 * @param string $char the char to check
	 * @return boolean  true if the char is a newline (that was handled properly)
	 */
	 private function checkNewline($char) {
	 if($char=="\n") { //@TODO what about mac endings?
	 if($this->whitespaceToHTML)
	 	echo "<br>";
	 	else
	 		echo "\n";
	 	return true;
	 }
	 return false;
	 }
	
	 /**
	  * 
	  * @param int $lineIndex which line is being outputed
	  * @param int $templateIteration one-based index of the current template
	  */
	 private function outputParallelAfterLine($lineIndex, $templateIteration) {
	 	if(empty($this->parallel))
	 		return;
	 	
	 	if($templateIteration>1)
	 		return;
	 	
	 	//@TODO push down to template
	 	echo '</td>';
	 	echo '<td>';
	 	if(isset($this->parallelLines[$lineIndex])) {
	 		echo $this->parallelLines[$lineIndex];
	 	}
	 	echo '</td>';
	 	echo '</tr>';
	 	echo '<tr>';
	 	echo '<td>';
	 }
	 
	 private function outputParallelBeforeChars() {
	 	if(empty($this->parallel)) 
	 		return;	 		

   	 	//@TODO push down to template
	 	echo '<table class="parallel"><tr><td>';
	 	$this->parallelLines=split("\n", $this->parallel);
	 }
	 
	 private function outputParallelAfterChars() {
	 	if(empty($this->parallel))
	 		return;

	 	echo '</td><td><p>';
	 	echo '</p></td></tr></table>';
	 }
	
	/**
	 *
	 * @param String $char        	
	 * @return boolean
	 */
	private function isIgnoredChar($char) {
		// (maybe should be replaced by testing unicode ranges for hanzi - to ignore alphanumeric chars
		return strpos ( self::ignoredChars, $char ) !== FALSE;
	}
	
}