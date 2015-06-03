<?php
class Suggestion {
	public $keyword;
	public $mnemo;
	public $compositions;
	public $dict;
	
	public function fill($systemID, $chardef, $parentController) {
		if(empty($chardef))
			return false;
		
		$system=System::model()->findByPk($systemID);
		
		$this->keyword=$this->suggestKeyword($system, $chardef);

		$model=new Char;
		$model->chardef=$chardef;
		ob_start();
		ob_implicit_flush(false);
		$editor=CompositionsEditor::create($model, $parentController)->outputEditable();
		$this->compositions=ob_get_clean();
		
		//dict-portlet
		ob_start();
		ob_implicit_flush(false);
		$parentController->widget('DictionaryWidget', array('dictionaryQuery' => $chardef));
		$this->dict=ob_get_clean();

		return true;
	}

	/**
	 * 
	 * @param Char $charModel
	 * @return Char[][]  What composition have other systems for this char. 
	 * 					First index of the array is JSON encoded array that is value.
	 * 					(useful for usage in listbox)
	 */
	public function suggestComposition($charModel) {
		//@TODO add a way to exclude chosen systems (to ignore systems per user basis)
		
		if(empty($charModel->chardef)) {
			return;
		}
		
		//@TODO add a way to exclude chosen systems (to ignore systems per user basis)
		$criteria=new CDbCriteria();
		$criteria->compare('chardef', $charModel->chardef);
		if(!$charModel->isNewRecord)
			$criteria->compare('id', "<>$charModel->id");
		$criteria->with='components';
		$charModelOthers=Char::model()->findAll($criteria); //@TODO fetch ids only

		//filter empty suggestions (with no compositions) and prepare the list data
		$allComponents=array();
		
		foreach($charModelOthers as $model) {
			$components=$model->components;
			
			//filter empty
			if(empty($components))
				continue;
			
			//assure no duplicite suggestions are made
			$entry=array();
			$chardefsOfCurrent=array();
			foreach($components as $comp) {
				$entry[]=$comp->subchar;
				$chardefsOfCurrent[]=$comp->subchar->chardef;
			}
			
			$uniquekey=CJSON::encode($chardefsOfCurrent);
			$allComponents[$uniquekey]=$entry;
		}
		return $allComponents;
	}
	
	/**
	 * 
	 * @param System $system
	 * @param string $chardef
	 */
	public function loadTranscription($system, $chardef) {
		
		//choose the relevant dictionary 
		$relevantDicts=Dictionary::model()->findAllByAttributes(array(
// 				'languageId'=>$system->language,
				'targetLanguageId'=>$system->targetLanguage,
				'transcriptionId'=> $system->transcription
		));
		
		if(count($relevantDicts)==0) {
			return "";
		}
		$relevantDicts=CHtml::listData($relevantDicts, 'id', 'id');
		
		//get the transcriptions out of the dict		
		$criteria=new CDbCriteria();
		$criteria->addInCondition('dictionaryId', $relevantDicts);
		$criteria->compare('simplified', $chardef);
		$criteria->compare('traditional', $chardef, false, "OR");
		
		$candidates=DictEntryChar::model()->findAll($criteria);
		
		//use the first one which is not yet used
		foreach($candidates as $candidate) {
			return $candidate->transcription;
		}		
	}
	
	private function suggestKeyword($system, $chardef) {
		$allInheritedIds=$system->allInheritedIds;
		
		//get the keywords out of the dict
		$relevantDicts=Dictionary::model()->findAllByAttributes(array(
				'languageId'=>$system->language,
				'targetLanguageId'=>$system->targetLanguage
		));
		
		$relevantDicts=CHtml::listData($relevantDicts, 'id', 'id');
		
		$criteria=new CDbCriteria();
		$criteria->addInCondition('dictionaryId', $relevantDicts);
		$criteria->compare('simplified', $chardef);
		$criteria->compare('traditional', $chardef, false, "OR");

		$candidates=DictEntryChar::model()->findAll($criteria);
		
		//find the first one which is not yet used
		foreach($candidates as $candidate) {
			
			foreach($candidate->translationsArray as $kw) {
				$criteria=new CDbCriteria();
				$criteria->addInCondition('system', $allInheritedIds);
				$criteria->compare('keyword', $kw);
				$result=Char::model()->find($criteria);
					
				if($result===NULL) { //the keyword is not yet used, so return it
					return $kw;
				}
			}
		}		
	}
	
	public function encode() {
		return CJSON::encode($this);
	}
}