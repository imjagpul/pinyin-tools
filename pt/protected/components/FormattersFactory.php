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
// 	private $_mode;

	public static function getFormatterForDictionaryWidget($transcription, $mode=PINYIN_FORMAT_MARKS_HTML_COLORS) {
		if(isset(self::$cache[$transcription]))
				return self::$cache[$transcription];

		if($transcription=="Pinyin") {
			self::$cache[$transcription]=new PinyinFormattingTools($mode);
		} else if($transcription=='meaning' || $transcription=='pronunciation' || $transcription=='both') {
			self::$cache[$transcription]=new MatthewsFormatter();
		} else {
			self::$cache[$transcription]=new DummyFormatter();
		}
		
		return self::$cache[$transcription];
	} 
}
