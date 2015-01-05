<?php
/* @var $this DictionaryController */
/* @var $data Dictionary */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('name')); ?>:</b>
	<?php echo CHtml::encode($data->name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('languageId')); ?>:</b>
	<?php echo CHtml::encode($data->languageId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('transcriptionId')); ?>:</b>
	<?php echo CHtml::encode($data->transcriptionId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('targetLanguageId')); ?>:</b>
	<?php echo CHtml::encode($data->targetLanguageId); ?>
	<br />


</div>