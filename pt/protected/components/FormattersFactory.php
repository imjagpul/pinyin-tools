<?php

//@TODO refactor: change PINYIN_ prefix to TRANSCRIPTION_
//@TODO refactor: change to class constants
define('PINYIN_FORMAT_NUMBERS', 1);
define('PINYIN_FORMAT_MARKS', 2);
define('PINYIN_FORMAT_NO_TONES', 3);
define('PINYIN_FORMAT_TONE_ONLY', 4); //only the number
define('PINYIN_FORMAT_MARKS_HTML_COLORS', 5);

class FormattersFactory {
	private static $cache=array();

	/**
	 * 
	 * @param string $transcription
	 * @param string $mode
	 * @return Formatter
	 */
	public static function getFormatterForDictionaryWidget($transcription, $mode=PINYIN_FORMAT_MARKS_HTML_COLORS) {
// 		$cacheIndex=array($transcription, $mode);
		$cacheIndex=$transcription."MODE:".$mode;
		if(isset(self::$cache[$cacheIndex]))
				return self::$cache[$cacheIndex];

		if($transcription=="Pinyin") {
			self::$cache[$cacheIndex]=new PinyinFormattingTools($mode);
		} else if($transcription=='meaning' || $transcription=='pronunciation' || $transcription=='both') {
			self::$cache[$cacheIndex]=new MatthewsFormatter();
		} else {
			self::$cache[$cacheIndex]=new DummyFormatter();
		}
		
		return self::$cache[$cacheIndex];
	} 
}
