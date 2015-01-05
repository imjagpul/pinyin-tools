<?php
class MatthewsFormatter extends Formatter {
	private $replaceVal;
	
	function format($text) {
		if(!isset($this->replaceVal)) {
			$holder=9999;
			$urlVal=Yii::app()->createUrl('char/view',array('id'=>$holder));
			$urlVal=str_replace($holder, "\\1", $urlVal);
			$this->replaceVal='<a class="mnemocomp" href="'.$urlVal.'">\\2</a>';
		}
// 		return $text;
// 		return preg_replace('#\[/c\]#', $this->replaceVal, $text);
		$result=preg_replace('#\[c(\d+)\](.+?)\[/c\]#', $this->replaceVal, $text);
		$result=preg_replace('#\[k\](.+?)\[/k\]#', "<b>\\1</b>", $result);
		$result=preg_replace('#\[a(\d+)\](.+?)\[/a\]#', '<span class="archetype\\1">\\2</span>', $result);
		$result=preg_replace('#\[s(\d+)\](.+?)\[/s\]#', '<span class="tone\\1">\\2</span>', $result);
		
		return $result;
// 		$matches=array();
// 		$n=preg_match_all('#\[c(\d+)\](.+?)\[/c\]#', $text, $matches);
// 		for($i=0; $i<$n; $i++) {
// 			$matches[]
// 		}
// 		return preg_replace('#\[c(\d+)\](.+?)\[/c\]#', '<a href="'.Yii::app()->createUrl('char/view',array('id'=>'$1')).'">\\2</a>', $text);
	}
}