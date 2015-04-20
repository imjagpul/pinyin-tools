<?php

/**
 * This is the model class for table "system".
 *
 * The followings are the available columns in table 'system':
 * @property integer $id
 * @property string $name
 * @property string $shortdescription
 * @property string $description
 * @property integer $master
 * @property string $mnemosystem
 * @property string $visibility
 * @property integer $language
 * @property integer $targetLanguage
 * @property integer $transcription
 *
 * The followings are the available model relations:
 * @property Char[] $chars
 * @property Soundword[] $soundwords
 * @property User $masterUser
 * @property SystemKeywordInheritance[] $keywordParents
 * @property SystemKeywordInheritance[] $keywordChildren
 * @property UserSettingsSystems[] $userSettingsSystems
 * @property Lookup $languageData
 * @property Lookup $targetLanguageData
 * @property Lookup $transcriptionData
 */
class System extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('language, targetLanguage', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
// 			array('description', 'length', 'max'=>2048),
// 			array('visibility', 'length', 'max'=>9),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, mnemosystem, visibility, language, targetLanguage', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'chars' => array(self::HAS_MANY, 'Char', 'system'),
			'soundwords' => array(self::HAS_MANY, 'Soundword', 'system'),
			'masterUser' => array(self::BELONGS_TO, 'User', 'master'),
			'keywordParents' => array(self::HAS_MANY, 'SystemKeywordInheritance', 'system'),
			'keywordChildren' => array(self::HAS_MANY, 'SystemKeywordInheritance', 'inheritsFrom'),
			'userSettingsSystems' => array(self::HAS_MANY, 'UserSettingsSystems', 'systemId'),
			'languageData' => array(self::BELONGS_TO, 'Lookup', 'language'),				
			'targetLanguageData' => array(self::BELONGS_TO, 'Lookup', 'targetLanguage'),				
			'transcriptionData' => array(self::BELONGS_TO, 'Lookup', 'transcriptionData'),				
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'System name',
			'description' => 'Description',
			'shortdescription' => 'Short description',
			'master' => 'Master',
			'mnemosystem' => 'Mnemosystem',
			'visibility' => 'Visibility',
			'language' => 'Mnemos language',
			'targetLanguage' => 'Target language',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('master',$this->master);
		$criteria->compare('mnemosystem',$this->mnemosystem,true);
		$criteria->compare('visibility',$this->visibility,true);
		$criteria->compare('language',$this->language);
		$criteria->compare('targetLanguage',$this->targetLanguage);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return System the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	
// 	public static function mapToIdName($modelsList)
// 	{
// 		$resultArray=array();
// 		foreach($modelsList as $model)
// 			$resultArray[$model->id]=$model->name;
// 		return $resultArray;
// 	}

	public function getAllInheritedIds(&$mergeWith=array()) {
		if(isset($mergeWith[$this->id])) //already included, skip (prevents loops)
			return;
		
		$mergeWith[$this->id]=$this->id; //add this system

		foreach ($this->keywordParents as $parent) {
			//recursively add all parent systems
			$parent->inheritsFromSystem->getAllInheritedIds($mergeWith);
		}
		
		return $mergeWith; 
	}
	
	public function getTranscriptionName() {
		return Lookup::model()->findByPk($this->transcription)->text;		
// 		$trans=$this->transcriptionData;
// 		return $trans->text;
	}
	
	public function getShortenedDescription() {
		if(!empty($this->shortdescription)) 
			return $this->shortdescription;
		else  {
			return strip_tags($this->description); //@TODO truncate
		}
	}
	
	private static $systemNames=array(); //cache 
	public static function getSystemName($id) {
		if(!array_key_exists($id, self::$systemNames)) {
			$s=System::model()->findByPk($id);
			if(is_null($s))
				Yii::log("System name for $id cannot be found.", 'error');
			self::$systemNames[$id]=$s->name;
		}
		return self::$systemNames[$id];
	}
	
	/**
	 * 
	 * @param integer $id
	 * @return boolean
	 * 		true if the given system is writeable for the current user 
	 */
	public static function isSystemWriteable($id) {
		$userId=Yii::app()->user->id;
		
		if(is_null($userId)) //guest user cannot write into any systems
			return false;
		
		return NULL!==System::model()->findByAttributes(array('id'=>$id, 'master'=>$userId));
	}
	
	/**
	 * 
	 * @return boolean
	 * 		true if the given system is writeable for the current user 
	 */
	public function isWriteable() {
		$userId=Yii::app()->user->id;
		
		return !(is_null($userId)) && $this->master===$userId;
	}
	
	/**
	 * Gets a list of systems the current user has permissions to write to.
	 * @return array
	 */
	public static function getWriteableSystems() {
		$id=Yii::app()->user->id;
	
		if(is_null($id)) //guest user cannot write into any systems
			return null; 

		$result=System::model()->findAllByAttributes(array('master'=>$id));
		
		return $result;
	}	

	/**
	 * Gets a list of systems the current user has permissions to read from.
	 * @return array
	 */
	public static function getReadableSystems() {
		$id=Yii::app()->user->id;
		
		//@TODO system hidding
		$result=System::model()->findAll();
		
		return $result;
	}
	static $mnemoFormatterCache=array();
	public static function getMnemoFormatter($systemId) {
		if(isset(self::$mnemoFormatterCache[$systemId])) {
			return self::$mnemoFormatterCache[$systemId];
		}
		
		$system=System::model()->findByPk($systemId);
		$mnemosystem=$system->mnemosystem;
		self::$mnemoFormatterCache[$systemId]=FormattersFactory::getFormatterForDictionaryWidget($mnemosystem);
		return self::$mnemoFormatterCache[$systemId];
	}
}
