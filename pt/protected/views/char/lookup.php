<?php
if(empty($models)) {
?>
No results found for '<?php echo $search; ?>'.
<?php 
} else {
$lastchardef=NULL;

	foreach($models as $model) {
		if($model->isEmpty())
			continue;
		
		$this->renderPartial('_detail',array('model'=>$model, 'lastchardef'=>$lastchardef));
		$lastchardef=$model->chardef;
	}
}
?>

