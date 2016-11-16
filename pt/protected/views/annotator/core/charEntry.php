<div><a name="c<?php echo $char; ?>"></a>
<?php if(!empty($mnemos)) {
	echo '<div class="tags">';
	echo '<b>'.$mnemos->keyword.'</b><br>';
	echo $mnemos->mnemonicsHTML;
	$this->outputKeywords($mnemos->components);
	echo '</div>';
}
echo '<p></p>';

	  foreach($translations as $trans) {?>
		<div class="ch s"><?php echo $trans->getText(AnnotatorEngine::CHARMOD_SIMPLIFIED_ONLY); ?></div>
		<div class="ch t"><?php echo $trans->getText(AnnotatorEngine::CHARMOD_TRADITIONAL_ONLY); ?></div>
		<div class="pinyin"><?php echo $transcriptionFormatters[$trans->dictionaryId]->format($trans->transcription); ?></div>
 		<ul><?php foreach($trans->translationsArray as $tr) { //might be replaced with implode </li><li>
					echo "<li>$tr</li>"; 
		 } ?></ul>
		<?php } ?>
					
		</div>