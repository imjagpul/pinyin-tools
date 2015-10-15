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
//$cs->registerCss('tones', UserSettings::getCurrentSettings()->tonesCss); 
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
	<?php echo $content; ?>
	</body>
</html>
