<?php
/* @var $this CharController */
/* @var $dataProvider CActiveDataProvider */

// $this->breadcrumbs=array(
// 	'Chars',
// );

// $this->menu=array(
// 	array('label'=>'Create Char', 'url'=>array('create')),
// 	array('label'=>'Manage Char', 'url'=>array('admin')),
// );

//@TODO find a better way to get variables from a php data file (see CPhpAuth)
//@TODO data should be loaded in Controller
require(Yii::getPathOfAlias('application.data.hskdata').".php"); 
require(Yii::getPathOfAlias('application.data.hsk-matthews').".php"); 
// Yii::import("application.data.hskdata"); //this doesnt export global varialbes
// global $hsk1_simp;
$simplified=true; //@TODO remove hardcoded preference
// echo count($hsk1_simp);
?>

<?php if($criteria=='matthews') { ?>
<h1>Learning Chinese Characters </h1>
<?php $this->echoCharLinksListMatthews($hsk_matthews); ?>

<?php } else if($criteria=='hsk') { ?>
<h2>Browse characters by HSK</h2>

<h3>HSK level 1</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk1_simp : $hsk1_trad); ?> 
<h3>HSK level 2</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk2_simp : $hsk2_trad); ?> 
<h3>HSK level 3</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk3_simp : $hsk3_trad); ?> 
<h3>HSK level 4</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk4_simp : $hsk4_trad); ?> 
<?php  } ?>

