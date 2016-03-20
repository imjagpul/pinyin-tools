<?php
/* @var $this SystemController */
/* @var $system System */
/* @var $data String[] */

echo "<h1>Keywords used in ".$system->name."</h1>";
foreach($data as $title=>$entries) {
		echo $title; 
		echo " => ";
		echo implode(', ', $entries);
		echo "<br>\n";
}

echo CHtml::form();
echo CHtml::submitButton('Commit', array('name'=>'commit'));
echo CHtml::endForm();