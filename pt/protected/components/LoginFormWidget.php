<?php
// class LoginFormWidget extends CWidget 
// define('LOGINFORMWIDGET_ACTION_PREFIX', 'log.');
class LoginFormWidget extends CWidget 
{
	private $_loginFormModel;

// 	public static function getPrefix() {
// 		return LOGINFORMWIDGET_ACTION_PREFIX;
// 	}
	
// 	public function getActionPrefix() {
// 		return LOGINFORMWIDGET_ACTION_PREFIX;
// 	}
	
// 	public function __construct($owner=null) {
// 		parent::__construct($owner);
// 		$this->actionPrefix=LOGINFORMWIDGET_ACTION_PREFIX;
// 	}	
	
	public static function actions() {
		return array('Login'=>'application.components.actions.LoginAction');
	}
	
	public function setLoginFormModel($loginFormModel) {
		$this->_loginFormModel=$loginFormModel;
	}

	/**
	 * Displays the login page
	 */
// 	public function actionLog()
// 	{
// 		echo 'HELLO WORLD';
// 		$model=new LoginForm;
	
// 		// if it is ajax validation request
// 		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
// 		{
// 			echo CActiveForm::validate($model);
// 			Yii::app()->end();
// 		}
	
// 		// collect user input data
// 		if(isset($_POST['LoginForm']))
// 		{
// 			$model->attributes=$_POST['LoginForm'];
// 			// validate user input and redirect to the previous page if valid
// 			if($model->validate() && $model->login()) {
// 				$this->redirect(Yii::app()->user->returnUrl);
// 			}
// 		}
// 		display the login form
// 		$this->loginFormModel=$model;
// 		$this->render('login');
// 				$this->render('login',array('model'=>$model));
// 	}	
	public function run()
	{
		// this method is called by CController::endWidget()
		
		if(Yii::app()->user->isGuest) {
			//   	global $model;
			if(is_null($this->_loginFormModel)) {
				$model=new LoginForm;
			} else {
				$model=$this->_loginFormModel;
			}
		
			$this->render('_login',array('model'=>$model));
			 
// 			if(!is_null($this->_loginFormModel))
				//exploiting the fact that $this->loginFormModel is set only on in the login action in controller
// 				Yii::app()->user->setReturnUrl(Yii::app()->getRequest()->getUrl());
			
		} else {
			?>
			<div class="login">
		
				Logged in as <b><?php
				echo CHtml::link(CHtml::encode(Yii::app()->user->name), array("/user/profile"), array('class'=>'profilelink'));
			 //echo Yii::app()->user->name;
			 ?>
				</b> (<a href="<?php echo $this->owner->createUrl("/site/logout"); ?>">logout</a>).
			</div>
	
	<?php 
     }
					
	}
}