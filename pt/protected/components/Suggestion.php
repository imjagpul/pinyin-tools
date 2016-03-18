<?php
/**
 *  
 * @author imjagpul
 */
class Suggestion {
	/** The new keyword that can be used. */
	public $keyword;
	/** The suggested mnemo (not implemented). */
	public $mnemo;
	/** The full HTML code of the composition editor. */
	public $compositions;
	/** The full HTML code of the dictionary widget. */ 
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
	 * Encodes this object as JSON.
	 */
	public function encode() {
		return CJSON::encode($this);
	}
	

	/**
	 * 
	 * @param Char $charModel
	 * @return Char[][]  What composition have other systems for this char. 
	 * 					First index of the array is JSON encoded array that is value.
	 * 					(useful for usage in listbox)
	 */
	public static function suggestComposition($charModel) {
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
	 * Loads the transcription for a given character from the dictionary.
	 * @param System $system		(used to select a relevant dictionary)
	 * @param string $chardef
	 */
	public static function loadTranscription($system, $chardef) {
		
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
	
	/**
	 * Finds a translation in the dictionary that is not yet used in the given system (or the parent systems).
	 * @param System $system
	 * @param String $chardef
	 * @return String	a keyword candidate
	 */
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
	
	/**
	 * 
	 * @param Int[] $allInheritedIds
	 * @param String $newcomp
	 * @param Boolean $exact
	 * 				if true, the $newcomp is matched only against chardefs
	 * 				if false, partial matches against chardefs and also keywords are also returned
	 * @return Char[]
	 * 			   all possible matches
	 */
	public static function matchKeywordForComposition($allInheritedIds, $newcomp, $exact=FALSE) {
		$criteria=new CDbCriteria();
		$criteria->limit=Yii::app()->params['maxCompositions'];
		$criteria->addInCondition('system', $allInheritedIds);
		$criteria->compare('chardef', $newcomp, !$exact, "AND");//partial match - @TODO check if not too slow
		if(!$exact)
			$criteria->compare('keyword', $newcomp, true, "OR");

		return Char::model()->findAll($criteria);
	}
	
	/**
	 * 
	 * @param Int[] $allInheritedIds
	 * @param String $newcomp
	 * @param Boolean $exact
	 * 				if true, the $newcomp is matched only against chardefs
	 * 				if false, partial matches against chardefs and also keywords are also returned
	 * @return
	 * 			an array of arrays of four Strings: (chardef, keyword, system name, ID)  
	 */
	public static function matchKeywordForCompositionFormatted($allInheritedIds, $newcomp, $exact=FALSE) {
		//query the systems, find all possibilities
		$models=self::matchKeywordForComposition($allInheritedIds, $newcomp, $exact);		
		
		$formatted=array();
		foreach($models as $model) {
			$formatted[]=array($model->chardef, $model->keyword, $model->systemValue->name, $model->id);
		}
		return $formatted;
	}
	
}