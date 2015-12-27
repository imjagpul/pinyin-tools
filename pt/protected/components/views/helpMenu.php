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

//@TODO main page
echoList("What's this about", array(
		array('Extensive reading method', 'extensiveReading'),
		array('Practical tips', 'practicalTips'),
		array('Chinese characters', 'chineseCharacters'),
		array('Annotator helps', 'annotatorHelps')
));

echoList("Further tips", array(
		array('Use your devices', 'useYourReader'),
		array('Efficent memorization', 'anki'),
		array('Practice writing', ''),
));

echoList("Usage", array(
		array('How to start', ''), //workflow
		array('Systems', 'systemsExplanation'),
		array('Untagged characters', 'untaggedCharacters'),
		array('Colors', ''),
		array('Export', ''),
		array('Transcription systems', ''),
		array('Soundwords index', ''),
));

//Appendix - memory tips
/*
Why mnemonics are powerful 
 -add link to matthews system description and to homepage
Link
Linked list
Peg
Pegged list
Trachtenberg

 */

?>