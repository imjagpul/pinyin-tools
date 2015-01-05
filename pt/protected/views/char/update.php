<?php
/* @var $this CharController */
/* @var $model Char */

// $this->breadcrumbs=array(
// 	'Chars'=>array('index'),
// 	$model->id=>array('view','id'=>$model->id),
// 	'Update',
// );

// $this->menu=array(
// 	array('label'=>'List Char', 'url'=>array('index')),
// 	array('label'=>'Create Char', 'url'=>array('create')),
// 	array('label'=>'View Char', 'url'=>array('view', 'id'=>$model->id)),
// 	array('label'=>'Manage Char', 'url'=>array('admin')),
// );
?>

<h1>Edit <?php echo $model->chardef; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'systemList'=>$systemList)); ?>