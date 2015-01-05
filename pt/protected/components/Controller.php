<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
// 	public $layout='//layouts/column3';
// 	public $layout='//layouts/column2';
// 	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
	//data for the widgets. I am not sure if this is the best way to pass them, though.
	public $loginFormModel;
	/** @var string */
	public $dictionaryQuery=NULL;
	public $sideMenu;
	public $sideMenuData;
	
	public $secondSideMenu;
	public $secondSideMenuData;
	
	public function actions() {
		return array(
				'log.'=>'application.components.LoginFormWidget'
				);
	}
	
	public function accessRules()
	{
		return array(
				array('allow',  // allow all users to login
				'actions'=>array('log.login'),
				'users'=>array('*')
		));
	}
}