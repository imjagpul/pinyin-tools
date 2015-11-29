<?php
/* @var $this UserSettingsController */
/* @var $model UserSettings */
/* @var $form CActiveForm */
function output($controller, $form, $model, $attribute) {
	echo $form->label($model,$attribute);

	$controller->widget('application.extensions.colorpicker.EColorPicker',
			array(
					'name'=>$attribute,
					'mode'=>'textfield',
					'value'=>sprintf("%06x", $model->$attribute),
					'curtain' => true,
					'timeCurtain' => 250));
	
	echo $form->error($model,$attribute);
		
}

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

	<p class="note">Click on a field to display or change the color.</p>

	<?php echo $form->errorSummary($model); ?>
<?php


	?>
	<div class="row"><?php output($this, $form, $model, 'toneColor1'); ?></div>
	<div class="row"><?php output($this, $form, $model, 'toneColor2'); ?></div>
	<div class="row"><?php output($this, $form, $model, 'toneColor3'); ?></div>
	<div class="row"><?php output($this, $form, $model, 'toneColor4'); ?></div>
	<div class="row"><?php output($this, $form, $model, 'toneColor5'); ?></div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->