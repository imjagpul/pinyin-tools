<?php

/**
 * This is the model class for table "dictionary".
 *
 * The followings are the available columns in table 'dictionary':
 * @property integer $id
 * @property string $name
 * @property integer $languageId
 * @property integer $transcriptionId
 * @property integer $targetLanguageId
 * @property integer $lastchange
 *
 * The followings are the available model relations:
 * @property DictEntryChar[] $dictEntryChars
 * @property DictEntryPhrase[] $dictEntryPhrases
 * @property Lookup $language
 * @property Lookup $transcription
 * @property Lookup $targetLanguage
 */
class Dictionary extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dictionary';
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
			array('languageId, transcriptionId, targetLanguageId', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, languageId, transcriptionId, targetLanguageId', 'safe', 'on'=>'search'),
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
			'dictEntryChars' => array(self::HAS_MANY, 'DictEntryChar', 'dictionaryId'),
			'dictEntryPhrases' => array(self::HAS_MANY, 'DictEntryPhrase', 'dictionaryId'),
			'language' => array(self::BELONGS_TO, 'Lookup', 'languageId'),
			'transcription' => array(self::BELONGS_TO, 'Lookup', 'transcriptionId'),
			'targetLanguage' => array(self::BELONGS_TO, 'Lookup', 'targetLanguageId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'languageId' => 'Language',
			'transcriptionId' => 'Transcription',
			'targetLanguageId' => 'Target Language',
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
		$criteria->compare('languageId',$this->languageId);
		$criteria->compare('transcriptionId',$this->transcriptionId);
		$criteria->compare('targetLanguageId',$this->targetLanguageId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function truncate()
	{
		DictEntryChar::model()->deleteAllByAttributes(array('dictionaryId'=>$this->id));
		DictEntryPhrase::model()->deleteAllByAttributes(array('dictionaryId'=>$this->id));
	}
	
	public function addEntry($traditional, $simplified, $transcription, $translation) {
		$encoding=Yii::app()->params->fileUploadEncoding;
	
			//see if it a single character or a phrase (phrase here means "more than one character")
			$model=NULL;
			$phrase= mb_strlen($traditional, $encoding)>1 ;
			if($phrase) { //phrase
				$model=new DictEntryPhrase();
			} else { //single char
				$model=new DictEntryChar();
			}
			
			//create the new entry
			$model->dictionaryId=$this->id;
			$model->traditional=$traditional;
			$model->simplified=$simplified;
			$model->transcription=$transcription;
			$model->translation=$translation;
			
			//somethimes there are duplcities in the input, so in that case let's join them
			if($phrase) {
				//note here we are using the new model to split the begin and rest
				$data=array(
				'dictionaryId'=>$this->id,
				'traditional_begin'=>$model->traditional_begin,
				'traditional_rest'=>$model->traditional_rest,
				'transcription'=>$transcription
						);
			} else {
				$data=array(
				'dictionaryId'=>$this->id,
				'traditional'=>$traditional,
				'transcription'=>$transcription
						);
				
			}
			
			$existing=$model->model()->findByAttributes($data);
			
			if(!is_null($existing)) {
				//add the value, with slash as the separator
				$joinedTranslation='';
				if(empty($translation)) $joinedTranslation=$existing->translation;
				else if(empty($existing->translation)) $joinedTranslation=$translation;
				else $joinedTranslation=($existing->translation.'/'.$translation);
				
				$existing->saveAttributes(array('translation'=> $joinedTranslation));
			} else {
				
				$model->insert();
			}
	}	
	/*
	public function replaceData($newData) {
		$this->truncate();
		$encoding=Yii::app()->params->fileUploadEncoding;
</div>
		foreach($newData as $entry) {
			$simplified=$entry[1];
			$traditional=$entry[2];
			$transcription=$entry[3];
			$translation=$entry[4];
			
			//see if it a single character or a phrase (phrase here means "more than one character")
			$model=NULL;
			if(mb_strlen($simplified, $encoding)>1) { //phrase
				$model=new DictEntryPhrase(</div>);
			} else { //single char
				$model=new DictEntryChar();
			}
			
			//add the entry into the dictionary
			$model->dictionaryId=$this->id;
			$model->simplified=$simplified;
			$model->traditional=$traditional;
			$model->transcription=$transcription;
			$model->translation=$translation;
			
			$model->insert();
		}
	}*/
	
	public function getTranscriptionName() {
		//TODO add an inner join at creation time and remove this method
		//@TODO or maybe just use the relations instead
		return Lookup::model()->findByPk($this->transcriptionId)->text;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Dictionary the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
