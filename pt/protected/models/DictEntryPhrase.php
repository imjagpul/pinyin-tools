<?php

/**
 * This is the model class for table "dict_entry_phrase".
 *
 * The followings are the available columns in table 'dict_entry_phrase':
 * @property integer $dictionaryId
 * @property string $simplified_begin
 * @property string $simplified_rest
 * @property string $traditional_begin
 * @property string $traditional_rest
 * @property string $transcription
 * @property string $translation
 */
class DictEntryPhrase extends DictEntry
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dict_entry_phrase';
	}
	
	public static function splitInTwo($string) {
		$encoding=Yii::app()->params->fileUploadEncoding;
		
// 		$len=mb_strlen($string, $encoding);
		
		$first=mb_substr($string, 0, 1, $encoding); //the first character
		$last=mb_substr($string, 1, null, $encoding); //the rest of the input
		return array($first, $last);
	}
	
	public function getSimplified() {
		return $this->simplified_begin.
			$this->simplified_rest;
	}
	
	public function setSimplified($simplified) {
		$split=self::splitInTwo($simplified);
		$this->simplified_begin=$split[0];
		$this->simplified_rest=$split[1];
	}
	
	public function getTraditional() {
		return $this->traditional_begin.
			$this->traditional_rest;
	}
	
	public function setTraditional($traditional) {
		$split=self::splitInTwo($traditional);
		$this->traditional_begin=$split[0];
		$this->traditional_rest=$split[1];
	}

	public function getLength() {
		return mb_strlen($this->traditional_rest, Yii::app()->params['annotatorEncoding'])+1;
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

		$criteria->compare('simplified_begin',$this->simplified_begin,true);
		$criteria->compare('simplified_middle',$this->simplified_middle,true);
		$criteria->compare('simplified_end',$this->simplified_end,true);
		$criteria->compare('traditional_begin',$this->traditional_begin,true);
		$criteria->compare('traditional_middle',$this->traditional_middle,true);
		$criteria->compare('traditional_end',$this->traditional_end,true);
		$criteria->compare('pinyin',$this->pinyin,true);
		$criteria->compare('english',$this->english,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DictEntryPhrase the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
