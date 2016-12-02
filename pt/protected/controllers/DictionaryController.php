<?php

class DictionaryController extends Controller
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
			array('allow', // admin can do all actions
				'roles'=>array('admin'),
			),
			array('deny',  // nobody else can do anything
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
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Dictionary;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Dictionary']))
		{
			$model->attributes=$_POST['Dictionary'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
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

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Dictionary']))
		{
			$model->attributes=$_POST['Dictionary'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
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
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Dictionary');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionJson($id, $simplified)
	{
		settype($id, 'integer');
		
		//check if it is cached
		$jsonText=Yii::app()->cache->get($id);
		if($jsonText!==false)
		{
			echo $jsonText;
			return;
		}
		
		$connection=Yii::app()->db;
		
		if($simplified) {			
			$sql='SELECT simplified,transcription,translation FROM dict_entry_char WHERE dictionaryId=:dictionaryId';
		} else {
			$sql='SELECT traditional,transcription,translation FROM dict_entry_char WHERE dictionaryId=:dictionaryId';
		}
		$command=$connection->createCommand($sql);
		$command->bindValue(':dictionaryId', $id);
		$dataChar=$command->query();
		
		if($simplified) {
			$sql='SELECT simplified_begin,simplified_rest,transcription,translation FROM dict_entry_phrase WHERE dictionaryId=:dictionaryId';
		} else {
			$sql='SELECT traditional_begin,traditional_rest,transcription,translation FROM dict_entry_phrase WHERE dictionaryId=:dictionaryId';
		}
		$command=$connection->createCommand($sql);
		$command->bindValue(':dictionaryId', $id);
		$dataPhrase=$command->query();

		$jsonText=$this->renderPartial('json',array(
				'simplified'=>$simplified,
				'dataChar'=>$dataChar,
				'dataPhrase'=>$dataPhrase
		), true);
		
		Yii::app()->cache->set($id,$jsonText,0,new CDbCacheDependency("SELECT lastchange FROM dictionary WHERE id=$id"));
		echo $jsonText;
	}
	
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Dictionary('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Dictionary']))
			$model->attributes=$_GET['Dictionary'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Imports data to the given dictionary.
	 * Note this always erases to present data. 
	 */
	public function actionImport()
	{
		
		ini_set('max_execution_time', 1200);
		
		if(isset($_GET['id'])) {
			$id=$_GET['id'];
			settype($id, "integer");
			
			$dictModel=$this->loadModel($id);
				
			$this->render('import',array(
					'model'=>$dictModel,
			));
		} else if(isset($_POST['id']) && isset($_FILES['upfile'])) {
			$id=$_POST['id'];
			settype($id, "integer");
			
			//check errors
			if($_FILES['upfile']['error']!=UPLOAD_ERR_OK) {
				if($_FILES['upfile']['error']==UPLOAD_ERR_INI_SIZE || $_FILES['upfile']['error']==UPLOAD_ERR_FORM_SIZE)
					throw new CHttpException(400,'File upload error. (File too large.)');
				
				if($_FILES['upfile']['error']==UPLOAD_ERR_NO_FILE)
					throw new CHttpException(400,'File upload error. (No file uploaded.)');
				
				throw new CHttpException(400,'File upload error. (errcode='.$_FILES['upfile']['error'].')');
			}
			
			
			//get the contents of the file
// 			$content=file_get_contents($tmpfile);
			
			//save to db
			$dictModel=$this->loadModel($id);
			$dictModel->truncate();
			$msg='Imported successfully. ';
			
			//have it parsed
			$uploadedFile=new UploadedFile('upfile', "#");
			$r=DictionaryFileParser::parseAndAdd($uploadedFile, $dictModel);
			if($r!==false) {
				$c=count($r);
				$msg.="$c invalid lines were skipped.";
				$msg.='<!--';
				$msg.=$r[0];
				$msg.='-->';
			}
			$tmpfile=$_FILES['upfile']['tmp_name'];
			unlink($tmpfile); //delete the temporary file
			
			//update timestamp of the dictionary
			$dictModel->lastchange=time();
			$dictModel->save();
			
			$msg.="Took ". (microtime(true)-YII_BEGIN_TIME)."s";
			
			$this->render('import',array(
					'model'=>$dictModel,
					'msg'=>$msg
			));
				
		} else {
			//no ID specified
			//@TODO redirect to index
			throw new CHttpException(400,'Your request is invalid.');
		}
	}
	
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Dictionary the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Dictionary::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Dictionary $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dictionary-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
