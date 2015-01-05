<?php

/**
 * This is the model class for table "soundword".
 *
 * The followings are the available columns in table 'soundword':
 * @property integer $id
 * @property string $soundword
 * @property string $transcription
 * @property integer $system
 *
 * The followings are the available model relations:
 * @property System $system0
 */
class Soundword extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'soundword';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('soundword, transcription, system', 'required'),
			array('system', 'numerical', 'integerOnly'=>true),
			array('soundword', 'length', 'max'=>50),
			array('transcription', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, soundword, transcription, system', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'soundword' => 'Soundword',
			'transcription' => 'Transcription',
			'system' => 'System',
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
		$criteria->compare('soundword',$this->soundword,true);
		$criteria->compare('transcription',$this->transcription,true);
		$criteria->compare('system',$this->system);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Soundword the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
