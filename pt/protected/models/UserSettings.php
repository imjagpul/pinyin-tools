<?php

/**
 * This is the model class for table "user_settings".
 *
 * The followings are the available columns in table 'user_settings':
 * @property integer $userId
 * @property integer $toneColor1
 * @property integer $toneColor2
 * @property integer $toneColor3
 * @property integer $toneColor4
 * @property integer $toneColor5
 * @property integer $toneColor6
 * @property integer $background
 * @property integer $foreground
 * @property integer $foregroundUnknown
 * @property integer $backgroundParallel
 * @property integer $backgroundBox
 * @property integer $backgroundBoxTag
 * @property integer $backgroundBoxChinese
 * @property integer $backgroundBoxTranscription
 * @property integer $lastSystemInAnnotator
 * @property integer $lastTemplateInAnnotator
 * @property integer $lastDictionaryInAnnotator
 * @property integer $defaultSystem
 * @property Enum  $variant
 * @property integer $autosuggestCharform
 * 
 * The followings are the available model relations:
 * @property System $lastSystemInAnnotator0
 * @property User $user 
 */
class UserSettings extends CActiveRecord
{
	/**
	 * Which dictionaries have been checked when annotator was used last.
	 * 
	 * @var Array[Integer => Boolean] [id => set]
	 */
	private $lastAnnotatorDictionariesCache=NULL;
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_settings';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId', 'required'),
			array('userId, toneColor1, toneColor2, toneColor3, toneColor4, toneColor5, lastSystemInAnnotator', 'numerical', 'integerOnly'=>true)
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
			'lastSystemInAnnotator0' => array(self::BELONGS_TO, 'System', 'lastSystemInAnnotator'),
			'user' => array(self::BELONGS_TO, 'User', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'userId' => 'User',
			'toneColor1' => 'Tone 1',
			'toneColor2' => 'Tone 2',
			'toneColor3' => 'Tone 3',
			'toneColor4' => 'Tone 4',
			'toneColor5' => 'Tone 5',
			'toneColor6' => 'Tone 6',
			'background' => 'Background',
			'foreground' => 'Foreground',
			'foregroundUnknown' => 'Foreground Unknown',
			'backgroundParallel' => 'Background Parallel',
			'backgroundBox' => 'Background Box',
			'backgroundBoxTag' => 'Background Box Tag',
			'backgroundBoxChinese' => 'Background Box Chinese',
			'backgroundBoxTranscription' => 'Background Box Transcription',
			'defaultSystem' => 'Default System',
			'lastSystemInAnnotator' => 'Last System In Annotator',
			'lastTemplateInAnnotator' => 'Last Template In Annotator',
			'variant' => 'Variant',
			'autosuggestCharform' => 'Autosuggest Charform' 
		);
	}

	public function getToneColors() {
		return array(
				$this->toneColor1,
				$this->toneColor2,
				$this->toneColor3,
				$this->toneColor4,
				$this->toneColor5,
				);
	}
	
	public function getColorNames() {
		return array(
				'toneColor1',
				'toneColor2',
				'toneColor3',
				'toneColor4',
				'toneColor5',
				
				'toneColor6',			
				
				'background',
				'foreground',
				
				'foregroundUnknown',
				
				'backgroundParallel',
				
				'backgroundBox',
				'backgroundBoxTag',
				'backgroundBoxChinese',
				'backgroundBoxTranscription'
		);
	}
	
	public function getAnnotatorColors() { //@TODO save to and load from DB
		$colors=new CAttributeCollection();
		$colors['FG']= Utilities::colorAsHex($this->foreground);
		$colors['FG_UNTAGGED'] = Utilities::colorAsHex($this->foregroundUnknown);
		//the BG_PARALLEL is deprecated 
		$colors['BG_PARALLEL']= $colors['BG']= Utilities::colorAsHex($this->background);
		$colors['BG_BOX']= Utilities::colorAsHex($this->backgroundBox);
		$colors['BG_TAGBOX']= Utilities::colorAsHex($this->backgroundBoxTag);
		$colors['BG_BOX_CH']= Utilities::colorAsHex($this->backgroundBoxChinese);
		$colors['BG_TRANSCRIPTION']= Utilities::colorAsHex($this->backgroundBoxTranscription);
		return $colors; 		
	}
	
// 	/**
// 	 * Which dictionaries have been selected last time annotator was used.
// 	 */
// 	public function getLastAnnotatorDictionaries() {
// 		$result=$this->lastAnnotatorDictionariesCache;
		
