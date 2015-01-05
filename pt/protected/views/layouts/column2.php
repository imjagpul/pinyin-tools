<?php /* @var $this Controller */  
$cs=Yii::app()->clientScript;
$cssDir=Yii::app()->request->baseUrl.'/css/';
$cs->registerCssFile($cssDir.'layout-6-custom.css');
$this->beginContent('//layouts/main'); ?>
<div id="contentwrapper">
	<div id="maincolumn">
		<?php echo $content; ?>
	</div>
</div>

<div id="rightcolumn">
	<?php
	$this->widget('LoginFormWidget', array('loginFormModel' => (isset($this->loginFormModel) ? $this->loginFormModel : NULL)));
	$this->widget('SearchBoxWidget');
// 	$this->widget('DictionaryWidget', array('dictionaryQuery' => isset($this->dictionaryQuery) ? $this->dictionaryQuery->chardef : NULL));
	$this->widget('DictionaryWidget', array('dictionaryQuery' => $this->dictionaryQuery));

	if(isset($this->sideMenu)) {
		$this->widget('SideMenu', array('name' => $this->sideMenu, 'data' => $this->sideMenuData));
	}
	
  ?>
</div>
<?php $this->endContent(); ?>