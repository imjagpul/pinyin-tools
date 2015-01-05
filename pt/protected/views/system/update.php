<?php
/* @var $this SystemController */
/* @var $model System */

?>

<h1><?php echo $model->name; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,
		'languagesList'=>$languagesList,
		'targetLanguagesList'=>$targetLanguagesList
		
)); ?>