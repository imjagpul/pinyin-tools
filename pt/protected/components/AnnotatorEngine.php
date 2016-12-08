<?php
class AnnotatorEngine {
	const ignoredCharsJs=" \\\"</>,.;'!。，《》…：“”？！　0"; //withonut newlines and with escaped quote
	const ignoredChars=" \n\t\r\"</>,.;'!。，《》…：“”？！　0"; // @TODO complement special asian characters
	const ignoredCharsMultibyte="。，《》…：“”？！　"; // ignored chars that are multibyte

	const CHARMOD_SIMPLIFIED_ONLY=1;
	const CHARMOD_TRADITIONAL_ONLY=2;
	const CHARMOD_CONVERT_TO_SIMPLIFIED=3;
	const CHARMOD_CONVERT_TO_TRADITIONAL=4;
	const CHARMOD_ALLOW_BOTH_PREFER_SIMP=5;
	const CHARMOD_ALLOW_BOTH=5;
	//const CHARMOD_ALLOW_BOTH=5;

	/** @var CController */
	public $parent;
	public $input;
	public $systemID;
	private $dictionariesID;
 	public $dictID;
	public $characterMode;
// 	/** @var Boolean If FALSE, the characters will link directly to the characters dictionary. If TRUE, the links will go to to the corresponding entry in the words dictionary. */
// 	public $wordsDictionary=true;

	/** @var AnnotatorMode */
	public $mode;
	public $whitespaceToHTML=true; 
	public $parallel;
	public $audioURL;
	
// 	/** @var String If the output is intended for downloading or for viewing (affects the mimetype). */
// 	public $outputType;
	/** @var Integer If the output is intended for downloading or for viewing (affects the mimetype). One of AnnotatorMode constants. */
	public $outputMode;
	/** @var String Text to be included as is at the beginning of the output. Useful for the demo page. */
	public $prependText=NULL;
	/** @var integer The starting index of the current chunk in the whole input. */
	public $startingIndex=0;
	
	private $encoding;
	private $colors;
	private $system;
	private $transcriptionFormatters;
	private $parallelLines;
	private $len;
	private $template;
	private $templateFull=NULL;
	private $templateFull2=NULL;

	/**
	 *
	 * @throws CException
	 */
// 	public function annotate2() {
// 		$dictID=$this->dictID;
		
// 		$startTime=time();
// 		$this->prepare();
// 		$this->outputHeader();
// 		$this->go();
// 		if($this->wordsDictionary)
// 			$this->outputDictionary($this->parent, false, $dictID);
// 		$this->outputDictionary($this->parent, true, $dictID);
// 		$this->outputFooter();
		
// 		$totalTime=time()-$startTime;
// 		echo "<!-- took $totalTime s -->";
// 	}
	
	/**
	 * Annotates the input without adding header and footer, and returns the output.
	 * 
	 * @return string[] an array of two elements, the first being the output, the second being the other part of the output in two-part templates.
	 */
	public function annotateChunk() {
		$this->prepare();
//		ob_start();
		return $this->go();
//		return ob_get_clean();
	}
	
	/**
	 * Converts an dictionary entry text to an anchor name.
	 * @param String $text
	 * 			the text of the entry to be linked
	 * @return String
	 * 			what name should be used in the href/name of the "a" HTML tag
	 */
	public static function textToLink($text) {
		return $text;
	}
	
