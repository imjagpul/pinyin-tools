<h1>Generate data</h1>

<?php if(isset($msg)) { ?>
<p><?php echo $msg; ?></p>
<?php } ?>


<p>You are going to generate precached files for this dictionary: </p>
<?php $this->renderPartial('_view', array('data'=>$model)); ?>

<?php echo CHtml::beginForm($this->createUrl("dictionary/generateFiles"), 'post'); ?>

	<div class="row">
	<?php echo CHtml::hiddenField('id', $model->id)?>
	</div>
	
	<div class="row">
	<?php echo CHtml::textField('system_id', $model->id)?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Generate'); ?>
	</div>
</form>
<?php echo CHtml::endForm(); ?>