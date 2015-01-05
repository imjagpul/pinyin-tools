<?php
/* @var $this CharController */
/* @var $model Char */

// $this->breadcrumbs=array(
// 	'Chars'=>array('index'),
// 	'Create',
// );

// $this->menu=array(
// 	array('label'=>'List Char', 'url'=>array('index')),
// 	array('label'=>'Manage Char', 'url'=>array('admin')),
// );
?>

<h1>Add a new entry</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'systemList'=>$systemList)); ?>