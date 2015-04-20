<?php
class AnnotatorModeQuick extends AnnotatorMode {
	function allowView() { return true; }
	function allowDownload() { return false; }
	
	function getTemplateID() {
		return 2;//dynamic
	}
}