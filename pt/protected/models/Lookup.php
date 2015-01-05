<?php

/**
 * This is the model class for table "lookup".
 *
 * The followings are the available columns in table 'lookup':
 * @property integer $id
 * @property string $type
 * @property string $text
 * @property integer $position
 *
 * The followings are the available model relations:
 * @property Dictionary[] $dictionaries
 * @property Dictionary[] $dictionaries1
 * @property Dictionary[] $dictionaries2
 */
class Lookup extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lookup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, text, position', 'required'),
			array('position', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>13),
			array('text', 'length', 'max'=>30),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, text, position', 'safe', 'on'=>'search'),
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
			'dictionaries' => array(self::HAS_MANY, 'Dictionary', 'languageId'),
			'dictionaries1' => array(self::HAS_MANY, 'Dictionary', 'transcriptionId'),
			'dictionaries2' => array(self::HAS_MANY, 'Dictionary', 'targetLanguageId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'text' => 'Text',
			'position' => 'Position',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('position',$this->position);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Lookup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function getTargetLanguages() {
		$c=new CDbCriteria;
		$c->addInCondition('type',array('targetLanguage'));
		$c->order="text";
		return Lookup::model()->findAll($c);
	}
	
	public static function getAllLanguages() {
		$c=new CDbCriteria;
		$c->addInCondition('type',array('language', 'targetLanguage'));
		$c->order="text";
		return Lookup::model()->findAll($c);
	}
}
