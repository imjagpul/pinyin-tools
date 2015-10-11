<?php
/* @var $this SystemController */
/* @var $model System */
/* @var $status Integer */

?>

<h1>Create System</h1>

<p><?php
//TODO improve constants handling
if($status==CREATE_SYSTEM_ADD_CHAR) {
	echo "Before adding an entry, give a name to your system.";
}?>

A system is a collection of mnemonics. See <a href="<?php 
echo $this->createUrl("/site/page", array("view"=>"systemsExplanation")); 
?>">detailed explanation</a>.

</p>

<?php $this->renderPartial('_form', array('model'=>$model,
		'languagesList'=>$languagesList,
		'targetLanguagesList'=>$targetLanguagesList,
		'status'=>$status
)); ?>