<?php

define('SYSTEM_STATUS_PRIMARY', 0);
define('SYSTEM_STATUS_FAVORITE', 1);
define('SYSTEM_STATUS_OWN', 2);
define('SYSTEM_STATUS_NOT_HIDDEN', 3);


class CharController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
// 	public $layout='//layouts/column2';

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
				'actions'=>array('index','view', 'lookup', 'suggest', 'suggestComposition', 'suggestCompositions', 'suggestSystemChanged', 'heisig', 'matthews', 'radicals', 'bySystem', 'browse'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'autocorrect'),
				'roles'=>array('admin'),
// 				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		));
	}

// 	public function actions() {
// 		return CMap::mergeArray(parent::actions(), array(
// 				'browse-hsk'=>'action',
				
// 				));
						
// 	}
	
	public function actionAutocorrect($id) {
		$this->render('view',array(
				'model'=>$this->loadModel($id),
				'special'=>"autocorrect"
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
	 * Looks up entries by either keyword or chardef.
	 * @param string $s
	 *   			keyword or chardef of the entries to be displayed
	 */
	public function actionLookup($s)
	{
		//$s can be either keyword or chardef
		$s=trim($s); //trim silently
		
		if(empty($s)) {
			$this->actionIndex();
			return;
		}
		//we need results from this users own systems and from all public (except those hidden by this user) 
		
		$lookupSystems=System::getLookupSystems();
		
		$criteria=new CDbCriteria(); 
		$criteria->compare('chardef',"=$s", false, "OR"); 
		$criteria->compare('keyword',"=$s", false, "OR");
		//@TODO add (and test) "with systems" clause
		
		$criteria->addInCondition('system', CHtml::listData($lookupSystems, 'id', 'id'));
		$models=Char::model()->findAll($criteria);

		if(count($models)==0) {
			//set the dictionary query
			$this->dictionaryQuery=$s; //nothing found - so maybe it is an unknown character, try searching it
			//render the notice
			$this->render('lookup',array('empty'=>true, 'search'=>$s));
			return;
		}
		
		//group by chardef, then by system
		//in this order:
		//primary, favorite, own, public and unlisted (except those hidden by this user)

		//$modelsSorted:
		//three dimensional array: first array is indexed by chardef, 
		//index in second array: 0 - primary, 1 - favorite, 2 - own, 3 - not hidden
		//(as defined in respective constants)   
		$modelsSorted=array();
		
		$primarySystemID=UserSettings::getCurrentSettings()->defaultSystem;
		if($primarySystemID!==NULL) {
			$primarySystem=System::model()->findByPk($primarySystemID);
		}
		$primarySystemName=isset($primarySystem)?$primarySystem->name:NULL;
		
		foreach($models as $model) {
			$key=$model->chardef;
			$system=$model->systemValue;
			
			if(!array_key_exists($key, $modelsSorted)) $modelsSorted[$key]=array();
			
			if($system->isHidden()) {
				continue;
			}
			
			if($system->id==$primarySystemID) { //primary
				$modelsSorted[$key][SYSTEM_STATUS_PRIMARY][]=$model;
			} else if($system->isFavorite()) { //favorite
				$modelsSorted[$key][SYSTEM_STATUS_FAVORITE][]=$model;
			} else if($system->master==Yii::app()->user->getId()) { //own
				$modelsSorted[$key][SYSTEM_STATUS_OWN][]=$model;
			} else { //public or unlisted
				//note the visibility is checked in the sql query already
				//and the hidden status is checked above
				$modelsSorted[$key][SYSTEM_STATUS_NOT_HIDDEN][]=$model;
			}
		}
		
		//set the dictionary query
		$this->dictionaryQuery=$s; //nothing found - so maybe it is an unknown character, try searching it
		
		$this->render('lookup',array(
				'empty'=>false, 'modelsSorted'=>$modelsSorted, 'search'=>$s, 'primarySystemID'=>$primarySystemID, 'primarySystemName'=>$primarySystemName
		));
	}
	
	private function handleComponents($model) {
		if($model->id===NULL) {
			throw new Exception('Given model has to have the id already set.');
		} 
		
		//update components
		$editComponents=array();
		
		if(isset($_POST['components'])) { //convert to a keymap and make sure it's all integers
			foreach ($_POST['components'] as $c) {
				$c=(int)$c;
				if(isset($editComponents[$c]))
					$editComponents[$c]++;
				else
					$editComponents[$c]=1;
			}
		}
		//@TODO check the access rights here
		//@TODO check the integrity of the components (if they belong to a parent system)
			
		foreach($model->components as $component) {
			if(isset($editComponents[$component->subcharId])) {
				//update counts where necessary
				$newCount=$editComponents[$component->subcharId];
				if($newCount!==$component->count) { //check if the count matches
					$component->count=$newCount;
					$component->save();
				}
				unset($editComponents[$component->subcharId]);
			} else {
				//a component was deleted
				$component->delete();
			}
		}
		foreach($editComponents as $newSubcharId => $newCount) {
			//a new component has to be added
			$newComposition=new Composition();
			$newComposition->charId=$model->id;
			$newComposition->subcharId=$newSubcharId;
			$newComposition->count=$newCount;
			
			$newComposition->insert();
		}
		
	}
	
	/**
	 * Handles a submited Char form.
	 * @param Char $model
	 */
	private function handleChar($model) {
		
		if(!empty($_POST['commponentSuggest'])) {
			//the add (component) button was clicked (javascript disabled on client)
			//@TODO implement
			//add a row to the table but do not save to DB yet
			//(we need to trim CompositionsEditor -> DataProvider; or the $model)
			echo "non-javascript interface is not yet implemented, sorry. Please enable your javascript.";
			Yii::app()->end();
		} else if(isset($_POST['Char'])) {
			$model->attributes=$_POST['Char'];
			
			if($model->save()) {
				$this->handleComponents($model);
				$this->redirect(array('view','id'=>$model->id));
			}
		}
	}
	
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate($charDef=NULL, $system=NULL)
	{
		$model=new Char;
		
		$this->handleChar($model);
		
		if(!is_null($charDef))
			$model->chardef=$charDef;
		
		$systemList=System::getWriteableSystems();
		
		//check if the user has any systems created
		if(empty($systemList)) {
			//TODO improve constants handling
			$this->redirect(array('system/create','status'=>1));
			//$this->redirect(array('system/create','status'=>CREATE_SYSTEM_ADD_CHAR));
		}
		
		//decide which system are we adding to (the default choice in the listbox)
		if(is_null($system)) {
			//if no system given explicitly, choose the primary system
			$primarySystemID=UserSettings::getCurrentSettings()->defaultSystem;
			if(!is_null($primarySystemID))
				$system=$primarySystemID;
			else // no primary system - just pick the first one
				$system=$systemList[0]->id;
		}
		$model->system=(int) $system;
		
		
		$this->render('create',array(
			'model'=>$model, 'systemList'=>$systemList
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id); //TODO with compomentns; if !isset($_POST['Char']) && !empty($_POST['commponentSuggest']) also the subchars 

		$this->dictionaryQuery=$model->chardef;
		
		$this->handleChar($model);
	
		$systemList=System::getWriteableSystems();
		
		$this->render('update',array(
			'model'=>$model, 'systemList'=>$systemList
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
	
	function echoCharLinksListMatthews($list) {
		echo "<ul>";
		foreach($list as $num => $char) {
			?><li><?php echo $num; ?>: <a href="<?php echo $this->createUrl('char/lookup', array('s'=>$char))?>"><?php echo $char; 
			?></a></li><?php
		}
		echo "</ul>";
	}
	function echoCharLinksList($list) {
		foreach($list as $char) {
			?><a href="<?php echo $this->createUrl('char/lookup', array('s'=>$char))?>"><?php echo $char; ?></a><?php
		}
	}

	
	public function actionHeisig() { $this->actionIndex('heisig'); }
	public function actionRadicals() { $this->actionIndex('radicals'); }
	public function actionMatthews() { $this->actionIndex('matthews'); }
	
	/**
	 * Lists all entries in a systematic way.
	 */
	public function actionIndex($criteria='hsk', $msg=null)
	{
		$this->layout='//layouts/column3';
		$this->secondSideMenu="browseCharsSidebar";
		
		$dataProvider=new CActiveDataProvider('Char');
		
		$this->render('index',array(
			'criteria'=>$criteria,
			'msg'=>$msg,
			'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * The landing page for browsing of the characters.
	 * 
	 * Explain how to find entries for other characters.
	 */
	public function actionBrowse() {
		$this->actionIndex('hsk', true);
	}

	public function actionBySystem($id) {
		$dataProvider=new CActiveDataProvider('Char', array(
	    'criteria'=>array(
	        'condition'=>'system='.$id,
// 	        'order'=>'create_time DESC',
// 	        'with'=>array('author'),
	    ),
// 	    'countCriteria'=>array(
// 	        'condition'=>'status=1',
// 	    ),
	    'pagination'=>array(
	        'pageSize'=>20,
	    ),
		));
		
		$this->render('bySystem',array(
				'systemName'=>System::model()->findByPk($id)->name,
				'dataProvider'=>$dataProvider,
		));
	}
	
	/**
	 * Lists all models.
	 */
// 	public function actionSearch()
// 	{
// 		$dataProvider=new CActiveDataProvider('Char');
// 		$this->render('index',array(
// 				//@TODO NOW add cieterium
// 				'dataProvider'=>$dataProvider,
// 		));
// 	}
	
	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Char('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Char']))
			$model->attributes=$_GET['Char'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Char the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Char::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Char $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='char-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public function actionSuggest($system, $chardef) { //called when the chardef field changes
		//until now, this is normally called only by AJAX
		$system=(int)$system;
		$suggest=new Suggestion();
		$success=$suggest->fill($system, $chardef, $this);
		
		if(!$success)
			return;
		
// 		$suggest->keyword="asdf: $system";		
// 		$suggest->mnemo="cd: $chardef";
// 		$suggest->compositions;
// 		$suggest->dict;
		
		echo $suggest->encode(); //output as JSON		
	}
	
	
	private function querySingleComposition($allInheritedIds, $newcomp, $exact=FALSE) {
		//query the systems, find all possibilities
		$criteria=new CDbCriteria();
		$criteria->addInCondition('system', $allInheritedIds);
		$criteria->compare('chardef', $newcomp, !$exact, "AND");//partial match - @TODO check if not too slow
		if(!$exact)
			$criteria->compare('keyword', $newcomp, true, "OR");
		$models=Char::model()->findAll($criteria);
			
		$formatted=array();
		foreach($models as $model) {
			$formatted[]=array($model->chardef, $model->keyword, $model->systemValue->name, $model->id);
		}
		return $formatted; 
	}
	/**
	 * 
	 * @param int $system
	 * @param string $newcomp
	 */
	public function actionSuggestComposition($system, $newcomp) {//called by the Add button (#commponentSuggest)
		$allInheritedIds=System::model()->findByPk((int)$system)->allInheritedIds;
		echo CJSON::encode($this->querySingleComposition($allInheritedIds, $newcomp, false));
	}

	public function actionSuggestCompositions($system, $comps) { //called by the Suggestions select box (#suggestions)
		$allInheritedIds=System::model()->findByPk((int)$system)->allInheritedIds;
		$comps=CJSON::decode($comps);
		$result=array();
		foreach($comps as $c) {
			$result[]=$this->querySingleComposition($allInheritedIds, $c, true);
		}		
		echo CJSON::encode($result);
	}
		
	public function actionSuggestSystemChanged($system, $components) { //called when the select system changes (#Char_system)
		$systemValue=System::model()->findByPk((int)$system);

		$result=array();
		
		//mnemo editor
		$result['mnemoeditor']=MnemonicsEditor::create($systemValue, $this)->createEditable();
		
		//components editor
		//@TODO implement the following id:COMPONENTS-EDITOR
// 		$result['compositions'];

		echo CJSON::encode($result);
	}
}
