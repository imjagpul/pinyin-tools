<?php 
/**
* @var string $chardef
* @var boolean $noticePrimary 
*  				if the link for addition to primary system should be displayed 
* @var int $primarySystemID 
*/

?>

<?php  if($totalCharCount>1) { ?>
<div class="charsection"><span class="char"><?php echo $chardef; ?></span></div>
<?php } ?>

<?php if($noticePrimary) { 
// 	echo '<p>'.CHtml::link("Add this character", array('char/create', 'charDef'=>$chardef, 'system'=>$primarySystemID)).' to your primary system.</p>';		
} ?>