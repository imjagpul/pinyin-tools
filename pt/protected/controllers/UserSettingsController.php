<?php

class UserSettingsController extends Controller
{
	
	/**
	 * @var string sets the default action to be 'update'
	 */
	public $defaultAction='update';
	
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return CMap::mergeArray(parent::accessRules(), array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('update', 'index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		));
	}

	/**
	 * Updates the settings of the current user (even if he is a guest).
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		$model=UserSettings::getCurrentSettings();
		//$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['toneColor1'])) //note this is not the Yii standard input parsing
		{
			//$model->attributes=$_POST['UserSettings'];
			
				//throw new Exception("$name = $val");
			foreach($model->colorNames as $name) {
				
				if(!isset($_POST[$name])) continue;
				
				$val=Utilities::parseColorAsHex($_POST[$name]);
				
				//ignore invalid values
				if(is_null($val)) continue;
				
				
				$model->$name=$val;
			}
			
			//parse variant (allow only permitted values)
			if(isset($_POST['variant'])) {
				if($_POST['variant']=='simplified_only') $model->variant='simplified_only';
				else if($_POST['variant']=='traditional_only') $model->variant='traditional_only';
				else if($_POST['variant']=='simplified_prefer') $model->variant='simplified_prefer';
				else if($_POST['variant']=='traditional_prefer') $model->variant='traditional_prefer';
			}
			
			$model->save();
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new UserSettings('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['UserSettings']))
			$model->attributes=$_GET['UserSettings'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return UserSettings the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=UserSettings::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param UserSettings $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-settings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
