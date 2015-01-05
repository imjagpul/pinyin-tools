<?php
foreach($data as $title=>$entry) {
	echo "<h1>$title</h1>";
	foreach($entry as $char) {
		echo CHtml::link($char->chardef, array("char/update", 'id'=>$char->id));
		$m=$char->mnemo;
		$brPos=strpos($m, "<br", 0);
		$brPosEnd=strpos($m, ">", $brPos)+1;
		echo "<!-- $brPosEnd -->"; 
		echo "<br>\n";
		echo substr($m, $brPosEnd);
		echo "\n";
		echo "<!-- $m -->"; 
		echo "<br><hr>\n";
 	}
}