<?php

class SystemController extends Controller
{
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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'changeOwn', 'changeForeign', 'deleteDialog'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'diagnostics', 'duplicates', 'deleteDialog'),
				'roles'=>array('admin'),
// 					'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model=$this->loadModel($id);
		
		$this->layout='//layouts/column3';
		$this->sideMenu="systemsViewRightSidebar";
		$this->sideMenuData=$model;
		$this->secondSideMenu="systemsViewSidebar";
		$this->secondSideMenuData=$model;
		
		$this->render('view',array(
			'model'=>$model
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($msg=null)
	{
		$model=new System;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['System']))
		{
			$model->attributes=$_POST['System'];
			$model['master']=Yii::app()->user->id;
					
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
			'msg'=>$msg,
			'languagesList'=>Lookup::getAllLanguages(),
			'targetLanguagesList'=>Lookup::getTargetLanguages()
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		if(isset($_POST['System']))
		{
			$model->attributes=$_POST['System'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
			'languagesList'=>Lookup::getAllLanguages(),
			'targetLanguagesList'=>Lookup::getTargetLanguages(),
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
	 * Lists all systems (that are visible for the current user).
	 */
	public function actionIndex()
	{
		$this->layout='//layouts/column3';
		$this->secondSideMenu="systemsListSidebar";
		
		//if the user is not logged in, all public systems are listed
		//if the user is logged in, his systems are listed first and the public ones later second
		//(but we have to exclude own systems of the user from the public list)
		$dataProviderUser=NULL;
		
		//construct the condition clause
		$condition="visibility='visible'";
		if(!Yii::app()->user->isGuest) {
			$userId=Yii::app()->user->id;
			$condition.=" AND master!='$userId'";
			
			$dataProviderUser=new CActiveDataProvider('System', array(
					'criteria'=>array(
							'condition'=>"master='$userId'"
					)
			));
				
		}
		
		$dataProvider=new CActiveDataProvider('System', array(
			'criteria'=>array(
				'condition'=>$condition
		)
		));
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
			'dataProviderUser'=>$dataProviderUser,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new System('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['System']))
			$model->attributes=$_GET['System'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	
	public function actionDeleteDialog($id=NULL) {
		if($id==NULL) {
			if(isset($_POST['doIt']) && isset($_POST['id'])) {
				$id=(integer) $_POST['id'];
				$model=System::model()->findByPk($id);
				if(is_null($model) || !$model->isWriteable()) {
					throw new CHttpException(403,'You are not authorized to delete this system.');
				}
				
				//remove inheritance from other systems... (this might lead to changes in systems of other users...)
				SystemKeywordInheritance::model()->deleteAllByAttributes(array('inheritsFrom'=>$id));
				
				//remove all entries of this system
				Char::model()->deleteAllByAttributes(array('system'=>$id));
				
				//remove system itself
				$model->delete();
				//System::model()->deleteAllByAttributes($attributes)
			}
			$this->redirect(array('system/index'));
		}
		
		$model=System::model()->findByPk($id);

		if($model==NULL) {
			throw new CHttpException(404,'The requested system does not exist.');
		}
		$this->render('deleteDialog',array(
				'model'=>$model,
		));		
	}
	
	public function actionChangeForeign($id, $prop, $newValue) {
		settype($id, 'integer');
		
		$system=System::model()->findByPk($id);
		$userID=Yii::app()->user->getId();
		
		$userSettingsSystems=UserSettingsSystems::model()->findByPk(array('userId'=>$userID, 'systemId'=>$id));
		if($userSettingsSystems==NULL) {
			$userSettingsSystems=new UserSettingsSystems();
			$userSettingsSystems->userId=$userID;
			$userSettingsSystems->systemId=$id;
		}
		
		if($prop==="hidden") {
			if($newValue=="true")
				$userSettingsSystems->hide=1;
			else
				$userSettingsSystems->hide=0;
		} else if($prop==="favorite") {
			if($newValue=="true")
				$userSettingsSystems->favorite=1;
			else
				$userSettingsSystems->favorite=0;
		} else {
			throw new CHttpException(403,'Invalid property.');
		}
		 
		$userSettingsSystems->save();
		$this->redirect(array('system/index'));
	}
	
	public function actionChangeOwn($id, $prop, $newValue) {
		settype($id, 'integer');
		
		$system=System::model()->findByPk($id);
		$userID=Yii::app()->user->getId();
		
		//verify user access
		if($system->master!==$userID) {
			throw new CHttpException(403,'Invalid user.');
			Yii::app()->exit(); //@TODO implemnt full user permissons
		}
		
		if($prop!=="primary") {
			throw new CHttpException(403,'Invalid property.');
		}
		
		if($newValue=="true") {
			UserSettings::getCurrentSettings()->defaultSystem=$id;
			UserSettings::getCurrentSettings()->saveSettings();
			$this->redirect(array('system/index'));
		}
	}
	
	private function actionDiagnosticsMiddle() {
		$autoconvertible=array();
		$notMatching=array();
		$notTwoLines=array();
		$noTone=array();
		$noKw=array();
		$noComp=array();
		$manyComp=array();
		$unmatch=array();
		
		$allChars=Char::model()->findAllByAttributes(array('system'=>2));
		foreach($allChars as $char)
		{
			if(!empty($char->mnemo)) {
				$result=MnemoParser::suggestMiddleToNew($char);
				
				if($result===-1) {
					$notTwoLines[]=$char;
				} else if($result===-2) {
					$noTone[]=$char;
				} else if($result===-3) {
					$noKw[]=$char;
				} else if($result===-4) {
					$noComp[]=$char;
				} else if($result===-5) {
					$manyComp[]=$char;
				} else if($result===-6) {
					$unmatch[]=$char;
				} else if($result!=NULL) {
					$autoconvertible[]=$result;
				} else {
					$notMatching[]=$char;
				}				
			}
		}
			
		$this->render('diagnostics',array(
				'data'=>array(
						'Auto-convertible'=>$autoconvertible,
						'Not matches'=>$notMatching,
						'Not two lines'=>$notTwoLines,
						'No tone'=>$noTone,
						'No keyword'=>$noKw,
						'No components'=>$noComp,
						'Unmatched keyword'=>$unmatch,
						'Mutliple components'=>$manyComp
				)
		)
		);
	}
	
	public function actionDuplicates() {
		
		$compare=array(10, 6, 2, 14);
		$chardefLog=array();
		foreach($compare as $id) { 
			$allChars=Char::model()->findAllByAttributes(array('system'=>$id));
			
			foreach($allChars as $char) 
			{
				if(!empty($char->chardef)) {
					if(empty($chardefLog[$char->chardef]))
						$chardefLog[$char->chardef]=array($id);
					else
						$chardefLog[$char->chardef][]=$id;
				}
			}
		}
		
		$result=array();
		foreach($chardefLog as $chardef => $ids) {
			if(count($ids)>1) {
				$result[$chardef]=$chardef.implode("; ", $ids);
			}
		}
		
		$this->render('diagnostics',array(
				'data'=>array(
						'Duplicates'=>$result,
				)
		)
		);		
	}
	
	public function actionDiagnostics($id) 
	{
		ini_set('max_execution_time', 60000);
		 
		if($id==2) {
			$this->actionDiagnosticsMiddle();
			return;
		}
		
		
		
		$notMatching=array();
// 		$nocomponents=array();
		$other=array();
		$multiple=array();
// 		$needFix=array();
		$lessMatches=array();
		$moreMatches=array();
		$notFullyMatches=array();
		
		$allChars=Char::model()->findAllByAttributes(array('system'=>$id));
		foreach($allChars as $char) 
		{
			if(!empty($char->mnemo)) {
// 				$result=MnemoParser::parseOld($char->mnemo);
				$result=MnemoParser::suggestOldToNew($char);
				if($result===-1) {
					$needFix[]=$char;
				} else if($result===-2) {
					$lessMatches[]=$char;
				} else if($result===-3) {
					$moreMatches[]=$char;
				} else if($result===-4) {
					$notFullyMatches[]=$char;
				} else 				
				if($result!=NULL) {
					$other[]=$result;

// 					$char->mnemo=$result;
// 					$char->system=14;
// 					$char->update();
// 					$other[]="moved ".$char->chardef;
					
				//for first diagnosis
// 					$other[]="$result";
// 					$other[]=$char->chardef." ".$char->transcriptionAuto." $result";
// 					$other[]=$char->chardef." ".$char->transcriptionAutoNoTone." $result";
					
					//for output
// 					$other[]=$char->transcriptionAutoNoTone."\t$result";
				} else { //$result == NULL
					$notMatching[]=$char;
				}
				
			}
				
			/*
			if(!empty(strpos($char->mnemo, "lightning")!==false))
				$autoconvertible[]=$char;
				*/
			
			/*if(strpos($char->mnemo, "<br")!==false)
				$autoconvertible[]=$char;
				*/
			/*
			if(!empty($char->notes))
				$autoconvertible[]=$char;
			*/
			
			/*
			$result=CharDiagnostics::diagnose($char);
			if(in_array(CharDiagnostics::AUTOCONVERTIBLE, $result)) {
				$autoconvertible[]=$char;
				CharDiagnostics::autoconvert($char);
			} else if(count($char->components)===0) {
				$nocomponents[]=$char;
			} else {
				$other[]=$char;
			}
			*/
		}
		
		$this->render('diagnostics',array(
				'data'=>array(
// 						'Auto-convertible'=>$autoconvertible,
						'Other chars'=>$other,
						'More marked as matches'=>$moreMatches,
						'Less marked as matches'=>$lessMatches,
						'Not matching'=>$notMatching,
// 						'Need fix'=>$needFix,
						'Not fully matches'=>$notFullyMatches
// 						'All chars'=>$allChars,
			)
		)
		);
		
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return System the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=System::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param System $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='system-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