	/**
	 * 
	 * @return string[] an array of two elements, the first being the output, the second being the other part of the output in two-part templates.
	 */
	private function go() {
		$result='';
		$result2='';
		
		//loop for every character
		for($i=0; $i<$this->len; $i++) {
			$char=mb_substr($this->input, $i, 1, $this->encoding);
		
			//handle newlines
			if($this->checkNewline($char)) {
				
				if($this->whitespaceToHTML)
					$result.='<br>';
				else
					$result.= "\n";
				
				//if the line ended, we need to output another line of parallel text if present
// 				$this->outputParallelAfterLine($lineIndex, $iTemplate);
				continue;
			}
		
			if($this->isIgnoredChar($char)) { //ignored chars are simply appended to the first output
				$result.=$char;
				continue;
			}
		
			$translations=$this->loadTranslations($char);
			$mnemos=$this->loadMnemonics($char);
			$phrases=$this->loadPhrases($char, $i);
			
			$result.=$this->outputChar($char, $translations, $mnemos, $phrases, $this->startingIndex+$i, $this->templateFull, true);
			if(!is_null($this->templateFull2)) { 
				$result2.=$this->outputChar($char, $translations, $mnemos, $phrases, $this->startingIndex+$i, $this->templateFull2, true);
			}
			
// 			$charData = self::loadDictChar($char, $this->characterMode);
// 			if($charData==null) { //if not in the dictionary, treat as ignored
// 				//@TODO this is not optimal, it would not output mnemonics on rare characters (esp. when using smaller dictionaries)
// 				echo $char;
// 				continue;
// 			}
			
							
			
// 			if($this->wordsDictionary) {
// 				//@TODO this is the place where the simplified <-> traditional conversion can be implemented (just replace $char with the desired variant from the dictionary)
				
// 				//dictionary search if the phrases dictionary is included
// 				$phraseEntry = $this->loadLongestPhrase($char, $i);
				
// 				if(is_null($phraseEntry))
// 					$phrase=$charData->traditional; //if no phrase found, just link to the characters dictionary (but we always need the traditional variant for the link)
// 				else
// 					$phrase=$phraseEntry->getTraditional();
				
// 				$data=array('char'=>$char, 'link'=>self::textToLink($phrase));
				
// 				$this->parent->renderPartial('core/percharSingleFileWords', $data) ;				
				
// 			} else {
// 				//if not phrases dictionary is included, just output the characters
// 				$data=array('char'=>$char, 'link'=>self::textToLink($charData->traditional));
// 				$this->parent->renderPartial('core/percharSingleFile', $data) ;				
// 			}
		} //end of character loop
		
		return array($result, $result2);
	}

	public function finalOutputAnnotate() {
		
		$this->prepare();
		$this->outputHeader();
		$this->outputAudioPlayer();
		$this->outputParallelBeforeChars();
// 		$this->preprocessInput();
// 		$this->goTemplates();
		echo $this->input;
		$this->outputParallelAfterChars();
		
		//now output the dictionaries
// 		DictionaryCacheWorker::outputDictionary(true, $this->dictID, $this->systemID);
// 		DictionaryCacheWorker::outputDictionary(false, $this->dictID, $this->systemID);
		
		$this->outputFooter();
		
// 		echo "<!-- took ". (microtime(true)-YII_BEGIN_TIME)." s -->";
	}
	
	public function annotate() {
		$startTime=time();
		
		$this->prepare();
		$this->outputHeader();
		$this->outputAudioPlayer();
		$this->outputParallelBeforeChars();
		$this->preprocessInput();
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
		$this->colors=UserSettings::getCurrentSettings()->annotatorColors;
		$this->len=mb_strlen($this->input,$this->encoding);
		$this->dictionariesID=array($this->dictID);
		
		if($this->len==0) {
			//no input given - even though that should be checked already
			throw new CException("No input given.");
		}
		
		$this->transcriptionFormatters=$this->createFormatters($this->dictionariesID);
		
		if(!is_null($this->systemID))
			$this->system=System::model()->findByPk($this->systemID);
		else
			$this->system=NULL;
		
			
		$this->template=$this->mode->getTemplatePath();
		if($this->mode->getTemplateCount()==1)
			$this->templateFull=$this->template.'/perchar';
		else if($this->mode->getTemplateCount()==2) {
			$this->templateFull=$this->template.'/perchar1of2';
			$this->templateFull2=$this->template.'/perchar2of2';
		} else {
			throw new Exception("Invalid subtemplates count!");
		}
			
	}
	
