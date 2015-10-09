<?php
if(isset($menuTitle)) {
	echo $menuTitle;
}

/** Uses common CMenu. */
$this->widget('zii.widgets.CMenu', array('items'=>$data, 'htmlOptions'=>array('id'=>'navmainlist')));
?>
