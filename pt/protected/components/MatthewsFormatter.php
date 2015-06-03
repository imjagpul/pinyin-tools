<?php
class MatthewsFormatter extends Formatter {
	private $replaceVal;
	
	function format($text) {
		if(!isset($this->replaceVal)) {
			//use Yii API to create the correct URL that links char view
			//we create it only once, and only the char ID varies
			
			$holder=9999;
			$urlVal=Yii::app()->createUrl('char/view',array('id'=>$holder));
			$urlVal=str_replace($holder, "\\1", $urlVal);
			$this->replaceVal='<a class="mnemocomp" href="'.$urlVal.'">\\2</a>';
		}

		//simply substitute the "BBcodes" with actual HTML
		$result=preg_replace('#\[c(\d+)\](.+?)\[/c\]#', $this->replaceVal, $text);
		$result=preg_replace('#\[c\](.+?)\[/c\]#', "<b>\\1</b>", $result);
		$result=preg_replace('#\[k\](.+?)\[/k\]#', "<b>\\1</b>", $result);
		$result=preg_replace('#\[m\](.+?)\[/m\]#', "<b>\\1</b>", $result);
		$result=preg_replace('#\[a(\d+)\](.+?)\[/a\]#', '<span class="archetype\\1">\\2</span>', $result);
		$result=preg_replace('#\[s(\d+)\](.+?)\[/s\]#', '<span class="tone\\1">\\2</span>', $result);
		
		return $result;
	}
}