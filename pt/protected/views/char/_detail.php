<?php 
/**
* @var Char $model 
*/

if($lastchardef!==$model->chardef) { /*do not output this header several times if equal entries are displayed*/ 
	?><div class="char"><?php echo $model->chardef; ?></div><?php 
} 

 
?>
<h2><?php echo $model->systemName; 

//output edit button (if this system is editable for the logged in user)
if(System::isSystemWriteable($model->system)) {//@TODO refactor auth using business rules
	?><span class="editlink">[<a href="<?php echo $this->createUrl('char/update', array('id'=>$model->id)); ?>">Edit</a>]</span><?php
}
?></h2>
				
<?php if(!empty($model->keyword)) { ?>
<div class="keyword"><?php echo $model->keyword; ?></div><!--<?php echo $model->id; ?>-->
<?php } ?>

<?php if(!empty($model->mnemo)) { ?>
<div class="mnemo"><blockquote><?php 
echo $model->mnemonicsHTML; ?></blockquote></div>
<?php } ?>

<?php if(!empty($model->notes)) { ?>
<div class="notes"><?php echo $model->notes; ?></div>
<?php } ?>

<?php if(!empty($model->notes2)) { ?>
<div class="notes"><?php echo $model->notes2; ?></div>
<?php } ?>

<?php if(!empty($model->notes3)) { ?>
<div class="notes"><?php echo $model->notes3; ?></div>
<?php } ?>

<?php 

$c=$model->components;
if(!empty($c)) { ?>
<div class="composition">
<b>Components:</b>
<ul>
<?php 
 foreach($c as $s) {
	for($i=0;$i<$s->count;$i++) {
	$subchar=$s->subchar;
// ?>
 <li><a href="<?php 
 echo $this->createUrl("char/lookup", array('s'=>$subchar->chardef)); 
 ?>"> <span class="cn"><?php echo $subchar->chardef; ?></span><?php echo $subchar->keyword; 
 ?></a></li>
 <?php 
}}?>
</ul>
</div>
<?php  

}
?>