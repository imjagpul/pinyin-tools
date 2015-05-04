<?php 
/**
* @var string $chardef
* @var boolean $noticePrimary 
*  				if the link for addition to primary system should be displayed 
* @var int $primarySystemID 
*/
?>
<div class="char"><?php echo $chardef; ?></div>
<?php if($noticePrimary) { 
	echo '<p>'.CHtml::link("Add this character", array('char/create', 'charDef'=>$chardef, 'system'=>$primarySystemID)).' to your primary system.</p>';		
} ?>