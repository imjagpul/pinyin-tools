<?php
class LoginAction extends CAction {
	public function run(){
		$model=new LoginForm;		
		
		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	
		$redirect=!Yii::app()->user->isGuest;
		$returnUrl=Yii::app()->homeUrl;
		
		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$returnUrl=$_POST['returnUrl'];
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if(($model->validate() && $model->login())) {
				$redirect=true;
// 				$this->redirect(Yii::app()->user->returnUrl);
				//@TODO return url doesn't really work ( http://localhost/pt/s/%E7%A7%80 )
				
// 				Yii::app()->getRequest()->redirect(Yii::app()->user->returnUrl);
			}
		}
		if($redirect) {
			if($returnUrl==$this->getController()->createUrl('site/log.Login')) {
				$returnUrl=Yii::app()->homeUrl;
			}
				
			$this->getController()->redirect($returnUrl);
		}
		
		//at this point: invalid login attempt and no AJAX enabled
		
		// display the login form
		//@TODO refactor (after registration); does not work fine when logging from elsewhere (than SiteController)
		$this->getController()->loginFormModel=$model;
		$this->getController()->redirect($this->getController()->createUrl("user/login"));
		
// 		if(get_class($this->getController())=='SiteController') {
// 			$this->getController()->render('login');		
// 		} else {
// 			$this->getController()->redirect($this->getController()->createUrl("user/login"));
// 		}
// 		$this->getController()->render();		
	}	
}