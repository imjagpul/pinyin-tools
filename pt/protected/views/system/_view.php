<?php
/* @var $this SystemController */
/* @var $data System */
?>

<div class="view">


	<?php echo CHtml::link(CHtml::encode($data->name), array('system/view', 'id'=>$data->id)); ?>
	(by <?php echo $data->masterUser->login ?>)
	
	<?php 
	if($data->isWriteable()) {
		echo '<span class="editlink">['.CHtml::link("Edit", array('system/update', 'id'=>$data->id)).']</span>';		
	}
	//echo "[Browse]";
	//echo "[Favorite]"; //perhaps move these two to the description 
	//echo "[Hide]";
	
	?>
	<br />
	<?php 
	$l=$data->targetLanguageData; if($l!==NULL) echo 'for <b>'.$l->text.'</b>'; 
	$l=$data->languageData; if($l!==NULL) echo '('.$l->text.')'; 
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