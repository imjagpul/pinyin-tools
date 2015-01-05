<?php

/**
 * This is the model class for table "system_keyword_inheritance".
 *
 * The followings are the available columns in table 'system_keyword_inheritance':
 * @property integer $system
 * @property integer $inheritsFrom
 *
 * The followings are the available model relations:
 * @property System $system0
 * @property System $inheritsFromSystem
 */
class SystemKeywordInheritance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'system_keyword_inheritance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('system, inheritsFrom', 'required'),
			array('system, inheritsFrom', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('system, inheritsFrom', 'safe', 'on'=>'search'),
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
			'system0' => array(self::BELONGS_TO, 'System', 'system'),
			'inheritsFromSystem' => array(self::BELONGS_TO, 'System', 'inheritsFrom'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'system' => 'System',
			'inheritsFrom' => 'Inherits From',
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

		$criteria->compare('system',$this->system);
		$criteria->compare('inheritsFrom',$this->inheritsFrom);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SystemKeywordInheritance the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
