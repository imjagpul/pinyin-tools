<?php /* @var $this DictionaryCacheWorker */ 

?><div><a name="<?php echo $entryText; ?>"></a><?php 
if(!empty($mnemos)) {
	echo '<div class="tags">';
	echo '<b>'.$mnemos->keyword.'</b><br>';
	echo $mnemos->mnemonicsHTML;
	AnnotatorController::outputKeywords($mnemos->components);
	echo '</div>';
}
echo '<p></p>';
      ?><div class="ch s"><?php echo self::linkify($entry->getText(AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY)); 
?></div><div class="ch t"><?php echo self::linkify($entry->getText(AnnotatorEngine::CHARMOD_TRADITIONAL_ONLY)); 
?></div><div class="pinyin"><?php echo $transcriptionFormatter->format($entry->transcription); 
?></div><ul><?php foreach($entry->translationsArray as $tr)  //might be replaced with implode </li><li>
					echo "<li>$tr</li>"; 
		?></ul></div>