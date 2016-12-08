<?php
class AnnotatorModeMobi extends AnnotatorModeEpub {

	function getDescription() {
		return "You will get a MOBI file with translations and mnemonics, suitable for Kindle.";
	}
	
	function getID() {
		return 5;
	}
}