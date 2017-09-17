<?php
$cs=Yii::app()->clientScript;
$baseUrl=Yii::app()->baseUrl;
$cs->registerScriptFile($baseUrl.'/js/translationsBoxScript.js', CClientScript::POS_HEAD);

//generated css for tones (depending on user settings)
// $cs->registerCss('tones', UserSettings::getCurrentSettings()->tonesCss);

?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type"
	content="text/html; charset=<?php echo $charset; ?>">
<title></title>
<style type="text/css"> /*
			<![CDATA[ */
a {
	text-decoration: none;
	color: #<?php echo$colors['FG_UNTAGGED'] ?>;
}

a.t {
	text-decoration: none;
	color: #<?php echo$colors['FG'] ?>;
}

#box {
	border: 1px solid #1A40B0;
	background-color: #<?php echo$colors['BG_BOX'] ?>;
	padding: 5px;
	z-index: 100;
	visibility: hidden;
	width: 500px;
	position: absolute;
}

.tags {
	background-color: #<?php echo$colors['BG_TAGBOX'] ?>;
	font-size: large;
}

.ch {
	background-color: #<?php echo$colors['BG_BOX_CH'] ?>;
	font-size: x-large;
}

.pinyin {
	background-color: #<?php echo$colors['BG_TRANSCRIPTION'] ?>;
	font-size: large;
}

body {
	font-size: x-large;
	background-color: #<?php echo$colors['BG'] ?>;
}
<?php /* @TODO inculde the following only if needed (if parallel table is shown) */?>
table.parallel tr:first-child td:first-child {
	width: 30%;
}

table.parallel td:last-child {
	background-color: #<?php echo$colors['BG_PARALLEL'] ?>;
}

.grip {
	width: 20px;
	height: 30px;
	margin-top: -3px;
	background-image: url('<?php echo Yii::app()->baseUrl; ?>/images/grip.png');
	margin-left: -5px;
	position: relative;
	z-index: 88;
	cursor: e-resize;
}

.grip:hover {
	background-position-x: -20px;
}

.dragging .grip {
	background-position-x: -40px;
}
<?php echo UserSettings::getCurrentSettings()->tonesCss; ?>
/* ]]> */
</style>
</head>
<body>
	<div id="box"></div>
	<script type="text/javascript" language="JavaScript">init()</script>
	<?php if($prependText!==NULL) echo $prependText; ?>