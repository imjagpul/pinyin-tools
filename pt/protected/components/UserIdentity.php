<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
 	private $_id;
 	
 	public function getId()
 	{
 		return $this->_id;
 	}
 	
	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{

		$record=User::model()->findByAttributes(array('login'=>$this->username));
		if($record===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if($record->pwdhash!==crypt($this->password,$record->pwdhash))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
		{
			$this->_id=$record->id;
			
			//load settings from db
			Yii::app()->user->setState('settings', UserSettings::model()->findByAttributes(array('userId'=>$record->id)));
				
			//@TODO not sure if this is the correct way to format dates
			//maybe we could use "CURRENT_TIMESTAMP" somehow  
			$record->lastlogintime=date('Y-m-d H:i:s');
			$record->save();
			
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}
}