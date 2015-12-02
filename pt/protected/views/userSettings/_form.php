<?php
/* @var $this UserSettingsController */
/* @var $model UserSettings */
/* @var $form CActiveForm */
function output($controller, $form, $model, $attribute) {
	echo $form->label($model,$attribute);

	$colorHex=Utilities::colorAsHex($model->$attribute);
	
	$controller->widget('application.extensions.colorpicker.EColorPicker',
			array(
					'name'=>$attribute,
					'selector'=>$attribute,
					'mode'=>'selector',
					'value'=>$colorHex,
					'curtain' => true,
					'timeCurtain' => 250));

	echo '<div><div id="'.$attribute.'selector" class="colorSelector"><div style="background-color: #'.$colorHex.'"></div></div><input id="'.$attribute.'" type="text" class="colorSelectorInput" value="'.$colorHex.'"></div>';	
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

 foreach($model->colorNames as $col) {

//echo '<div class="row">';
output($this, $form, $model, $col);
//echo '</div>';


 }

	?>
	<script type="text/javascript">$(".colorSelectorInput").hide();</script>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->