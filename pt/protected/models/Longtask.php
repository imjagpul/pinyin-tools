<?php

/**
 * This is the model class for table "longtask".
 *
 * The followings are the available columns in table 'longtask':
 * @property integer $id
 * @property integer $user_id
 * @property string $expire_time
 * @property integer $last_chunk
 * @property integer $max_chunk
 * @property integer $system_id
 * @property integer $dict_id
 * @property integer $mode
 * @property integer $outputMode
 */
class Longtask extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'longtask';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, max_chunk, outputMode', 'required'),
			array('user_id, last_chunk, max_chunk, system_id, dict_id, mode, outputMode', 'numerical', 'integerOnly'=>true),
			array('expire_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, expire_time, last_chunk, max_chunk, system_id, dict_id, mode, outputMode', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'expire_time' => 'Expire Time',
			'last_chunk' => 'Last Chunk',
			'max_chunk' => 'Max Chunk',
			'system_id' => 'System',
			'dict_id' => 'Dict',
			'mode' => 'Mode',
			'outputMode' => 'Output Mode',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('expire_time',$this->expire_time,true);
		$criteria->compare('last_chunk',$this->last_chunk);
		$criteria->compare('max_chunk',$this->max_chunk);
		$criteria->compare('system_id',$this->system_id);
		$criteria->compare('dict_id',$this->dict_id);
		$criteria->compare('mode',$this->mode);
		$criteria->compare('outputMode',$this->outputMode);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Longtask the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * Create an AnnotatorEngine with the data from this Longtask, but with no input set.
	 * @param CController $parent
	 * @return AnnotatorEngine an empty 
	 */
	private function createEmptyAnnotatorEngine($parent) {
		$annotatorEngine=new AnnotatorEngine();
		$annotatorEngine->parent=$parent;
		$annotatorEngine->systemID=$this->system_id;
		$annotatorEngine->dictID=$this->dict_id;
		$annotatorEngine->mode=AnnotatorMode::parseMode($this->mode);
		$annotatorEngine->outputMode=$this->outputMode;
		
		return $annotatorEngine;
	}
	
	/**
	 * 
	 * @param CController $parent
	 * @return AnnotatorEngine
	 */
	public function createFinalAnnotatorEngine($parent) {
		$annotatorEngine=$this->createEmptyAnnotatorEngine($parent);
		
		$criteria=new CDbCriteria();
		$criteria->order="id";
		$criteria->compare('longtask_id',$this->id);
		$chunks=LongtaskChunk::model()->findAll($criteria);

		$secondResults='';
				
		foreach($chunks as $chunk) {
			$annotatorEngine->input.=$chunk->result;
			
			if(!is_null($chunk->result2)) {
				$secondResults.=$chunk->result2;
			}
		}
		
		//append the second results to after the first results
		$annotatorEngine->input.=$secondResults;
		
		return $annotatorEngine;
	}
	
	public function createNextAnnotatorEngine($parent) {
		$annotatorEngine=$this->createEmptyAnnotatorEngine($parent);
		
		$lastChunkId=!empty($this->last_chunk) ? $this->last_chunk : 0;
		$chunk=LongtaskChunk::model()->findByAttributes(array('id' => $lastChunkId, 'longtask_id'=>$this->id));
		$annotatorEngine->input=$chunk->input;
		$annotatorEngine->startingIndex=$chunk->startIndex;
	
		return array($annotatorEngine, $chunk);
	}
	
	//the folowing method is taken from the Yii wiki:
	//http://www.yiiframework.com/wiki/840/background-task-with-ajax/
	
	// Initialize attributes
	public function init() {
		if ($this->scenario <> 'search') {
			// 			$this->end_time = date('Y-m-d H:i:s');
			// 			$this->task = Yii::app()->request->url;
			// 			$this->username = Yii::app()->user->id;
			$this->expire_time=date('Y-m-d H:i:s', strtotime('+3 hours'));
		}
	}
}
