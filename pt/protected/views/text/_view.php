<?php
/* @var $this TextController */
/* @var $data Text */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->name), array('view', 'id'=>$data->name)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('category')); ?>:</b>
	<?php echo CHtml::encode($data->category); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('original')); ?>:</b>
	<?php echo CHtml::encode($data->original); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('translations')); ?>:</b>
	<?php echo CHtml::encode($data->translations); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('audio')); ?>:</b>
	<?php echo CHtml::encode($data->audio); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('storedId')); ?>:</b>
	<?php echo CHtml::encode($data->storedId); ?>
	<br />


</div>