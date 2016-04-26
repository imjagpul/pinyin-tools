<?php
/* @var $this CharController */
/* @var $dataProvider CActiveDataProvider */
/* @var $msg null|boolean */

//@TODO find a better way to get variables from a php data file (see CPhpAuth)
//@TODO data should be loaded in Controller

$userVariant=UserSettings::getCurrentSettings()->variant;
	
$simplified=($userVariant=='simplified_only' || $userVariant=='simplified_prefer');

if($msg) {
?>
<h1>Browse existing entries</h1>
Search for a concrete character by using the search box on the right, or use one of the lists on the left.
<?php 
}

?>

<?php if($criteria=='matthews') { ?>
<h1>Learning Chinese Characters </h1>
<?php 
require(Yii::getPathOfAlias('application.data.hsk-matthews').".php"); 
$this->echoCharLinksListMatthews($hsk_matthews); 

} else if($criteria=='hsk') { 
	if($simplified)
		require(Yii::getPathOfAlias('application.data.hskdata').".php");
	else
		require(Yii::getPathOfAlias('application.data.hskdata-trad').".php");
?>
<h2>Browse characters by HSK</h2>

<h3>HSK level 1</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk1_simp : $hsk1_trad); ?> 
<h3>HSK level 2</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk2_simp : $hsk2_trad); ?> 
<h3>HSK level 3</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk3_simp : $hsk3_trad); ?> 
<h3>HSK level 4</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk4_simp : $hsk4_trad); ?> 
<h3>HSK level 5</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk5_simp : $hsk5_trad); ?> 
<h3>HSK level 6</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk6_simp : $hsk6_trad); ?> 
<?php  
//echo "Total count: ".(count($hsk1_trad)+count($hsk2_trad)+count($hsk3_trad)+count($hsk4_trad)+count($hsk5_trad)+count($hsk6_trad));
} ?>

