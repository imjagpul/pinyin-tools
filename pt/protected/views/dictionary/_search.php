<?php
/* @var $this DictionaryController */
/* @var $model Dictionary */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'languageId'); ?>
		<?php echo $form->textField($model,'languageId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'transcriptionId'); ?>
		<?php echo $form->textField($model,'transcriptionId'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'targetLanguageId'); ?>
		<?php echo $form->textField($model,'targetLanguageId'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->