<?php
/* @var $data System */

if($data->isWriteable()) {
	echo CHtml::link("Edit this system", array('system/update', 'id'=>$data->id));
}
