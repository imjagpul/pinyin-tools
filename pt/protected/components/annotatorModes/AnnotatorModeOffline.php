<?php
class AnnotatorModeOffline extends AnnotatorMode  {
	function allowView() { return false; }
	function allowDownload() { return true; }
	function getOutputProcessor() { return null; }
	
	function getDescription() {
		return "You will get a HTML page suitable for offline usage.";
	}
	
	function getTemplateID() {
		return 0; //'jsbased'
	}
}