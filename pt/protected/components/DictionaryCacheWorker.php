<?php
class DictionaryCacheWorker {
	
	private static function generateFilepath($chars, $dictID, $systemID) {
		$datadir=Yii::getPathOfAlias('application.data.generatedDicts');
		return $datadir.DIRECTORY_SEPARATOR.($chars ? 'chars' : 'words').(is_null($systemID) ? '' : $systemID).'dict'.$dictID;
	}
	
	/**
	 * Removes all cache entries using the given ID of the System.
	 * (This is to be called after every change to a system.)
	 * 
	 * @param integer $dictID
	 */
	public static function nullifyCacheSystem($dictID) {
		//@TODO implement and call from all edits
	}
	
	/**
	 * Removes all cache entries using the given ID of the Dictionary.
	 * (This is to be called after new dictionary data is upload to the db.)
	 * 
	 * @param integer $dictID
	 */
	public static function nullifyCacheDictionary($dictID) {
		//@TODO implement and call from the upload dialog
	}

	/**
	 * Loads data from the DB to the file, rewriting an existing file.
	 *
	 * @param boolean $chars
	 *        	TRUE if character dictionary is to be outputed, FALSE if the phrase dictionary
	 * @param integer $dictID
	 * 			ID of the Dictionary to be used
	 * @param integer $systemID
	 * 			ID of the System to be used for mnemonics, or NULL for no mnemonics
	 */
	private static function rewriteCacheDictionary($chars, $dictID, $systemID=NULL) {		
		$filepath=self::generateFilepath($chars, $dictID, $systemID);
		$handle=fopen($filepath, 'wb');
		if($handle===FALSE) {
			$msg="Opening file '$filepath' failed.";
			throw new CHttpException(400, $msg);
		}
			
		DictionaryCacheWorker::writeDictionary($chars, $dictID, $systemID, $handle);
		fclose($handle);
	}
	
	/**
	 * Ensures the cache of the desired dictionary exists.
	 * 
	 * @param boolean $chars
	 *        	TRUE if character dictionary is to be outputed, FALSE if the phrase dictionary
	 * @param integer $dictID
	 * 			ID of the Dictionary to be used
	 * @param integer $systemID
	 * 			ID of the System to be used for mnemonics, or NULL for no mnemonics
	 * @return boolean
	 * 			TRUE if the cache was regenerated,
	 * 			FALSE if it already existed
	 */
	public static function ensureCacheDictionary($chars, $dictID, $systemID=NULL) {
		$filepath=self::generateFilepath($chars, $dictID, $systemID);
		
		if(!file_exists($filepath) || filesize($filepath)<1) {
			self::rewriteCacheDictionary($chars, $dictID, $systemID);
			return TRUE;
		}
		
		return FALSE;
	}
	
	/**
	 * Outputs a dictionary to standard output.
	 * 
	 * The data always taken from the cache (the cache entry is created if it does not exist).
	 * 
	 * @param boolean $chars
	 *        	TRUE if character dictionary is to be outputed, FALSE if the phrase dictionary
	 * @param integer $dictID        	
	 * 			ID of the Dictionary to be used
	 * @param integer $systemID        	
	 * 			ID of the System to be used for mnemonics, or NULL for no mnemonics
	 */
	public static function outputDictionary($chars, $dictID, $systemID=NULL) {
		$filepath=self::generateFilepath($chars, $dictID, $systemID);
		self::ensureCacheDictionary($chars, $dictID, $systemID);
		
		echo file_get_contents($filepath);
	}
	
	/**
	 * Generates a dictionary in HTML format to standard output, or to a file handle (if given).
	 * The data is read anew from the database.
	 * 
	 * @param boolean $chars
	 *        	TRUE if character dictionary is to be outputed, FALSE if the phrase dictionary
	 * @param integer $dictID        	
	 * 			ID of the Dictionary to be used
	 * @param integer $systemID        	
	 * 			ID of the System to be used for mnemonics, or NULL for no mnemonics
	 * @param resource
	 * 			the file handle to write output to, or NULL if the result is to be written on standard output
	 */
	public static function writeDictionary($chars, $dictID, $systemID=NULL, $handle=NULL) {
// 		$dictionariesID=array (
// 				$dictID 
// 		);
// 		$transcriptionFormatters=AnnotatorEngine::createFormatters($dictionariesID);
		$transcriptionFormatter=AnnotatorEngine::createFormatter($dictID);
		
		$criteria=new CDbCriteria();
		$criteria->compare('dictionaryId', $dictID);
		$dbStepSize=Yii::app()->params ['dbStepSize'];
		
		if ($dbStepSize != 0) {
			$criteria->offset=0;
			$criteria->limit=$dbStepSize;
		}
		
		// setup paths and parameters
		$coreDir=Yii::app()->getViewPath() . DIRECTORY_SEPARATOR . 'annotator' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR;
		if ($chars)
			$renderer=$coreDir . 'charEntry.php';
		else
			$renderer=$coreDir . 'phraseEntry.php';
		
		//load the system model if necessary
		$systemData=null;
		if (!is_null($systemID)) {
			$systemData=System::model()->findByAttributes(array (
					"id" => $systemID 
			));
			
			if (is_null($systemData)) {
				throw new Exception("Failed to load desired system from the database.");
			}
		}
			
		do {
			if ($chars)
				$allEntries=DictEntryChar::model()->findAll($criteria);
			else
				$allEntries=DictEntryPhrase::model()->findAll($criteria);
			
			foreach ( $allEntries as $entry ) {
				$entryText=$entry->traditional;
				
				// if($chars)
				// $translations=self::loadTranslationsFromDictionaries($entryText, $dictionariesID, self::CHARMOD_TRADITIONAL_ONLY);
				// else
				// $translations=self::loadPhrasesFromDictionaries($entryText, $dictionariesID, self::CHARMOD_TRADITIONAL_ONLY);
				
				$mnemos=null;
				
				if(!is_null($systemData))
					$mnemos=AnnotatorEngine::loadMnemonicsForSystem($entryText, $systemData);
				
				if (!is_null($handle)) { // output goes to an file
					ob_start();
					// ob_implicit_flush(false);
				}
				require ($renderer);
				
				if (!is_null($handle)) { // output goes to an file
					$chunk=ob_get_clean();
					fwrite($handle, $chunk);
				}
				
				// if($chars) {
				// $data=array (
				// 'char' => $entryText,
				// 'entry' => $entry,
				// 'mnemos' => $mnemos,
				// 'transcriptionFormatters' => $transcriptionFormatters
				// );
				
				// $parent->renderPartial('core/charEntry', $data, false, true);
				// } else {
				// $data=array (
				// 'char' => $entryText,
				// 'entry' => $entry,
				// 'mnemos' => $mnemos,
				// 'transcriptionFormatters' => $transcriptionFormatters
				// );
				
				// $parent->renderPartial('core/charEntry', $data, false, true);
				
				// }
			}
			
			if ($dbStepSize == 0)
				break;
			
			$criteria->offset+=$dbStepSize;
		} while ( !empty($allEntries) );
	}
	
	//every character of the phrase must link to the dictionary
	private static function linkify($text) {
		$encoding=Yii::app()->params['fileUploadEncoding'];
		$result='';
	
		for($i=0;$i<mb_strlen($text, $encoding);$i++) {
			$char = mb_substr($text, $i, 1, $encoding);
	
			$result.='<a href="#';
			$result.=AnnotatorEngine::textToLink($char);
			$result.='">';
			$result.=$char ;
			$result.='</a>';
		}
	
		return $result;
	}
	
}