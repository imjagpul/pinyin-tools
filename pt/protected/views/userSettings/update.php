<?php
/* @var $this UserSettingsController */
/* @var $model UserSettings */

$this->breadcrumbs=array(
	'User Settings'=>array('index'),
	$model->userId=>array('view','id'=>$model->userId),
	'Update',
);

$this->menu=array(
	array('label'=>'List UserSettings', 'url'=>array('index')),
	array('label'=>'Create UserSettings', 'url'=>array('create')),
	array('label'=>'View UserSettings', 'url'=>array('view', 'id'=>$model->userId)),
	array('label'=>'Manage UserSettings', 'url'=>array('admin')),
);
?>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>