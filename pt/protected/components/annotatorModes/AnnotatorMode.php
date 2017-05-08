<?php
abstract class AnnotatorMode {
	const MODE_DOWNLOAD=0;
	const MODE_SHOW=1;
	const MODE_DOWNLOAD_EPUB=2;
	
	const DUMP="dump";
	
	function allowParallel() { return false; }
	function allowView() { return true; }
	function allowDownload() { return true; }
	
	function getOutputProcessor() { return null; }
	
	function getDescription() {
		return null;
	}
	
	/**
	 * See constants here.
	 * 0 => 'jsbased' 
	 * 1 => 'kindle'
	 * 2 => 'dynamic'
	 */
	protected abstract function getTemplateID();

	/**
	 * Returns the numerical representation of the mode (to be used in the database).
	 */
	public abstract function getID();
	
	/**
	 * @var String[][] 
	 *    first element is handling class
	 *    second element is human-readable menu label (used in annotator) 	
	 */
	private static $modesList=array(
				array("AnnotatorModeQuick", "Quick"),
				array("AnnotatorModeOffline", "Offline"),
				array("AnnotatorModeParallel", "Parallel"),
				array("AnnotatorModePortable", "Portable"),
// 				array("AnnotatorModeEpub", "EPUB"),
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
	
	private static $templatesList=array(
			0 => 'jsbased',
			1 => 'kindle',
			2 => 'dynamic'
	);
	
	public function getTemplatePath() {
		return self::$templatesList[$this->getTemplateID()];
	}
	
	public function getTemplateCount() {
		if($this->getTemplateID()==1) return 2; //hard-coded: kindle - needs dictionary separatedly
		if($this->getTemplateID()==2) return self::DUMP; //hard-coded: dynamic - dumping
		
		return 1;		
	}
	
}