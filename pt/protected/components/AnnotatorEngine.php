<?php
class AnnotatorEngine {
	const ignoredCharsJs=" \\\",.;'!。，《》…：“”？！　0"; //withonut newlines and with escaped quote
	const ignoredChars=" \n\t\r\",.;'!。，《》…：“”？！　0"; // @TODO complement special asian characters
		
	public $parent;
	public $input;
	public $systemID;
	public $dictionariesID;
	public $simplified=true;
	public $template="jsbased";
	public $whitespaceToHTML=true; 
	public $parallel;
	public $audioURL;
	public $outputType;
	
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
	
		$data=array('charset'=>$this->encoding, 'colors'=>$this->colors, false, true);

		$this->handleOutputType();
		
		if(!empty($this->parallel)) {
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/colResizable-1.3.min.js');
			Yii::app()->clientScript->registerScript('resizableCol','$("table.parallel").colResizable({gripInnerHtml:"<div class=\'grip\'></div>"});');
		}
		
		$this->parent->renderPartial($this->template.'/header', $data) ;
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
				'simplified'=>$this->simplified,
				'transcriptionFormatters'=>$this->transcriptionFormatters
		);
		$this->parent->renderPartial($templateFull, $data) ;
	}
	
	private function outputAudioPlayer() {
		if(empty($this->audioURL))
			return;
		
		$this->parent->widget('ext.jouele.Jouele', array(
				'file' => $this->audioURL,
				'name' => 'ä¸‰ĺ­—ç»Ź',
				'htmlOptions' => array(
						'class' => 'jouele-skin-silver',
				)
		));
	}
	
	private function goTemplates() {
		$templateCount=$this->detectTemplateCount($this->template);
		
		if($templateCount==="DUMP") { //if no templates are set, just dump the whole input as-is
			echo $this->input;
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
	
	public static function loadTranslationsFromDictionaries($char, $dictionariesID, $simplified) {
		$criteria=new CDbCriteria();
		$criteria->compare($simplified ? 'simplified' : 'traditional', $char);
		$criteria->addInCondition('dictionaryId', $dictionariesID);
		return DictEntryChar::model()->findAll($criteria);
	}
	
	private function loadTranslations($char) {
		return AnnotatorEngine::loadTranslationsFromDictionaries($char, $this->dictionariesID, $this->simplified);
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
	
	public static function loadPhrasesFromDictionaries($char, $compounds, $dictionariesID, $simplified) {
		if(empty($compounds))
			return null;
		
		$beginColumnName=$simplified ? 'simplified_begin' : 'traditional_begin';
		$restColumnName=$simplified ? 'simplified_rest' : 'traditional_rest';
		
		$criteria=new CDbCriteria();
		
		foreach($compounds as $c) {
			$criteria->compare($restColumnName, $c, false, 'OR');
		}
		
		//note by adding the other conditions after the _rest column search makes the OR and AND in the correct brackets
		$criteria->addInCondition('dictionaryId', $dictionariesID); //it has to be in one of the chosen dictionaries
		
		
		//@TODO the condition is not escaped (so it would fail if $char would be an apostrophe)
		$criteria->addCondition("$beginColumnName='$char'");//the first letter (because of the DB structure)
		
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
	 *
	 * @param String $char
	 * @param int[] $dictionaries
	 * @param boolean $simplified
	 * @param String $input
	 * @param int $i
	 * @return DictEntryPhrase[]
	 */
	private function loadPhrasesRegex($char, $offset) {
		//@TODO move this from this controller to them odel
		$limit=Yii::app()->params['staticAnnotatorCompositionLengthLimit'];
	
		$regex=NULL;
	
		$j=1;
		for(;$j<=$limit;$j++) {
			//stop at ignored char
			$sub=mb_substr($this->input, $offset+$j, 1, $this->encoding);
			if(empty($sub) || $this->isIgnoredChar($sub)) {
				$j--;
				break;
			}
				
			if(is_null($regex)) { //first char must be there
				$regex=$sub;
				continue;
			}
			//abcd
			//a|ab|abc|abcd
			//a(b(c(d)?)?)?
				
			$regex="$regex($sub";
		}
		if($j==0) return array();
		$simpleSearch = $j==1;
		for(;$j>1;$j--) {
			$regex.=")?";
		}

		$restColumnName=$this->simplified ? 'simplified_rest' : 'traditional_rest';
	
		$criteria=new CDbCriteria();
		$criteria->addInCondition('dictionaryId', $this->dictionariesID);
		$criteria->compare($this->simplified ? 'simplified_begin' : 'traditional_begin', $char);
		if($simpleSearch) {
			$criteria->compare($restColumnName, $regex);
		} else {
			$criteria->condition.=" AND $restColumnName REGEXP '^$regex\$'"; //@TODO investigate if it's 1. supported 2. optimal
		}

		// 		return array(DictEntryPhrase::model()->find($criteria), DictEntryPhrase::model()->find($criteria));
		var_dump($criteria);//die;
		$result=DictEntryPhrase::model()->findAll($criteria);
		var_dump($result);die;
		return DictEntryPhrase::model()->findAll($criteria);
	
	}
	/**
	 *
	 * @param String $char
	 * @param int[] $dictionaries
	 * @param boolean $simplified
	 * @param String $input
	 * @param int $i
	 * @return DictEntryPhrase[]
	 */
	private function loadPhrases($char, $offset) {
		//@TODO (maybe) move this from this controller to them odel
		
		//abcd
		//a|ab|abc|abcd
		//a(b(c(d)?)?)?
		
		$limit=Yii::app()->params['staticAnnotatorCompositionLengthLimit'];
	
		$search='';

		$beginColumnName=$this->simplified ? 'simplified_begin' : 'traditional_begin';
		$restColumnName=$this->simplified ? 'simplified_rest' : 'traditional_rest';
		
		$criteria=new CDbCriteria();
		
		$j=1;
		for(;$j<=$limit;$j++) {
			//stop at ignored char
			$sub=mb_substr($this->input, $offset+$j, 1, $this->encoding);
			if(empty($sub) || $this->isIgnoredChar($sub)) {
				$j--;
				break;
			}
							
			$search.=$sub;
			$criteria->compare($restColumnName, $search, false, 'OR');
// 			$criteria->addCondition("$restColumnName='$search'", 'OR');
		}
		if($j==0) return array();

		//note by adding the other conditions after the _rest column search makes the OR and AND in the correct brackets

		$criteria->addInCondition('dictionaryId', $this->dictionariesID); //it has to be in one of the chosen dictionaries
		//@TODO the condition is not escaped (so it would fail if $char would be an apostrophe)
		$criteria->addCondition("$beginColumnName='$char'");//the first letter (because of the DB structure)
		
		$results=array();
		$results=DictEntryPhrase::model()->findAll($criteria);
		
		//sort to put the longest first
		usort($results,function($a,$b) {
			return mb_strlen($b->traditional_rest, $this->encoding)- mb_strlen($a->traditional_rest, $this->encoding);
		} );
		
		return $results;
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