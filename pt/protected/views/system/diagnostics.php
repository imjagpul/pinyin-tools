<?php
foreach($data as $title=>$entry) {
	echo "<h1>$title (".count($entry).")</h1>";
	foreach($entry as $char) {
		if(is_string($char))
			echo $char;
		else if(get_class($char)=="Char")
			echo CHtml::link($char->chardef, array("char/update", 'id'=>$char->id));
		else 
			echo "Object of type ".get_class($char);
// 		$m=$char->mnemo;
// 		$brPos=strpos($m, "<br", 0);
// 		$brPosEnd=strpos($m, ">", $brPos)+1;
// 		echo "<!-- $brPosEnd -->"; 
// 		echo "<br>\n";
// 		echo substr($m, $brPosEnd);
		echo "\n";
// 		echo "<!-- $m -->"; 
		echo "<br><hr>\n";
 	}
}