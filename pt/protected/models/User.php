<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $login
 * @property string $pwdhash
 * @property string $email
 * @property string $registertime
 * @property string $lastlogintime
 *
 * The followings are the available model relations:
 * @property System[] $systems
 * @property UserSettings $userSettings
 * @property UserSettingsSystems[] $userSettingsSystems
 */
class User extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('login, pwdhash', 'required'),
			array('login', 'length', 'max'=>50),
			array('email', 'length', 'max'=>99),
// 			array('lastlogintime', 'safe'), // not in  user input
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login, pwdhash, email, registertime, lastlogintime', 'safe', 'on'=>'search'),
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
			'systems' => array(self::HAS_MANY, 'System', 'master'),
			'userSettings' => array(self::HAS_ONE, 'UserSettings', 'userId'),
			'userSettingsSystems' => array(self::HAS_MANY, 'UserSettingsSystems', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'login' => 'Choose your username',
			'pwdhash' => 'Create a password',
			'email' => 'E-mail (optional)'
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
		$criteria->compare('login',$this->login,true);
		$criteria->compare('pwdhash',$this->pwdhash,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('registertime',$this->registertime,true);
		$criteria->compare('lastlogintime',$this->lastlogintime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