	/**
	 * Sends the headers corresponding to the requested output mode.
	 */
	public function handleOutputMode() {
		if($this->outputMode==AnnotatorMode::MODE_DOWNLOAD) {
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="export.html"');
		} else if($this->outputMode==AnnotatorMode::MODE_DOWNLOAD_EPUB) {
			header('Content-type: application/epub+zip');
			header('Content-Disposition: attachment; filename="export.epub"');
		}
	}
	
	private function outputHeader() {
		//note we cannot use render() because the output files might be large (and with render() we could run out of memory)
		//so instead we use renderPartial several times
	
		$data=array('charset'=>$this->encoding, 'colors'=>$this->colors, 'prependText'=>$this->prependText);

		$this->handleOutputMode();
		
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
	 * @param boolean $return whether the rendering result should be returned instead of being displayed to end users
	 */
	private function outputChar($char, $translations, $mnemos, $phrases, $index, $templateFull, $return=false) {
		$data=array(
				'char'=>$char,
				'translations'=>$translations,
				'mnemos'=>$mnemos,
				'phrases'=>$phrases,
				'index'=>$index,
				'characterMode'=>$this->characterMode,
				'transcriptionFormatters'=>$this->transcriptionFormatters
		);
		return $this->parent->renderPartial($templateFull, $data, $return) ;
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
	
	/**
	 * Rewraps text using \n newlines.
	 * Does not handle HTML encoding (for > < characters).
	 * @param unknown $input
	 * @param string $parallel
	 */
	public static function preprocessInput($input) {
		//An attempt to guess correct places for paragraph endings based on whitespace
		/**
		For each line, two variables are determined:
		@var $len - how long is this line?
		@var $currentIndent how many empty spaces before the current line?
		@var $nextIndent how many empty spaces before the following line?
		
		rules:
		len <12 => newline after //poems, heading, indent based articles
		len >30 => newline after //newline after a long line is probably to be kept
		len >12<30 & next line indent not bigger as current line =>no newline after //probably aligned text
		len >12<30 & next line indent bigger as current line     =>newline after //end of paragraph, because next line is more aligned
		*/
		
		$output='';
		
		$lines=split("(\r\n)|\r|\n", $input);
// 		$lines=split("(\r\n){1,}", $this->input);
		$count=count($lines);
		$lastWrap=false;
		
// 		if(!empty($parallel))
			$wrapText="\n";
// 		else
// 			$wrapText='</div>'."\n".'<div class="x">';
		
		for($i=0;$i<$count-1;$i++) {
			
			$l=$lines[$i]; //current line
			
			if(empty($l)) { //empty line(s) means wrap (but not more than once)
				if(!$lastWrap) {
					$output.=$wrapText;
					$lastWrap=true;
				}
				continue;
			}
			
			$n=$lines[$i+1]; //next line
			
			$len=strlen($l); //length of current line
			$nlen=strlen($n);//length of next line
			
			$currentIndent=0; //how many whitespace characters are at the beginning of current line
			$nextIndent=0; //how many whitespace characters are at the beginning of next line
			
			for(; $currentIndent<$len && ($l[$currentIndent]==' ' || $l[$currentIndent]=="\t"); $currentIndent++);
			for(; $nextIndent<$nlen && ($n[$nextIndent]==' ' || $n[$nextIndent]=="\t"); $nextIndent++);
			
			$output.=$l; //append line to output
// 			$output.=CHtml::encode($l); //append line to output - and HTML encode as well (in case of < >)
			//implement rules as described in the comment above
			if($len>30 && $len<200 && $nextIndent<=$currentIndent) {
				//no newline
				$lastWrap=false;
			} else {
				$lastWrap=true;
				$output.=$wrapText;
			}
		}
		$output.=$lines[$count-1]; //last line
		
		//return the preprocessed input 
		return $output;
// 		$this->len=mb_strlen($this->input,$this->encoding);
// 		implode('</div><br><div class="x">', split("(\r\n){1,}", $this->input));
	}
	
	private function goTemplates() {
		die('deprecated');
// 		$templateCount=$this->detectTemplateCount($this->template);
		
// 		if($templateCount==="DUMP") { //if no templates are set, just dump the whole input as-is
// 			//parallel does not work in quick dump (but that is not a problem) 
// 			echo $this->input;
// 			return;
// 		}
		
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
		
			$this->outputParallelAfterLine($lineIndex, $iTemplate, true);
			
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
		$criteria->limit=20;
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
		$criteria->limit=20;
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
	
// 	private static function loadDictChar($char, $characterMode) {
// 		$criteria=new CDbCriteria();
		
// 		if($characterMode!=self::CHARMOD_SIMPLIFIED_ONLY) //in all other modes have to search both
// 			$criteria->compare('traditional', $char, false, 'OR');
// 		if($characterMode!=self::CHARMOD_TRADITIONAL_ONLY)
// 			$criteria->compare('simplified', $char, false, 'OR');
		
// 		return DictEntryChar::model()->find($criteria);
// 	}
	
// 	private function loadLongestPhrase($char, $offset) {
// 		$phrases=self::loadPhrases($char, $offset);
// 		if(!empty($phrases))
// 			return $phrases[0];
// 		else
// 			return null;
// 	}
	
	/**
	 *
	 * @param integer $dictID
	 * @return Formatter
	 */
	public static function createFormatter($dictID) {
		$result=array();
		$dict=Dictionary::model()->findByAttributes(array('id'=>$dictID));
		
		return FormattersFactory::getFormatterForDictionaryWidget($dict->transcriptionName, PINYIN_FORMAT_MARKS);
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
	
// 	private function detectTemplateCount($template) {
// 		if(($viewFile=$this->parent->getViewFile("$template/dumpoutput"))!==false) {
// 			return "DUMP";
// 		}
		
// 		if(($viewFile=$this->parent->getViewFile("$template/perchar"))!==false) {
// 			return FALSE;
// 		} else {
// 			for($i=0;$i<Yii::app()->params['maxTemplateParts'];$i++) {
// 				if(($viewFile=$this->parent->getViewFile("$template/perchar1of$i"))!==false) {
// 					return $i;
// 				}
// 			}
// 		}
// 		throw new CException("Invalid template: $template");
// 	}
	

	
	/**
	 *
	 * @param string $char the char to check
	 * @return boolean  true if the char is a newline (that was handled properly)
	 */
	 private function checkNewline($char) {
	 	return $char=="\n";
	 }
	
	 /**
	  * 
	  * @param int $lineIndex which line is being outputed
	  * @param int $templateIteration one-based index of the current template
	  * @param boolean $finish	if all following lines should be outputed as well
	  */
	 private function outputParallelAfterLine($lineIndex, $templateIteration, $finish=false) {
	 	if(empty($this->parallel))
	 		return;
	 	
	 	if($templateIteration>1)
	 		return;
	 	
	 	//@TODO push down to template
	 	echo '</td>';
	 	echo '<td>';
	 	if(isset($this->parallelLines[$lineIndex])) {
	 		echo $this->parallelLines[$lineIndex];
	 		
	 		if($finish) {
	 			for($i=$lineIndex+1; $i<count($this->parallelLines); $i++) {
	 				echo "<br>\n";
	 				echo $this->parallelLines[$i];
	 			}
	 		}
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
	public static function isIgnoredChar($char) {
		// (maybe should be replaced by testing unicode ranges for hanzi - to ignore alphanumeric chars
		//this is a dirty hack - unicode characters outside ascii range (like hanzi) - have more bytes in length (contrast with mb_strlen)
		return strlen($char)<2 || (strpos ( self::ignoredCharsMultibyte, $char ) !== FALSE);
// 		return  strpos ( self::ignoredChars, $char ) !== FALSE;
	}
	
// 	private function outputWordDictionary($parent, $dictID) {
		
// 	}
	
	
	
	
}