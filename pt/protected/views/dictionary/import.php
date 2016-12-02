<?php 
//file:///home/imjagpul/Data/books/php-chunked-xhtml/features.file-upload.post-method.html
//See also the file_uploads, upload_max_filesize, upload_tmp_dir, post_max_size and max_input_time directives in php.ini

?>
<h1>Import data</h1>

<?php if(isset($msg)) { ?>
<p><?php echo $msg; ?></p>
<?php } ?>

<h2>Data</h2>
<div class="form">
<?php echo CHtml::beginForm($this->createUrl("dictionary/import"), 'post', array('enctype'=>'multipart/form-data')); ?>

	<div class="row">
	<?php echo CHtml::hiddenField('id', $model->id)?>
	<?php 
	echo CHtml::hiddenField('MAX_FILE_SIZE', Yii::app()->params->maxDictUploadFileSize)?>
	<?php echo CHtml::fileField('upfile')?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Upload'); ?>
	</div>
</form>
<?php echo CHtml::endForm(); ?>
</div>

<h2>Info</h2>

<p>Max file size: <?php echo Yii::app()->params->maxDictUploadFileSize; ?>

<p>Encoding: 
<?php 
/* Set internal character encoding to UTF-8 */
// mb_internal_encoding("UTF-8");

/* Display current internal character encoding */
echo mb_internal_encoding();
?></p>

<p>You are uploading into this dictionary: </p>
<?php $this->renderPartial('_view', array('data'=>$model)); ?>
