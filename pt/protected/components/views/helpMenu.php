<?php

$activeItem=isset($this->owner->actionParams['view'])?$this->owner->actionParams['view']:"";

function echoList($title, $entries) {
	global $activeItem;
	
	echo "<b>$title</b><ul id=\"navmainlist\">";
	
	foreach($entries as $e) {
		$htmlOptions=array();
		if($activeItem==$e[1]) {
			$htmlOptions['class']='activeMainMenuItem';
		}
	
		echo '<li>'.CHtml::link($e[0], array('site/page/view/'.$e[1]), $htmlOptions).'</li>';
	}
}

echoList("What's this about", array(
		array('Extensive reading method', 'extensiveReading'),
		array('Practical tips', 'practicalTips'),
		array('Chinese characters', 'chineseCharacters'),
		array('Annotator helps', 'annotatorHelps')
));

echoList("Usage", array(
		array('Systems', 'systemsExplanation'),
));

//Appendix - memory tips
?>