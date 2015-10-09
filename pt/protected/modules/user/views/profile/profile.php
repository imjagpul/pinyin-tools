<?php 
/* @var $this ProfileController */


//HEAVILY MODIFIED for PT

$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Profile");
$this->breadcrumbs=array(
	UserModule::t("Profile"),
);

$this->sideMenu="standardMenu";
$this->sideMenuData=array('menuTitle'=>'Options', 'data'=>array(
    array('label'=>UserModule::t('Change password'), 'url'=>array('changepassword')),
));
?>

<h1><?php echo UserModule::t('Your profile'); ?></h1>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
	<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>

<?php 
//Display a message in correspondence to how many entries the user has made yet.
//1. No own systems created.
//2. Only an empty system created.
//3. Non empty system created.

$userId=Yii::app()->user->id;
$systems=System::model()->findAll("master='$userId'");

//$firstSystem=System::model()->find("master='$userId'");

if(is_null($systems) || empty($systems)) {?>
<p>
	You have not added any mnemonics yet. You can <a
		href="<?php echo $this->createUrl('/char/create'); ?>">add an entry
		now</a>, or you can just <a
		href="<?php echo $this->createUrl('/char/index'); ?>">browse</a>
	existing entries or take a look at the <a
		href="<?php echo $this->createUrl('/annotator'); ?>">annotator</a>.
	You might want to take a look at the Learning Chinese Characters
	system. You can add an entry now</a>.

<?php /* or read about mnemonics. - link to manual */?>

</p>
<?php } else {
	$allEmpty=true;
	
	foreach ($systems as $s) {
		if ($s->ownEntriesCount > 0) {
			$allEmpty = false;
			break;
		}
	}
	
	if($allEmpty) {
?> 
You have created a system, now you can start <a href="<?php echo $this->createUrl('/char/create'); ?>">adding notes</a>.
<?php 
	} else {
?> 
 You can <a href="<?php echo $this->createUrl('/system/index'); ?>">browse your systems</a> or use the <a
		href="<?php echo $this->createUrl('/annotator'); ?>">annotator</a> to read any text with your mnemonics.
<?php 
	}
}


?>


