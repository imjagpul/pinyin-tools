<?php
/* @var $this SystemController */
/* @var $model System */

?>

<h1>Create System</h1>

<p><?php 
if(isset($msg)) {
	echo $msg;
}
?></p>

<?php $this->renderPartial('_form', array('model'=>$model,
		'languagesList'=>$languagesList,
		'targetLanguagesList'=>$targetLanguagesList
)); ?>