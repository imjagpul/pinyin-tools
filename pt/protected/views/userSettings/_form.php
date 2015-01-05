<?php
/* @var $this UserSettingsController */
/* @var $model UserSettings */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-settings-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'userId'); ?>
		<?php echo $form->textField($model,'userId'); ?>
		<?php echo $form->error($model,'userId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'toneColor1'); ?>
		<?php echo $form->textField($model,'toneColor1'); ?>
		<?php echo $form->error($model,'toneColor1'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'toneColor2'); ?>
		<?php echo $form->textField($model,'toneColor2'); ?>
		<?php echo $form->error($model,'toneColor2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'toneColor3'); ?>
		<?php echo $form->textField($model,'toneColor3'); ?>
		<?php echo $form->error($model,'toneColor3'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'toneColor4'); ?>
		<?php echo $form->textField($model,'toneColor4'); ?>
		<?php echo $form->error($model,'toneColor4'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'toneColor5'); ?>
		<?php echo $form->textField($model,'toneColor5'); ?>
		<?php echo $form->error($model,'toneColor5'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->