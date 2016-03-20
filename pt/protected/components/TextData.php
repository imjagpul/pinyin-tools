<?php
class TextData {
	private $dir;
	private $audioURL;
	
	public function __construct() {
		$this->dir=Yii::app()->basePath . '/texts/'.'sanzijing'.'/';
		$this->audioURL=Yii::app()->baseUrl. '/audio/';
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
		return $this->audioURL.'sanzijing_anon.mp3';
	}
	
}
?>