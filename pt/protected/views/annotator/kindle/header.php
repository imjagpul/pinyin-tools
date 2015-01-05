<?php
$cs=Yii::app()->clientScript;
//generated css for tones (depending on user settings)
$cs->registerCss('tones', UserSettings::getCurrentSettings()->tonesCss);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=<?php echo $charset; ?>">
		<title>
		</title>
		<style type="text/css">  /*
			<![CDATA[ */
			a {text-decoration: none; color: #<?php echo $colors['FG'] ?>;}
			a.u {text-decoration: none; color: #<?php echo $colors['FG_UNTAGGED'] ?>;}    
			.tags {background-color: #<?php echo $colors['BG_TAGBOX'] ?>; font-size: large; }
			.ch {background-color: #<?php echo $colors['BG_BOX_CH'] ?>; font-size: x-large; }
            .pinyin {background-color: #<?php echo $colors['BG_TRANSCRIPTION'] ?>; font-size: large; }
            body {font-size: x-large; background-color: #<?php echo $colors['BG'] ?>;} 
			/* ]]> */
		</style>		
	</head>
	<body>
