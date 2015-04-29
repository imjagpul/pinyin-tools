<?php
/* @var $this CharController */
/* @var $data Char */
?>

<div class="view charview">
<?php /* @TODO replace table design with CSS */ ?>
	<table>
	<tr>
	<td>
	<span class="charbox"><?php echo CHtml::encode($data->chardef); ?></span>
	</td>
	<td style="width:100%">
	<span class="mnemo"><?php if(strlen($data->mnemonicsHTML)>0) { ?><blockquote> <?php echo $data->mnemonicsHTML; ?></blockquote><?php } ?></span>
	</tr>
	<tr>
	<td>
	<div class="keyword"><?php echo CHtml::encode($data->keyword); ?></div>
	</td>
	<td>
	<div class="components"><?php
		$componentsText=array(); 
		foreach ($data->components as $comp) {
			$componentsText[]=$comp->subchar->keyword.' '.$comp->subchar->chardef;
// 			$componentsText[]=$comp->subchar->chardef;
		}
	echo CHtml::encode(implode(' + ',$componentsText)); 
	?></div>
	</td>
	</tr>
	</table>
</div>