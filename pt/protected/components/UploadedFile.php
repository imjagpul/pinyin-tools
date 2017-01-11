<?php
define('EOL', "\r\n"); //when changing this change the following EOL_LENGTH too 
define('EOL_LENGTH', 2);
define('BUFFER_SIZE', 10000000);
//define('BUFFER_SIZE', 1024);

class UploadedFile {
	//this implementation caches all the content of the file
	//private $f;
	private $contents;
	private $lineIndex;
	function __construct($uploadedFileName=NULL) {
		if($uploadedFileName===NULL) //used to construct blank object for deserialization 
			return; 
		
		$filename=$_FILES[$uploadedFileName]['tmp_name'];
		$this->contents=file($filename, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
		$this->rewindFile();
	}	

	function rewindFile() {
		$this->lineIndex=-1;
	}
	
	function readline() {
		$this->lineIndex++;
		if(count($this->contents)<=$this->lineIndex) return FALSE;
		return $this->contents[$this->lineIndex];
	}
	
	function peekline($stepSize=1) {
		$desiredIndex=$this->lineIndex+$stepSize;
		if(count($this->contents)<=$desiredIndex || $desiredIndex<0) return FALSE;
		return $this->contents[$desiredIndex];
	}
	
// 	function serialize() { //NOTE line index is currently not serialized!
// 		return $this->contents;
// 	}
	
// 	function deserialize($contents) {
// 		$this->contents=$contents;
// 		$this->rewindFile();
// 	}
	
	/*
	function readline() {
		//check that the file is not already finished
		if($this->lastNewlineIndex===FALSE) 
			return FALSE;
		
		$pos=strpos($this->contents, EOL, $this->lastNewlineIndex);
		if($pos!==FALSE) {
			$result=substr($this->buffer, $this->lastNewlineIndex, $pos-$this->lastNewlineIndex);
			$this->lastNewlineIndex=$pos+1;
			return $result;
		} else {
			$result=substr($this->buffer, $this->lastNewlineIndex);
			$this->lastNewlineIndex=FALSE;
			return $result;
		}
	}
*/
	function splitLineBy($separator) {
		$line=$this->readline();
		
		if($line!==FALSE)
			return explode($separator, $line);
		else
			return FALSE;
	}
	
	/**
	 * Deletes the source file from the disk.
	 */
	function unlink() {
		
	}
}



?>
