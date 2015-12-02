<?php
/* @var $this UserSettingsController */
/* @var $model UserSettings */
/* @var $form CActiveForm */
function output($controller, $form, $model, $attribute, $label="") {
/* 	echo $form->label($model,$attribute);*/

	$colorHex=Utilities::colorAsHex($model->$attribute);
	$label='<span>'.$label.'</span>';
	$controller->widget('application.extensions.colorpicker.EColorPicker',
			array(
					'name'=>$attribute,
					'selector'=>$attribute,
					'mode'=>'selector',
					'value'=>$colorHex,
					'curtain' => true,
					'timeCurtain' => 250));

	echo '<div id="'.$attribute.'selector" class="colorSelector"><div style="background-color: #'.$colorHex.'"></div>'.$label.'</div><input id="'.$attribute.'" type="text" class="colorSelectorInput" value="'.$colorHex.'">';	
// 	echo $form->error($model,$attribute);
		
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


	<?php echo $form->errorSummary($model); ?>

<?php 
if(Yii::app()->user->isGuest) {
echo '<p>Note you are not logged in. Your settings will not be saved between sessions.</p>';
}?>
<h1>Tone colors</h1>
<p>Customize the tone colors. This applies to both dictionary results and the annotator output.</p>
<?php output($this, $form, $model, 'toneColor1', 'First'); ?>
<?php output($this, $form, $model, 'toneColor2', 'Second'); ?>
<?php output($this, $form, $model, 'toneColor3', 'Third'); ?>
<?php output($this, $form, $model, 'toneColor4', 'Fourth'); ?>
<?php output($this, $form, $model, 'toneColor5', 'Neutral&nbsp;(or&nbsp;fifth)'); ?>
<?php output($this, $form, $model, 'toneColor6', 'Sixth&nbsp;(for&nbsp;Cantonese)'); ?>

<h1>Annotator colors</h1>
<?php output($this, $form, $model, 'background', 'Background'); ?>
<?php output($this, $form, $model, 'foreground', 'Foreground'); ?>
<h2>Characters having no mnemonic</h2> <?php echo CHtml::link("(more info)", array('/site/page', 'view'=>'untaggedCharacters'))?>
<?php output($this, $form, $model, 'foregroundUnknown', 'Foreground&nbsp;-&nbsp;no&nbsp;mnemonics'); ?>

<?php /* output($this, $form, $model, 'backgroundParallel'); */ ?>
<h2>Results box</h2> 
<?php output($this, $form, $model, 'backgroundBoxTag', 'Mnemo'); ?>
<?php output($this, $form, $model, 'backgroundBoxChinese', 'Characters'); ?>
<?php output($this, $form, $model, 'backgroundBoxTranscription', 'Pronunciation'); ?>
<?php output($this, $form, $model, 'backgroundBox', 'Translations'); ?>


	<script type="text/javascript">$(".colorSelectorInput").hide();</script>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->