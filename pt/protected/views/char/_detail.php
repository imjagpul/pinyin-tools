<?php 
/* @var $this CharController */
/**
* @var Char $model
* @var int $systemFlag
*     one of the SYSTEM_STATUS_* values as defined in CharController
*/
 
$editLink="";
if($systemFlag==SYSTEM_STATUS_PRIMARY || $systemFlag==SYSTEM_STATUS_OWN) {
	//edit button (output only if this system is editable for the logged in user)
	$editLink='<span class="editlink">[';
	$editLink.=CHtml::link('Edit entry', array('char/update', 'id'=>$model->id));
	$editLink.=']</span>';
}

$icons="";
$iconsPath=Yii::app()->request->baseUrl.'/images/icons/';
if($systemFlag==SYSTEM_STATUS_PRIMARY) {
		$icons.=' ';
		$icons.=CHtml::image($iconsPath.'primary.png');
} else if($systemFlag==SYSTEM_STATUS_FAVORITE) {
		$icons.=' ';
		$icons.=CHtml::image($iconsPath.'favorite.png');
}


?>
<h2><?php echo $model->systemName; ?><?php echo $icons; ?></h2><?php echo $editLink; ?>
				
<?php $this->renderPartial('_view', array('data'=>$model)); ?>
