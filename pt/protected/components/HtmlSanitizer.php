<?php
require('htmLawed.php');
class HtmlSanitizer {
	
	private $config=array();
	public function __construct() {
		$this->config["safe"]=1;
// 		$this->config["elements"]="*-table";
		
	}
	
	public function sanitize($htmlSnippet) {
		return htmLawed($htmlSnippet, $this->config);
	}
	
}