<?php

class SiteController extends Controller
{
	public $layout='//layouts/column3';
	public $secondSideMenu='helpMenu';
// 	public $secondSideMenuData='helpMenu';
	public $defaultAction="page";
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return CMap::mergeArray(parent::actions(), array(
			// captcha action renders the CAPTCHA image displayed on the contact page
// 			'captcha'=>array(
// 				'class'=>'CCaptchaAction',
// 				'backColor'=>0xFFFFFF,
// 			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
				'defaultView'=>'whatsthis'
			),
				
			'log.'=>'application.components.LoginFormWidget',
		));
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
				'accessControl', // perform access control for CRUD operations
// 				'postOnly + delete', // we only allow deletion via POST request
		);
	}
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
				array('deny',  // logged in users don't have to login
						'actions'=>array('login'),
						'users'=>array('@'),
						'deniedCallback'=> array($this, 'redirectHome')
						),
// 				array('allow', // allow authenticated user to perform 'create' and 'update' actions
// 						'actions'=>array('create','update'),
// 						'users'=>array('@'),
// 				),
// 				array('allow', // allow admin user to perform 'admin' and 'delete' actions
// 						'actions'=>array('admin','delete'),
// 						'users'=>array('admin'),
// 				),
// 				array('deny',  // deny all users
// 						'users'=>array('*'),
// 				),
		);
	}	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the login page.
	 * 
	 * This action is only to show a full login page - the actual login is handled by 
	 * {@link LoginFormWidget} and {@link LoginAction}.
	 */
	public function actionRegister()
	{
	    $model=new User('register');
	
	    // uncomment the following code to enable ajax-based validation
	    /*
	    if(isset($_POST['ajax']) && $_POST['ajax']==='user-register-form')
	    {
	        echo CActiveForm::validate($model);
	        Yii::app()->end();
	    }
	    */
	
	    if(isset($_POST['User']))
	    {
	        $model->attributes=$_POST['User'];
	        if($model->validate())
	        {
	            // form inputs are valid, do something here
	            return;
	        }
	    }
	    $this->render('register',array('model'=>$model));
	}

	/**
	 * Displays the login page.
	 * 
	 * This action is only to show a full login page - the actual login is handled by 
	 * {@link LoginFormWidget} and {@link LoginAction}.
	 */
	public function actionLogin()
	{
		$this->render('login');
	}

	public function redirectHome($rule) { //contrary to the documentation this method takes only one parameter
// 	public function redirectHome($user, $rule) {
		$this->redirect(Yii::app()->homeUrl);
	}
	
	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		
		$returnUrl=Yii::app()->homeUrl;
		
		$ref=Yii::app()->getRequest()->urlReferrer;
		if(!is_null($ref)) //@TODO perhaps check if the url is not a "logged users only" link and use home url in that case 
			$returnUrl=$ref;
		
		$this->redirect($returnUrl);
	}
}