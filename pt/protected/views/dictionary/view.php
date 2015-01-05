
<h1>View Dictionary #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'languageId',
		'transcriptionId',
		'targetLanguageId',
	),
)); ?>
