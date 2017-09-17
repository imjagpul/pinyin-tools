<?php
/* @var $this AnnotatorController */

if(!empty($translations) || !empty($mnemos)) { ?>
<a <?php if(!empty($mnemos)) { ?>class="t" <?php } ?>onmouseover="box(new Array(
<?php 
echo $this->boxToDisplay($translations, $mnemos, $phrases, $transcriptionFormatters, $characterModeAnnotations); 
?>))" onmouseout="hb()"><?php echo $char; ?></a><?php } else { echo $char; }?>