<b>What's this about:</b>
<ul id="navmainlist">
<?php 
$activeItem=isset($this->owner->actionParams['view'])?$this->owner->actionParams['view']:"";

$entries=array(
		array('Extensive reading method', 'extensiveReading'),
		array('Practical tips', 'practicalTips'),
		array('Chinese characters', 'chineseCharacters'),
		array('Annotator helps', 'annotatorHelps')
);

foreach($entries as $e) {
	$htmlOptions=array();
	if($activeItem==$e[1]) {
		$htmlOptions['class']='activeMainMenuItem';
	}
	
	echo '<li>'.CHtml::link($e[0], array('site/page/view/'.$e[1]), $htmlOptions).'</li>';
}

?>
</ul>
<?php /* 
<b>Usage:</b>
<ul id="navmainlist">
</ul>

<b>Appendix - memory tips:</b>

*/ ?>