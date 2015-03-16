<?php
class TextData {
	private $dir;
	
	public function __construct() {
		$this->dir=Yii::app()->basePath . '/texts/'.'sanzijing'.'/';
	}
	
	public function getTextSimplified() {
		return file_get_contents($this->dir.'sanzijing');
	}
	
	public function getTextTraditional() {
		return file_get_contents($this->dir.'sanzijing_T');
	}
	
	public function getTextParallel() {
		return file_get_contents($this->dir.'parallel');
		
	}
	
	public function getTextAudioPath() {
		//@TODO perhaps we need the URL instead
		$this->dir.'audio/sanzijing_anon.mp3';
	}
	
}
?>