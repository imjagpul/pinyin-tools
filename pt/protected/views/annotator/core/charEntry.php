<div><a name="<?php echo $entryText; ?>"></a><?php 
if(!empty($mnemos)) {
	echo '<div class="tags">';
	echo '<b>'.$mnemos->keyword.'</b><br>';
	echo $mnemos->mnemonicsHTML;
// 	AnnotatorController::outputKeywords($mnemos->components); //does not work for some reason, so temporarily just copied here
	$composition=$mnemos->components;
	if(count ( $composition )>0) {
	
		$result = '<br>';
	
		for($i = 0; $i < count ( $composition ); $i ++) {
			$sub = $composition [$i]->subchar;
				
			$result .= $sub->keyword;
			$result .= ' ';
			$result .= $sub->chardef;
				
			if ($i != count ( $composition ) - 1)
				$result .= " + ";
		}
		echo $result;
	}
		//end of AnnotatorController::outputKeywords
	
	echo '</div>';
}
echo '<p></p>';
      ?><div class="ch s"><?php echo $entry->getText(AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY); 
?></div><div class="ch t"><?php echo $entry->getText(AnnotatorEngine::CHARMOD_TRADITIONAL_ONLY); 
?></div><div class="pinyin"><?php echo $transcriptionFormatter->format($entry->transcription); 
?></div><ul><?php foreach($entry->translationsArray as $tr)  //might be replaced with implode </li><li>
					echo "<li>$tr</li>"; 
		?></ul></div>