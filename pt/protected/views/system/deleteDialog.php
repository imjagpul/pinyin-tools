<?php
/* @var $this SystemController */
/* @var $model System */

$systemName=$model->name;

if(!$model->isWriteable()) {
	throw new CHttpException(403,'You are not authorized to delete this system.');
}
?>

<h1>Delete the system "<?php echo $systemName; ?>"</h1>
<p>
Are you sure you want to delete the system?
<?php  if($model->ownEntriesCount==0) { ?>
(It is empty.)
<?php } else  { ?>
It has <?php echo $model->ownEntriesCount==1 ? "one entry" : $model->ownEntriesCount." entries"; ?>.
<?php }
echo CHtml::form(array('/system/deleteDialog'));
echo CHtml::hiddenField('id', $model->id);
echo CHtml::hiddenField('doIt', "TRUE");
echo CHtml::submitButton($model->ownEntriesCount==0 ? "Delete system" : "Delete system with all entries!");
echo CHtml::endForm();
?>
</p>
