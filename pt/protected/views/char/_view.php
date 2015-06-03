<?php
/* @var $this CharController */
/* @var $data Char */

$system=$data->systemValue;
if(!is_null($system->transcriptionData)) 
	$formatter=FormattersFactory::getFormatterForDictionaryWidget($system->transcriptionData->text);
?>

<div class="view charview">
<?php /* @TODO replace table design with CSS */ ?>
	<table>
	<tr>
	<td>
	<?php echo CHtml::link(CHtml::encode($data->chardef), array('char/view', 'id'=>$data->id), array('class'=>'charbox')); ?>
	<?php if(isset($formatter)) { echo $formatter->format($data->transcriptionAuto); } ?>
	<div class="keyword"><?php echo CHtml::encode($data->keyword); ?></div>
	</td>
	<td style="width:100%">
	<?php if(!empty($data->components)) { ?>
	<div class="components"><?php
		$componentsText=array(); 
		foreach ($data->components as $comp) {
			for($i=0; $i<$comp->count; $i++) {
// 				$componentsText[]=$comp->subchar->keyword.' '.$comp->subchar->chardef;
				$linkText=CHtml::encode($comp->subchar->keyword);
				$linkText.=' <span class="cn">'.CHtml::encode($comp->subchar->chardef).'</span> ';
// 				$linkText=CHtml::encode($comp->subchar->keyword.' '.$comp->subchar->chardef);
				$componentsText[]=CHtml::link($linkText, array("char/lookup", 's'=>$comp->subchar->chardef));
// 				$componentsText[]=$comp->subchar->keyword.' '.$comp->subchar->chardef;
// 			$componentsText[]=$comp->subchar->chardef;
			}
		}
	echo implode(' + ',$componentsText); 
// 	echo CHtml::encode(implode(' + ',$componentsText)); 
	?></div>
	<?php } ?>
	
	<span class="mnemo"><?php if(strlen($data->mnemonicsHTML)>0) { ?><blockquote> <?php echo $data->mnemonicsHTML; ?></blockquote><?php } ?></span>
	
	<?php if(!empty($data->notes)) { ?>
<div class="notes"><?php echo $data->notes; ?></div>
<?php } ?>

<?php if(!empty($data->notes2)) { ?>
<div class="notes2"><?php echo $data->notes2; ?></div>
<?php } ?>

<?php if(!empty($data->notes3)) { ?>
<div class="notes"><?php echo $data->notes3; ?></div>
<?php } ?>
	
	</tr>
	</table>
</div>