<?php

/**
 * This is the model class for table "longtask_chunk".
 *
 * The followings are the available columns in table 'longtask_chunk':
 * @property integer $id
 * @property integer $longtask_id
 * @property string $input
 * @property string $result
 * @property string $result2
 * @property integer $startIndex
 */

class LongtaskChunk extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'longtask_chunk';
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LongtaskChunk the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
