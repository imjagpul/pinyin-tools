		<hr>
		<div>
		<a name="c<?php echo $index; ?>"></a>
		<?php if(!empty($mnemos)) { 
			echo '<div class="tags">';
			echo '<b>'.$mnemos->keyword.'</b><br>'; 
		 	//echo $mnemos->mnemo;
			echo $mnemos->mnemonicsHTML;
// 		 	$this->prepare($mnemos->mnemo);
		 	$this->outputKeywords($mnemos->components);
			echo '</div>';
		} 
		echo '<p></p>'; 

		foreach($phrases as $phrase) { ?>
		<a href="#l<?php echo $index; ?>" class="back">
		<div class="ch"><?php echo $phrase->getText($simplified); ?></div>
		<div class="pinyin"><?php echo $transcriptionFormatters[$phrase->dictionaryId]->format($phrase->transcription); ?></div>
		</a>
 		<ul><?php foreach($phrase->translationsArray as $tr) { 
 			echo "<li>$tr</li>";
 		}
 		echo '</ul>';
	  }
	  
	  foreach($translations as $trans) {?>
		<a href="#l<?php echo $index; ?>" class="back">
		<div class="ch"><?php echo $trans->getText($simplified); ?></div>
		<div class="pinyin"><?php echo $transcriptionFormatters[$trans->dictionaryId]->format($trans->transcription); ?></div>
		</a>
 		<ul><?php foreach($trans->translationsArray as $tr) {
					echo "<li>$tr</li>"; 
		 } ?></ul>
		<?php } ?>
					
		</div>