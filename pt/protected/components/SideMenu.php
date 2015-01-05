<?php

Yii::import('zii.widgets.CPortlet');

class SideMenu extends CPortlet
{
	public $name;
	public $data;
	
	public function init()
	{
// 		$this->title=CHtml::encode(Yii::app()->user->name);
// 		$this->title="Menu";
		$this->contentCssClass="sidemenu";
		parent::init();
	}

	protected function renderContent()
	{
		$this->render($this->name, $this->data);
	}
}