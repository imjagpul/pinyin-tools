<?php
/* @var $this CharController */
/* @var $data Char */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('chardef')); ?>:</b>
	<?php echo CHtml::encode($data->chardef); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('system')); ?>:</b>
	<?php echo CHtml::encode($data->system); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('keyword')); ?>:</b>
	<?php echo CHtml::encode($data->keyword); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('transcription')); ?>:</b>
	<?php echo CHtml::encode($data->transcription); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('mnemo')); ?>:</b>
	<?php echo CHtml::encode($data->mnemo); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('notes')); ?>:</b>
	<?php echo CHtml::encode($data->notes); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('notes2')); ?>:</b>
	<?php echo CHtml::encode($data->notes2); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('notes3')); ?>:</b>
	<?php echo CHtml::encode($data->notes3); ?>
	<br />

	*/ ?>

</div>