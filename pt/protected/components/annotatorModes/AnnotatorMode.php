<?php
abstract class AnnotatorMode {
	const MODE_DOWNLOAD=0;
	const MODE_SHOW=1;
	const MODE_DOWNLOAD_EPUB=2;
	
	function allowParallel() { return false; }
	function allowView() { return true; }
	function allowDownload() { return true; }
	
	function getOutputProcessor() { return null; }
	
	function getDescription() {
		return null;
	}
	
	/**
	 * See constants in AnnotatorController.
	 * 0 => 'jsbased' 
	 * 1 => 'kindle'
	 * 2 => 'dynamic'
	 */
	abstract function getTemplateID();
	
	/**
	 * @var String[][] 
	 *    first element is handling class
	 *    second element is human-readable menu label (used in annotator) 	
	 */
	private static $modesList=array(
				array("AnnotatorModeQuick", "Quick"),
				array("AnnotatorModeOffline", "Offline"),
				array("AnnotatorModeParallel", "Parallel"),
				array("AnnotatorModeEpub", "Portable"),
// 				array("AnnotatorModeMobi", "MOBI"),
// 				array("AnnotatorModeUntagged", "Only list untagged")
	);
	
	static function parseMode($modeID) {
		return new self::$modesList[$modeID][0];		
	}
	
	/**
	 * 
	 * @return multitype:array:string
	 */
	static function getModesList() {
		return self::$modesList;
	}
}