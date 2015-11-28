<?php /* @var $this Controller */ 

$cs=Yii::app()->clientScript;
$cs->coreScriptPosition=CClientScript::POS_HEAD;
$cs->scriptMap=array();

$cssDir=Yii::app()->request->baseUrl.'/css/';
$cs->registerCssFile($cssDir.'main.css');
$cs->registerCssFile($cssDir.'form.css');
// $cs->registerCssFile($cssDir.'screen.css');
$cs->registerCssFile($cssDir.'global-custom.css');
$cs->registerCssFile($cssDir.'navigation-custom.css');
//$cs->registerCssFile($cssDir.'layout-6-custom.css');

//generated css for tones (depending on user settings)
$cs->registerCss('tones', UserSettings::getCurrentSettings()->tonesCss); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<meta name="keywords" content="chinese,annotator,pinyin,offline,mnemonics,keywords" />
	<meta name="language" content="en" />
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div id="maincontainer">

<div id="headline1">
<div class="title"><a href="<?php echo $this->createUrl('site/index');?>">Pinyin <em>tools</em></a></div>
<span class="subtitle">Chinese mnemonics and annotator</span>
</div>

<div id="navtoplist" class="navtoplist">
		<?php $obj=$this->widget('zii.widgets.CMenu',array(
				'activeCssClass'=>'current',
				
				'items'=>array(
				array('label'=>'What\'s this all about?', 'url'=>array('/site/page')),
				array('label'=>'Browse', 'url'=>array('/char/index')),
				array('label'=>'Add', 'url'=>array('/char/create')),
				array('label'=>'Texts', 'url'=>array('/text/index')),
				array('label'=>'Systems', 'url'=>array('/system/index')),
				array('label'=>'Settings', 'url'=>array('/userSettings/update')),
				array('label'=>'Annotator', 'url'=>array('/annotator'))
			),
		)); 
		?>
</div>

<div id="navtoplistline">&nbsp;</div>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		copyright &copy; <?php echo date('Y'); ?> by Imjagpul<br/> 
		licensed under the <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution License</a><br/>
		<?php echo Yii::powered(); ?>
		Using web template by <a href="http://www.wfiedler-online.de">wfiedler</a>. 
	</div>
</div>

</body>
</html>
