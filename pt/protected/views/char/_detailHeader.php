<?php 
/**
* @var string $chardef
* @var boolean $noticePrimary 
*  				if the link for addition to primary system should be displayed 
* @var int $primarySystemID 
*/

if($totalCharCount>1) { ?>
<div class="charsection"><span class="char"><?php echo $chardef; ?></span></div>
<?php } 

if($noticePrimary && $primarySystemName!==null) {
	$iconsPath=Yii::app()->request->baseUrl.'/images/icons/';
	$linkText=CHtml::image($iconsPath.'add.png');
	$linkText.="add ";
	$linkText.=$chardef;
	$linkText.=" to ";
	$linkText.=$primarySystemName;
  echo '<p>'.CHtml::link($linkText, array('char/create', 'charDef'=>$chardef, 'system'=>$primarySystemID), array('class'=>'addToPrimary')).'</p>';		
} ?>