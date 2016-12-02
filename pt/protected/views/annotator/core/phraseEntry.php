<div><a name="<?php echo $char; ?>"></a><?php 
if(!empty($mnemos)) {
	echo '<div class="tags">';
	echo '<b>'.$mnemos->keyword.'</b><br>';
	echo $mnemos->mnemonicsHTML;
	$this->outputKeywords($mnemos->components);
	echo '</div>';
}
echo '<p></p>';
      ?><div class="ch s"><?php echo $entry->getText(AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY); 
?></div><div class="ch t"><?php echo $entry->getText(AnnotatorEngine::CHARMOD_TRADITIONAL_ONLY); 
?></div><div class="pinyin"><?php echo $transcriptionFormatters[$entry->dictionaryId]->format($entry->transcription); 
?></div><ul><?php foreach($entry->translationsArray as $tr)  //might be replaced with implode </li><li>
					echo "<li>$tr</li>"; 
		?></ul></div>