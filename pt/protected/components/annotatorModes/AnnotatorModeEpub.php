<?php
class AnnotatorModeEpub extends AnnotatorMode  {
	function allowView() { return false; }
	function allowDownload() { return true; }
	function getOutputProcessor() { return null; }
	
	function getTemplateID() {
		return 1;//'kindle'
	}

	function getDescription() {
// 		return "You will get a HTML, EPUB or MOBI file with translations and mnemonics, suitable for various eReaders.";
		return "You will get a HTML file with translations and mnemonics, suitable for various eReaders. The result can be converted to MOBI and EPUB easily.";
	}
	
}