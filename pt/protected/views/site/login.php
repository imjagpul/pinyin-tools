<?php
/* @var $this SiteController */
$this->pageTitle=Yii::app()->name . " - Login";
?>

Please login. If you don't have account yet,
<a href="<?php echo $this->createUrl("/site/register"); ?>">register</a>
(you just need to pick an username and password).
