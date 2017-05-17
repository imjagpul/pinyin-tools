<?php
class AnnotatorModeParallel extends AnnotatorMode  {
	function allowParallel() { return true; }
	function allowDownload() { return false; }
	
	protected function getTemplateID() {
		return 0; //'jsbased'
	}
	
	function getDescription() {
		return "Shows a second column with a translation and/or a playable audio file.";
	}

	function getID() {
		return 2;
	}
	
}