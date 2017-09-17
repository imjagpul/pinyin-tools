<?php
class AnnotatorEngine {
	const ignoredCharsJs=" \\\"</>,.;'!。，《》…：“”？！　0（）"; //withonut newlines and with escaped quote
	const ignoredChars=" \n\t\r\"</>,.;'!。，《》…：“”？！　0（）"; // @TODO complement special asian characters
	const ignoredCharsMultibyte="。，《》…：“”？！（）　"; // ignored chars that are multibyte

	/** @var CController */
	public $parent;
	public $input;
	public $systemID;
	private $dictionariesID;
 	public $dictID;

 	/** @var Enum CharacterModeInput  What (if-any) conversions between simplified / traditionals are done with the input.  **/
 	public $characterModeInput;
 	
 	/** @var Enum CharacterModeAnnotations  Which variant is used in displaying the dictionary results. **/
	public $characterModeAnnotations;

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

	public function annotateDirect() {
		$this->finalOutputAnnotatePre();
		list($firstOutput, $secondOutput)=$this->go();
		echo $firstOutput;
		echo $secondOutput;
		$this->finalOutputAnnotatePost();
	}

	/**
	 * Annotates the input without adding header and footer, and returns the output.
	 * 
	 * @return string[] an array of two elements, the first being the output, the second being the other part of the output in two-part templates.
	 */
	public function annotateChunk() {
		$this->prepare();
		return $this->go();
	}
		
	/**
	 * 
	 * @return string[] an array of two elements, the first being the output, the second being the other part of the output in two-part templates.
	 */
	private function go() {
						
	    if($this->templateFull===AnnotatorMode::DUMP) { //if no templates are set, just dump the whole input as-is
			//parallel does not work in quick dump (but that is not a problem)
			return array($this->input, '');
		}
			
		$result='';
		$result2='';
		
		$lineIndex=0;
		
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
				$result.=$this->outputParallelAfterLine($lineIndex);
				$lineIndex++;
				continue;
			}
		
			if($this->isIgnoredChar($char)) { //ignored chars are simply appended to the first output
				$result.=$char;
				continue;
			}
		
			$translations=$this->loadTranslations($char);
			$mnemos=$this->loadMnemonics($char);
			$phrases=$this->loadPhrasesDouble($char, $i);
			
			$result.=$this->outputChar($char, $translations, $mnemos, $phrases, $this->startingIndex+$i, $this->templateFull, true);
			if(!is_null($this->templateFull2)) { 
				$result2.=$this->outputChar($char, $translations, $mnemos, $phrases, $this->startingIndex+$i, $this->templateFull2, true);
			}
			
		} //end of character loop
		
		$result.=$this->outputParallelAfterLine($lineIndex, true);
		
		return array($result, $result2);
	}

	public function finalOutputAnnotatePre() {
		$this->prepare();
		$this->outputHeader();
		$this->outputAudioPlayer();
		$this->outputParallelBeforeChars();
	}
	public function finalOutputAnnotatePost() {
		$this->outputParallelAfterChars();
		$this->outputFooter();
	}
	
	/*
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
	*/
// 	public function annotate() {
// 		$startTime=time();
		
// 		$this->prepare();
// 		$this->outputHeader();
// 		$this->outputAudioPlayer();
// 		$this->outputParallelBeforeChars();
// 		$this->preprocessInput();
// 		$this->goTemplates();
// 		$this->outputParallelAfterChars();
// 		$this->outputFooter();
		
