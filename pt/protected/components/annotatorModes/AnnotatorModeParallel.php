<?php
class AnnotatorModeParallel extends AnnotatorMode  {
	function allowParallel() { return true; }
	function allowDownload() { return false; }
	
	function getTemplateID() {
		return 2;//dynamic
	}
	
	function getDescription() {
		return "Shows a second column with a translation and/or a playable audio file.";
	}
	
}