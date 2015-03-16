<?php
class AnnotatorModeEpub extends AnnotatorMode  {
	function allowView() { return false; }
	function allowDownload() { return true; }
	function getOutputProcessor() { return null; }
	
	function getTemplateID() {
		return 1;//'kindle'
	}

	function getDescription() {
		return "You will get a EPUB file with translations and mnemonics, suitable for various eReaders.";
	}
	
}