<?php
/* @var $data System */

if($data->isWriteable()) {
	?>
	Options:
	<ul id="navmainlist">
	<li><?php echo CHtml::link("Edit this system", array('system/update', 'id'=>$data->id)); ?></li>
	</ul>
	<?php	
	
}
