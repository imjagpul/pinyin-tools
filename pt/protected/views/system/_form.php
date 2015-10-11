<?php
/* @var $this SystemController */
/* @var $model System */
/* @var $status Integer */
/* @var $form CActiveForm */
/* @var $languagesList Lookup[] */
/* @var $targetLanguagesList Lookup[] */

$baseUrl=Yii::app()->baseUrl;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/main.js');
$cs->registerScriptFile($baseUrl.'/js/tooltip.js');
$cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
$cs->registerCssFile($baseUrl.'/css/main-gi.css');

?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'system-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'targetLanguage'); ?>
		<?php echo $form->dropDownList($model,'targetLanguage',CHtml::listData($targetLanguagesList, 'id', 'text'), array('class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'targetLanguage'); ?>
		<div class="tooltip">What language are you learning using this system.</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'language'); ?>
		<?php echo $form->dropDownList($model,'language',CHtml::listData($languagesList, 'id', 'text'), array('class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'language'); ?>
		<div class="tooltip">In which language are the keywords, mnemonics and notes written.</div>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>10, 'class'=>'with-tooltip', 'class'=>'with-tooltip')); ?>
		<?php /*echo $form->textField($model,'description',array('size'=>60,'maxlength'=>2048)); */?>
		<?php echo $form->error($model,'description'); ?>
		<div class="tooltip">Here you can describe how this system works (HTML is allowed).</div>
		
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'shortdescription'); ?>
		<?php echo $form->textArea($model,'shortdescription',array('rows'=>1,'class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'shortdescription'); ?>
		<div class="tooltip">A short (optional) description for the system list.</div>
		
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'mnemosystem'); ?>
		<?php echo $form->dropDownList($model,'mnemosystem',array('none'=>'None','meaning'=>'Meaning only','pronunciation'=>'Pronunciation only','both'=>'Both','other'=>'Other'), array('class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'mnemosystem'); ?>
		<div class="tooltip">What do the mnemonics help you remember?
		<ul>
		<li><b>None</b> - there are no mnemonics in this system</li>
		<li><b>Meaning only</b> - what do the characters mean or how they are composed</li>
		<li><b>Pronunciation only</b> - how the characters are pronounced</li>
		<li><b>Both</b> - both of the above</li>
		<li><b>Other</b> - something else</li>
		</ul>
		</div>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'visibility'); ?>
		<?php echo $form->dropDownList($model,'visibility',array('visible'=>'Visible', 'nolisting'=>'Unlisted', 'private'=>'Private'), array('class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'visibility'); ?>
		
		<div class="tooltip">Can others access your system?
		<ul>
		<li><b>Visible</b> - anybody can view the entries in this system (but only you can edit them)</li>
		<li><b>Unlisted</b> - entries are shown when a character is viewed but the system is not listed on the "Systems" tab</li>
		<li><b>Private</b> - nobody except you can view the entries in the system</li>
		</ul>
		</div>
		
	</div>
	
	
	
	
	<div class="row buttons">
	<?php echo CHtml::hiddenField('status', $status); ?>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->