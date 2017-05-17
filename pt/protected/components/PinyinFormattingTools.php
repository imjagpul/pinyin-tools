<?php
//Regexp pattern matching a single pinyin syllabe
//define('PINYIN_PATTERN', '/^\s*(\w{1,6})([0-5]?)\s*$/');
define('PINYIN_PATTERN', '/^\s*([a-zA-Z]{1,6})([0-5]?)\s*$/i');
/*
 //already defined in superclass
define('PINYIN_FORMAT_NUMBERS', 1);
define('PINYIN_FORMAT_MARKS', 2);
define('PINYIN_FORMAT_NO_TONES', 3);
define('PINYIN_FORMAT_TONE_ONLY', 4); //only the number
define('PINYIN_FORMAT_MARKS_HTML_COLORS', 5);
*/

define('GLIDES', 'iu');
define('ACCENT_CANDIDATES', "aeiouvAEIOU");
// define('PINYIN_VOWEL', '/^\w{0,2}['.GLIDES.']?(['.ACCENT_CANDIDATES.'])['.ACCENT_CANDIDATES.']?n?g?r?|r?$/');
define('PINYIN_VOWEL', '/^[^'.ACCENT_CANDIDATES.']{0,2}['.GLIDES.']?(['.ACCENT_CANDIDATES.'])['.ACCENT_CANDIDATES.']?n?g?r?|r?$/');

class PinyinFormattingTools extends Formatter
{
	private $desiredFormat; 
	private $colors;
	
	/**
	 * 
	 * @param enum $desiredFormat
	 * @param array $colors
	 * 		If set, the output will be wrapped in <div>, styling every syllabe
	 *      with corresponding tone color.
	 */
	function __construct($desiredFormat, $colors=NULL) {
		$this->desiredFormat=$desiredFormat;
		$this->colors=$colors;
	}
	
	/**
	 *  
	 * @param string $pinyinText
	 * 		A string, containing pinyin syllabe(s), optionally ending with the tone number.
	 * @return
	 *      The pinyin formatted as desired. 
	 */
	function format($pinyinText) {
		if(empty($pinyinText))
			return '';
		
		$accentReplacements=array(
		"aeiouvAEIOU",
		"āēīōūvĀĒĪŌŪ",
		"áéíóúǘÁÉÍÓÚ",
		"ǎěǐǒǔǚǍĚǏǑǓ",
		"àèìòùǜÀÈÌÒÙ",
		"aeiouvAEIOU");
		$encoding='utf-8';
		
		// $pinyinText
		$syllabes=explode(' ', $pinyinText);
		if(count($syllabes)<1)
			return '';
		$matches=NULL;			

		$returnValue=array();
		
		foreach ($syllabes as $s) {
			$s=str_replace("u:", "v", $s);
			
			$success=preg_match(PINYIN_PATTERN, $s, $matches);
			if($success==0) {
				if($s!=',' && $s!='·')
					$this->warning("Recognizing '$s' as pinyin failed (whole pinyinText:'$pinyinText').");
				$returnValue[]=$s; //just return it as it is
				continue;
			}
			
			$pinyin=$matches[1];
			$tone=$matches[2];
			$toneNumeric=is_numeric($tone);
			
			if($this->desiredFormat==PINYIN_FORMAT_NUMBERS)
				$pinyin.=$tone;
			else if($this->desiredFormat==PINYIN_FORMAT_TONE_ONLY)
				$pinyin=$tone;
			else if($this->desiredFormat==PINYIN_FORMAT_MARKS || $this->desiredFormat==PINYIN_FORMAT_MARKS_HTML_COLORS) {
				if($toneNumeric && $pinyin!="r") {
					//find the appropiate vowel
					$result=preg_match(PINYIN_VOWEL, $pinyin, $matches, PREG_OFFSET_CAPTURE);
					if(!$result) {
						$this->warning("pinyin not matched: '$pinyin'");
						$returnValue[]=$s; //just return it as it is
						continue;
					}
					
					if(count($matches)<2) {
						$this->warning("no pinyin candidates matched: '$pinyin'\n");
						$returnValue[]=$s; //just return it as it is
						continue;
					}
					
					//and replace it with the accented character
					$vowelPos=strpos(ACCENT_CANDIDATES, $matches[1][0]);
					if($vowelPos!==FALSE) {
						$resultingChar=mb_substr($accentReplacements[$tone], $vowelPos, 1, $encoding);
						$pinyin=substr($pinyin, 0,$matches[1][1]).$resultingChar.substr($pinyin, $matches[1][1]+1);
					}  //else : no vowel (i.e. "r5" in erhua) - nothing to do
					
					if($this->desiredFormat==PINYIN_FORMAT_MARKS_HTML_COLORS) {
						$pinyin='<span class="dicttone'.$tone.'">'.$pinyin.'</span>';
					}
				}
			}
			
			//if(isset($this->colors)) {
					//TODO implement maybe (or maybe it's not neccessary)
			//}

			$returnValue[]=$pinyin;
		}
		return implode(' ', $returnValue);		
	}
	
	public function warning($msg) {
		Yii::log($msg, 'error', 'PinyinFormattingTools');
	} 
	
}