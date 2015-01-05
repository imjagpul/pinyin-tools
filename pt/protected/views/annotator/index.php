<?php
/* @var $this AnnotatorController */

$baseUrl=Yii::app()->baseUrl;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');

// $cs->registerScriptFile($baseUrl.'/js/main.js');
// $cs->registerScriptFile($baseUrl.'/js/tooltip.js');
// $cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');

//$cs->registerCssFile($baseUrl.'/css/main-gi.css');

$cs->registerScriptFile($baseUrl.'/js/main.js');
$cs->registerScriptFile($baseUrl.'/js/tooltip.js');
$cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
$cs->registerCssFile($baseUrl.'/css/main-gi.css');


?>
<h1>Annotator</h1>

<p>
<div class="form">
<?php echo CHtml::beginForm(array('go'), 'post'); ?>

<div class="row">
	<?php echo CHtml::textArea('input', '', array('rows'=>20, 'cols'=>80)); ?>
</div>

<div class="row">
<?php if(!empty($systemList)) { ?>
	<?php 
	echo CHtml::label('Add mnemonics from this system:', 'system'); 
	echo CHtml::dropDownList('system', $systemLast, CHtml::listData($systemList, 'id', 'name'), array('prompt'=>'(no mnemonics)')); 
	?>
<?php } ?>
</div>

<div class="row">
<?php if(!empty($templatesList)) { ?>
	<?php 
	echo CHtml::label('Output:', 'template');
	echo CHtml::dropDownList('template', $lastTemplate, $templatesList); 
	?>
<?php } ?>
</div>

<?php if(!empty($allDicts)) { ?>
<div class="row">
	<?php echo CHtml::label('Add translations from these dictionaries:', 'dictionary'); ?>  
</div>

<div class="row">
	<?php
	echo CHtml::checkBoxList('selectedDictionaries', CHtml::listData($selectedDicts,'dictionaryId','dictionaryId'), CHtml::listData($allDicts,'id','name'));
	?> 
</div>
<?php } ?>	

<div class="row">
	<?php echo CHtml::label('Text to be shown in parallel', 'parallel'); ?>	
</div> 
<div class="row sticky">
	<?php echo CHtml::textArea('parallel', '', array('rows'=>20, 'cols'=>80, 'class'=>'with-tooltip')); ?>
	<div class="tooltip">
			The text will be matched line by line. 
	</div>
</div>

<div class="row">
	<?php echo CHtml::label('Audio URL', 'parallel'); ?>
</div> 
<div class="row sticky">
	<?php //@TODO make sticky work; add note "It might be better"  
	echo CHtml::textField('audioURL', '', array('rows'=>20, 'cols'=>80)); ?> 
</div>

<div class="row">
	<?php echo CHtml::label('Output type', 'type'); ?>	
</div> 
<div class="row">
	<?php echo CHtml::radioButtonList('type', 'html', array('html'=>'Show in browser', 'download'=>'Download')); ?>
	<div class="tooltip">
			The text will be matched line by line. 
	</div>
</div>


<div class="row buttons">
	<?php echo CHtml::submitButton();?>
</div>

<?php echo CHtml::endForm();?>

</p>
</div>
