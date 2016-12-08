<?php
class AnnotatorModeOffline extends AnnotatorMode  {
	function allowView() { return true; }
	function allowDownload() { return true; }
	function getOutputProcessor() { return null; }
	
	function getDescription() {
		return "The result has the tooltip preloaded (i.e. it is suitable for offline usage but takes slightly longer to generate).";
	}
	
	function getID() {
		return 1;
	}
	
	protected function getTemplateID() {
		return 0; //'jsbased'
	}
}