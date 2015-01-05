<?php
/* @var $this SystemController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Systems',
);

$this->menu=array(
	array('label'=>'Create System', 'url'=>array('create')),
	array('label'=>'Manage System', 'url'=>array('admin')),
);


if(!is_null($dataProviderUser)) { ?>

<h1>Systems created by you</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProviderUser,
	'itemView'=>'_view',
)); 


echo CHtml::link("Create a new system", array('system/create'));
} 
?>

<h1>Public systems</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); 
?>
