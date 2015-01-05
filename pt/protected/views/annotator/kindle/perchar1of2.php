<?php
/* @var $this AnnotatorController */

if(!empty($translations) || !empty($mnemos)) { ?><a name="l<?php echo $index; ?>" href="#c<?php echo $index; ?>" style="text-decoration: none !important;" <?php if(empty($mnemos)) { ?>class="u" <?php } ?>><?php echo $char; ?></a><?php } else { echo $char; }?>