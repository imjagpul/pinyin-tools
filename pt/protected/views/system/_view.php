<?php
/* @var $this SystemController */
/* @var $data System */
?>

<div class="view">


	<?php echo CHtml::link(CHtml::encode($data->name), array('system/view', 'id'=>$data->id)); ?>
	(by <?php echo $data->masterUser->username ?>)
	
	<span style="float: right">
	
	<?php 
		echo '<span class="editlink">';
	$iconsPath=Yii::app()->request->baseUrl.'/images/icons/';
	if($data->isWriteable()) {
		echo CHtml::link(CHtml::image($iconsPath.'primary.png', "").'Primary', array('system/update', 'id'=>$data->id));
		echo "&nbsp;";
		echo CHtml::link(CHtml::image($iconsPath.'edit.png', "").'Edit', array('system/update', 'id'=>$data->id));
	} else {
		echo "&nbsp;";
		echo CHtml::link(CHtml::image($iconsPath.'favorite.png', "").'Favorite', array('system/update', 'id'=>$data->id));
		echo "&nbsp;";
		echo CHtml::link(CHtml::image($iconsPath.'hide.png', "").'Hide', array('system/update', 'id'=>$data->id));
	}
		echo "&nbsp;";
		echo CHtml::link(CHtml::image($iconsPath.'browse.png', "").'Browse', array('system/update', 'id'=>$data->id));
	echo '</span>';		
	?>
	</span>
	
	<br />
	<?php 
	$l=$data->targetLanguageData; if($l!==NULL) echo 'for <b>'.$l->text.'</b>'; 
	$l=$data->languageData; if($l!==NULL) echo ' ('.$l->text.')'; 
	?>
	<br />
	<?php echo CHtml::encode($data->shortenedDescription); ?>
	<br />

	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('targetLanguage')); ?>:</b>
	<?php echo CHtml::encode($data->targetLanguage); ?>
	<br />

	*/ ?>

</div>