// 		if($result==NULL) { 
// 			if(!empty($this->userId)) {
// 				//pull it out of db (if an user is logged in)
// 				$result=UserSettingsDictionaries::model()->findAllByAttributes(array('userId'=>$this->userId,
// 						'annotator'=>1));
// 				$result=CHtml::listData($result,'dictionaryId','dictionaryId');
// 				$this->lastAnnotatorDictionariesCache=$result;
// 			} else {
// 				//return default value;
// 				$result = array("1"); 
// 			}
// 		}
		
// 		return $result;
// 	}
	
	/**
	 * Which dictionaries have been selected last time annotator was used.
	 * @param array $newValues array of ids
	 */
	public function setLastAnnotatorDictionaries($newValues) {
		$this->lastAnnotatorDictionariesCache=$newValues;
	}
	
	private function saveLastAnnotatorDictionaries() {
		//do nothing if nothing to save
		$newValues=$this->lastAnnotatorDictionariesCache;
		if(is_null($newValues)) {
			return;
		}
		
		//set all dictionaries (that have a settings entry) to false
		//NOTE by NOT including the addNotInCondition the $updatedCount (below) gives correct values 
		$criteria=new CDbCriteria();
		$criteria->compare('userId', $this->userId);
		UserSettingsDictionaries::model()->updateAll(array('annotator'=>0), $criteria);
		
		//set all selected dictionaries (that have a settings entry) to true
		$criteria=new CDbCriteria();
		$criteria->compare('userId', $this->userId);
		$criteria->addInCondition('dictionaryId', $newValues);
		$updatedCount=UserSettingsDictionaries::model()->updateAll(array('annotator'=>1), $criteria);
		
		if($updatedCount!=count($newValues)) {
			//not all selected dictionaries have an entry
			//we need to add these to the db

			//first get a list of those already updated values
			$updatedList=UserSettingsDictionaries::model()->findAll($criteria);
			$updatedList=CHtml::listData($updatedList ,'dictionaryId','dictionaryId'); //convert it to an array, whose keys are IDs
			
			foreach ($newValues as $toInsert) {
				if(!array_key_exists($toInsert, $updatedList)) {
					$newEntry=new UserSettingsDictionaries;
					$newEntry->userId=$this->userId;
					$newEntry->dictionaryId=$toInsert;
					$newEntry->annotator=1;
					$newEntry->save(false);
				}
			}
		}		
	}
	
	public function getTonesCss() {
		$toneColors=UserSettings::getCurrentSettings()->toneColors;
		$result='';
		
		for($i=0; $i<count($toneColors); $i++) {
			$toneColor=$toneColors[$i];
			//$result.= '.tone'.($i+1).'{color:#'.Utilities::colorAsHex($toneColor).';}';
		    $result.='.dicttone'.($i+1).'{color:#'.Utilities::colorAsHex($toneColor).';}';
		    
		    $result.='.archetype'.($i+1).', .tone'.($i+1).'{font-weight:bold;color:#'.Utilities::colorAsHex($toneColor).';}';
		}
		
		$result.='a.mnemocomp {font-weight:bold;color:black !important;}';

		return $result;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserSettings the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * This method is to be called after the user logs in.
	 */
	public static function loadSettingsAfterLogin() {
		//try to load from DB
		$settings=UserSettings::model()->findByAttributes(array('userId'=>Yii::app()->user->getId()));
		
		if(is_null($settings)) {
			//if there is nothing in DB, keep what is in session data, if anything is there
			$settings=Yii::app()->user->getState('settings');
			
			//else just use new
			if(is_null($settings)) {
				$settings=new UserSettings();
			}
		}
			
		Yii::app()->user->setState('settings', $settings);
	}
	
	/**
	 * Gets the settings for the current user (even for a guest).
	 * @return UserSettings
	 */
	public static function getCurrentSettings() {
		$settings=Yii::app()->user->getState('settings');
		
		if(is_null($settings)) {
			//it can also happen that the current session is lost, but the user stays logged in - so pull it out of the db  
			$settings=UserSettings::model()->findByAttributes(array('userId'=>Yii::app()->user->getId()));

			if(is_null($settings)) {
				$settings=new UserSettings();
			}
			
			Yii::app()->user->setState('settings', $settings);
		}
		
		return $settings;
	}
	
	public function saveSettings() {
		if(!Yii::app()->user->isGuest) { //guest settings are not saved into any persistent storage (they are kept only in the current session)
			//this is relevant when saving the settings for the first time per user
			$this->userId=Yii::app()->user->getId(); //@TODO not sure if this is the correct place to set it, maybe when filling with default values would be better
			$this->save(false);  //no validation needed
			$this->saveLastAnnotatorDictionaries(); //also save subsettings
		}
	}
}
