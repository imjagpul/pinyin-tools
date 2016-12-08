<?php
class AnnotatorModeQuick extends AnnotatorMode {
	function allowView() { return true; }
	function allowDownload() { return false; }
	
	protected function getTemplateID() {
		return 2;//dynamic
	}

	function getID() {
		return 0;
	}
}