// 		$totalTime=time()-$startTime;
// 		echo "<!-- took $totalTime s -->";
// 	}
/*	
	public static function getBoxData($systemID, $dictionariesID, $char) {
		self::loadTranslationsFromDictionaries($char, $dictionariesID);
		self::loadMnemonicsForSystem($char, $systemID);
		$transcriptionFormatters=self::createFormatters($dictionariesID);
		
	}*/
	
	private function prepare() {
		$this->encoding=Yii::app()->params['annotatorEncoding'];
		$this->colors=UserSettings::getCurrentSettings()->annotatorColors;
		$this->characterModeAnnotations=UserSettings::getCurrentSettings()->characterModeAnnotationsParsed;
		$this->characterModeInput=UserSettings::getCurrentSettings()->characterModeInputParsed;
		$this->len=mb_strlen($this->input,$this->encoding);
		$this->dictionariesID=array($this->dictID);
				
		$this->transcriptionFormatters=$this->createFormatters($this->dictionariesID);
		
		if(!is_null($this->systemID))
			$this->system=System::model()->findByPk($this->systemID);
		else
			$this->system=NULL;
		
			
		$this->template=$this->mode->getTemplatePath();
		if($this->mode->getTemplateCount()===AnnotatorMode::DUMP)
			$this->templateFull=AnnotatorMode::DUMP;
		else if($this->mode->getTemplateCount()==1)
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
	private function handleOutputMode() {
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
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/colResizable-1.6.min.js');
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
				'characterModeAnnotations'=>$this->characterModeAnnotations,
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
	}
		
	private function outputFooter() {
		$this->parent->renderPartial($this->template."/footer", array(
				 'systemID'=>$this->systemID,
				 'dictionariesID'=>$this->dictionariesID
		), false, true) ;
	}
	
	public static function loadTranslationsFromDictionaries($char, $dictionariesID) {
		$criteria=new CDbCriteria();

		//both variants are always searched (note this is imperfect in some cases, when we are only interested in the traditional result and not in the simplified 
		$criteria->compare('simplified', $char);
		$criteria->compare('traditional', $char, false, 'OR');
		
		$criteria->addInCondition('dictionaryId', $dictionariesID);
		$criteria->limit=20;
		return DictEntryChar::model()->findAll($criteria);
	}
	
	private function loadTranslations($char) {
		return AnnotatorEngine::loadTranslationsFromDictionaries($char, $this->dictionariesID);
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
	
	public static function loadPhrasesFromDictionaries($char, $compounds, $dictionariesID) {
		if(empty($compounds))
			return null;

		$criteria=new CDbCriteria();
		
		foreach($compounds as $search) {
			$criteria->compare('traditional_rest', $search, false, 'OR');
			$criteria->compare('simplified_rest', $search, false, 'OR');
		}
		
		//note by adding the other conditions after the _rest column search makes the OR and AND in the correct brackets
		$criteria->addInCondition('dictionaryId', $dictionariesID); //it has to be in one of the chosen dictionaries
		
		//@TODO the condition is not escaped (so it would fail if $char would be an apostrophe)
		//the first letter (because of the DB structure)
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
	
	
	private function loadPhrasesDouble($char, $offset) {
		if(DictEntryPhrase::getFirstPartLength()!=2) die;
		
		$nextChar=mb_substr($this->input, $offset+1, 1, $this->encoding);
		
		if(empty($nextChar) || $this->isIgnoredChar($nextChar))
			return null;
		
		$search=$char.$nextChar;
		
		$criteria=new CDbCriteria();
		
		//note by adding the other conditions after the _rest column search makes the OR and AND in the correct brackets
		$criteria->addInCondition('dictionaryId',  $this->dictionariesID); //it has to be in one of the chosen dictionaries
		
		//@TODO the condition is not escaped (so it would fail if $char would be an apostrophe)
		//the first letter (because of the DB structure)
		$criteria->addCondition("simplified_begin='$search' OR traditional_begin='$search'");
		
		$results=DictEntryPhrase::model()->findAll($criteria);

		$matches=array();
		foreach ($results as $result) {
			//check if any of the phrases match
			$len=mb_strlen($result->traditional_rest, $this->encoding);
			$searched=mb_substr($this->input, $offset+2, $len, $this->encoding);
			
			if($result->simplified_rest==$searched) {
					$matches[]=$result;
					continue; //no need to compare the traditional if the simplifed matches
			}
			
			//no else here - both have to be compared in some modes
			if($result->traditional_rest==$searched) {
					$matches[]=$result;
					continue;
			}
		}
		
		//sort to put the longest first
		usort($matches,function($a,$b) {
			$encoding=Yii::app()->params['annotatorEncoding'];
			return mb_strlen($b->traditional_rest, $encoding)- mb_strlen($a->traditional_rest, $encoding);
		} );
		
		return $matches;
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
		
		$j=DictEntryPhrase::getFirstPartLength();
		
		if($j>1) {
			$char.=mb_substr($this->input, $offset+1, $j-1, $this->encoding);
			$compounds[]='';
		}
		
		//@TODO BUG HERE - if $limit = $j - it does not work
		for(;$j<=$limit;$j++) {
			//stop at ignored char
			$sub=mb_substr($this->input, $offset+$j, 1, $this->encoding);
			if(empty($sub) || $this->isIgnoredChar($sub)) {
				$j--;
				break;
			}
							
			$search.=$sub;
			$compounds[]=$search;
		}
		return self::loadPhrasesFromDictionaries($char, $compounds, $this->dictionariesID);
	}
		
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
	 private function outputParallelAfterLine($lineIndex, $finish=false) {
	 	if(empty($this->parallel))
	 		return;
	 	
	 	$output='';
	 		
	 	//@TODO push down to template
	 	$output.='</td><td>';
	 	if(isset($this->parallelLines[$lineIndex])) {
	 		$output.= $this->parallelLines[$lineIndex];
	 		
	 		if($finish) {
	 			for($i=$lineIndex+1; $i<count($this->parallelLines); $i++) {
	 				$output.="<br>\n";
	 				$output.=$this->parallelLines[$i];
	 			}
	 		}
	 	}
	 	$output.='</td></tr><tr><td>';
	 	return $output;
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