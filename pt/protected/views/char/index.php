<?php
/* @var $this CharController */
/* @var $dataProvider CActiveDataProvider */
/* @var $msg null|boolean */

//@TODO find a better way to get variables from a php data file (see CPhpAuth)
//@TODO data should be loaded in Controller

require(Yii::getPathOfAlias('application.data.hskdata').".php"); 
require(Yii::getPathOfAlias('application.data.hsk-matthews').".php"); 

$simplified=true; //@TODO remove hardcoded preference

if($msg) {
	echo 'Search for a concrete character by using the search box on the right, or use one of the lists on the left.';
}

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
<h3>HSK level 5</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk5_simp : $hsk5_trad); ?> 
<h3>HSK level 6</h3> 
<?php $this->echoCharLinksList($simplified ? $hsk6_simp : $hsk6_trad); ?> 
<?php  } ?>

