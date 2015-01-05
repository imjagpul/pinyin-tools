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

	private function suggestKeyword($system, $chardef) {
		$allInheritedIds=$system->allInheritedIds;
		
		//get the keywords out of the dict
		$relevantDicts=Dictionary::model()->findAllByAttributes(array('languageId'=>$system->language));
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