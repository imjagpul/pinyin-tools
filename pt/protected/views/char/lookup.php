<?php
/**
 * Please see @link CharController#actionLookup() for details about the values.
 * 
 * @var boolean $empty
 * @var Char[][][] $modelsSorted
 * @var string $search
 * @var int $primarySystemID
 * @var string $primarySystemName
 */


if($empty) {
?>
No results found for '<?php echo $search; ?>'.
<?php 
} else {


foreach($modelsSorted as $chardef=>$modelsSortedSub) {
	//output header for this char
	$this->renderPartial('_detailHeader',
			array(	'chardef'=>$chardef, 
					'noticePrimary'=>empty($modelsSortedSub[SYSTEM_STATUS_PRIMARY]), 
					'primarySystemID'=>$primarySystemID, 
					'primarySystemName'=>$primarySystemName,
					'totalCharCount'=>count($modelsSorted)));
	
	foreach($modelsSortedSub as $systemFlag=>$models)
		foreach($models as $model) {
			if($model->isEmpty())
				continue;
			
			$this->renderPartial('_detail',array('model'=>$model, 'systemFlag'=>$systemFlag));
		}
	}	
}	
?>

