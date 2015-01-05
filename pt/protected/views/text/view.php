<?php
/* @var $this TextController */
/* @var $model Text */

$this->breadcrumbs=array(
	'Texts'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Text', 'url'=>array('index')),
	array('label'=>'Create Text', 'url'=>array('create')),
	array('label'=>'Update Text', 'url'=>array('update', 'id'=>$model->name)),
	array('label'=>'Delete Text', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->name),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Text', 'url'=>array('admin')),
);
?>

<h1>View Text #<?php echo $model->name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'name',
		'category',
		'description',
		'original',
		'translations',
		'audio',
		'storedId',
	),
)); ?>
