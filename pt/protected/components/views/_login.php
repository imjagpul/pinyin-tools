
<div class="login">
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
// 		'action'=>array('site/login'),
		'action'=>array('log.Login'),
 	'enableAjaxValidation'=>false,
 	'enableClientValidation'=>false,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>


	<div class="row">
		<?php echo CHtml::hiddenField('returnUrl', Yii::app()->getRequest()->getUrl()); ?>
		<?php echo $form->label($model,'username'); ?>
		<?php echo $form->textField($model,'username', array('class'=>'full')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'password'); ?>
		<?php echo $form->passwordField($model,'password', array('class'=>'full')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row rememberMe">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Login'); ?>
		or 
		<a href="<?php echo  Yii::app()->createUrl("/site/register"); ?>">register</a>
	</div>

<?php $this->endWidget(); ?>
</div>
</div>
