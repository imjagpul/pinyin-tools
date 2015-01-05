<?php
/* @var $this CharController */
/* @var $model Char */

// $this->breadcrumbs=array(
// 	'Chars'=>array('index'),
// 	$model->id,
// );

// $this->menu=array(
// 	array('label'=>'List Char', 'url'=>array('index')),
// 	array('label'=>'Create Char', 'url'=>array('create')),
// 	array('label'=>'Update Char', 'url'=>array('update', 'id'=>$model->id)),
// 	array('label'=>'Delete Char', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
// 	array('label'=>'Manage Char', 'url'=>array('admin')),
// );
?>
<?php 
if(isset($special)&& $special=="autocorrect") {
	echo "<h2>Autocorrect</h2>";
	$result=CharDiagnostics::autoconvert($model);
	if($result===true) {
		echo "done";
	} else {
		echo "failed";
	}
}
?>

<h1>View Char #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'chardef',
		'system',
		'keyword',
		'transcription',
		'mnemo',
		'mnemonicsHTML',
		'diagnostics',
		'notes',
		'notes2',
		'notes3',
	),
)); ?>

<p>
<?php echo $model->mnemonicsHTML; ?>
</p>
<p>
<a href="<?php echo $this->createUrl('char/lookup', array('s'=>$model->chardef))?>">other entries for the same character</a>
<br>
<a href="<?php echo $this->createUrl('char/lookup', array('s'=>$model->keyword))?>">other entries with the same keyword</a>
<br>
<a href="<?php echo $this->createUrl('char/update', array('id'=>$model->id))?>">Edit</a>
</p>