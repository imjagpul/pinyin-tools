<?php
/* @var $this AnnotatorController */
/* @var $mode AnnotatorMode */

$baseUrl=Yii::app()->baseUrl;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');

$cs->registerScriptFile($baseUrl.'/js/main.js');
$cs->registerScriptFile($baseUrl.'/js/tooltip.js');
$cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
$cs->registerCssFile($baseUrl.'/css/main-gi.css');


?>
<h1>Annotator</h1>

<p>
<div class="form">
<?php echo CHtml::beginForm(array('go'), 'post'); ?>

<?php /* *************************** Annotator mode.  ***************************************************/ ?>
<div class="navsubmenu">
<ul>
<?php 
	//Add the menu with the links
	foreach(AnnotatorMode::getModesList() as $modeID => $modeData) {
		if(get_class($mode)==$modeData[0]) 
			echo '<li class="current">';
		else
			echo "<li>";
		echo CHtml::link($modeData[1], array("annotator/input", "modeID"=>$modeID));
		echo "</li>";
	}  
?>
</ul>
<p class="modeDesc">
<?php
if(!is_null($mode->getDescription())) echo $mode->getDescription(); 
?>
</p>
</div>
 
 <?php echo CHtml::hiddenField('template', $mode->getTemplateID()); ?> 

<?php /* *************************** Main text input.  ***************************************************/ ?>
<div class="row"><?php echo CHtml::label('Text to annotate:', 'input'); ?></div>
<div class="row"><?php echo CHtml::textArea('input', '', array('rows'=>20, 'cols'=>80)); ?></div>


<?php /* *************************** Parallel data (optional).  ***************************************************/ ?>
<?php if($mode->allowParallel()) { ?>
<div class="row">
	<?php echo CHtml::label('Parallel text:', 'parallel'); ?>	
</div> 
<div class="row sticky">
	<?php echo CHtml::textArea('parallel', '', array('rows'=>20, 'cols'=>80, 'class'=>'with-tooltip')); ?>
	<div class="tooltip">
			The text will be matched line by line. 
	</div>
</div>

<div class="section">
<div class="row">
	<?php echo CHtml::label('Audio URL', 'parallel'); ?>
</div> 
<div class="row sticky">
	<?php //@TODO make sticky work; add note "It might be better"  
	echo CHtml::textField('audioURL', '', array('rows'=>20, 'cols'=>80)); ?> 
</div>
</div>
<?php } ?>

<?php /* *************************** System choice.  ***************************************************/ ?>
<div class="section">
<div class="row">
<?php if(!empty($systemList)) { ?>
	<?php 
	echo CHtml::label('System:', 'system');?>  
</div>
<div class="row">
	<?php
	echo CHtml::dropDownList('system', $systemLast, CHtml::listData($systemList, 'id', 'name'), array('prompt'=>'(no mnemonics)')); 
	?>
<?php } ?>
</div>
</div>
<?php /* *************************** Dictionaries choice.  ***************************************************/ ?>
<?php if(!empty($allDicts)) { ?>
<div class="section">
<div class="row">
	<?php echo CHtml::label('Dictionaries:', 'dictionary'); ?>  
</div>
<div class="row">
	<?php
	echo CHtml::checkBoxList('selectedDictionaries', $selectedDicts, CHtml::listData($allDicts,'id','name'));
	?> 
</div>
</div>
<?php } ?>	

<?php /* *************************** Buttons (depending on mode).  ***************************************************/ ?>

<div class="row buttons">
	
	<?php 
	if($mode->allowView()) echo CHtml::submitButton('Show in browser', array('name'=>'submit-view'));
	echo ' &nbsp; ';
	if($mode->allowDownload()) echo CHtml::submitButton('Download', array('name'=>'submit-download'));
	?>
</div>

<?php echo CHtml::endForm();?>

</p>
</div>
