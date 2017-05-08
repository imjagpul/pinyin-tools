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

/**
 * This constant defines the length of traditional_begin and simplified_begin (could be 1 or 2).
 * It has to correspond to the data in the database.
 * 
 * Make sure the tableName() returns correct value as well.
 */
define("DICTDATA_FIRSTPART_LENGTH", 2); 

class DictEntryPhrase extends DictEntry
{
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dict_entry_phrase_two';
// 		return 'dict_entry_phrase_obsolete';
	}
	
	public static function splitInTwo($string) {
		$encoding=Yii::app()->params->fileUploadEncoding;
		
		$first=mb_substr($string, 0, DICTDATA_FIRSTPART_LENGTH, $encoding); //the first and second character
		$last=mb_substr($string, DICTDATA_FIRSTPART_LENGTH, null, $encoding); //the rest of the input
		return array($first, $last);
	}
	
	public static function getFirstPartLength() {
		return DICTDATA_FIRSTPART_LENGTH;
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
		return mb_strlen($this->traditional_rest, Yii::app()->params['annotatorEncoding'])+DICTDATA_FIRSTPART_LENGTH;
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
