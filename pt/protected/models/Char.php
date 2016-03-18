<?php

/**
 * This is the model class for table "char".
 *
 * The followings are the available columns in table 'char':
 * @property integer $id
 * @property string $chardef
 * @property integer $system
 * @property string $keyword
 * @property string $transcription
 * @property string $transcriptionAuto
 * @property string $mnemo
 * @property string $notes
 * @property string $notes2
 * @property string $notes3
 *
 * The followings are the available model relations:
 * @property System $systemValue
 * @property Composition[] $components
 * @property Composition[] $compositionParents
 */
class Char extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'char';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chardef, system', 'required'),
// 			array('chardef, system, keyword', 'required'),
			array('system', 'numerical', 'integerOnly'=>true),
			array('keyword', 'length', 'max'=>256),
			array('transcription', 'length', 'max'=>25),
			array('mnemo, notes, notes2, notes3', 'length', 'max'=>2048),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, chardef, system, keyword, transcription, mnemo, notes, notes2, notes3', 'safe', 'on'=>'search'),
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
			'systemValue' => array(self::BELONGS_TO, 'System', 'system'),
			'components' => array(self::HAS_MANY, 'Composition', 'charId'),
			'compositionParents' => array(self::HAS_MANY, 'Composition', 'subcharId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'chardef'=>'Character (or a verbal description)',
				'system'=>'System',
				'keyword'=>'Keyword',
				'transcription'=>'Transcription (optional)',
				'components'=>'Components',
				'mnemo'=>'Mnemonics',
				'notes'=>'Notes',
				'notes2'=>'Notes 2',
				'notes3'=>'Notes 3',				
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
		$criteria->compare('chardef',$this->chardef,true);
		$criteria->compare('system',$this->system);
		$criteria->compare('keyword',$this->keyword,true);
		$criteria->compare('transcription',$this->transcription,true);
		$criteria->compare('mnemo',$this->mnemo,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('notes2',$this->notes2,true);
		$criteria->compare('notes3',$this->notes3,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @deprecated use ->systemValue->name;
	 * @return String
	 */
	public function getSystemName() {
		return System::getSystemName($this->system);
	}
	/*
	public function getComposition() {
		//@TODO convert to an inner join
		
		$criteria=new CDbCriteria;
		$criteria->compare('charId', $this->id, false);
		
		$entries=Composition::model()->findAll($criteria);
		
		$result=array();
		
		foreach($entries as $entry) {
			$result[]=self::findByPk($entry->subcharId);
		}
		return $result;
	}*/

	public function getMnemonicsHTML() {
		 return System::getMnemoFormatter($this->system)->format($this->mnemo);
	}
	
	public function getMnemonicsResanitized() {
		$sanitizer=new HtmlSanitizer();
		return $sanitizer->sanitize($this->mnemo);
	}
	
	/**
	 * Returns the transcription as set (if set)
	 * or the transcription as loaded from dictionary. 
	 * @return string
	 */
	public function getTranscriptionAuto() {
		if(!empty($this->transcription)) {
			return $this->transcription;
		} else {
			return Suggestion::loadTranscription($this->systemValue, $this->chardef);
		}
	}
	
	/**
	 * Returns the transcription as set (if set)
	 * or the transcription as loaded from dictionary. 
	 * @return string
	 */
	public function getTranscriptionAutoNoTone() {
		$val=$this->transcriptionAuto;
		$factory=FormattersFactory::getFormatterForDictionaryWidget($this->systemValue->transcriptionName, PINYIN_FORMAT_NO_TONES);
		return $factory->format($val);
	}

	public function getDiagnostics() {
		return implode(" ",CharDiagnostics::diagnose($this));
// 		return CharDiagnostics::diagnose($this->mnemo, $this->systemValue->mnemosystem);
		//return System::getMnemoFormatter($this->system)->format($this->mnemo);
	}
	
	
	/**
	 * Returns true if all data in this model are empty
	 * (excluding chardef and id - these two are ignored).
	 * 
	 * @return boolean 
	 */
	public function isEmpty() {
		return (empty($this->keyword) &&
			empty($this->mnemo)&&
			empty($this->notes)&&
			empty($this->notes2)&&
			empty($this->notes3)&&
			empty($this->components)); 
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Char the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
