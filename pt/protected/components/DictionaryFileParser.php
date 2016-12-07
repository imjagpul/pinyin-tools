<?php
class DictionaryFileParser {
	/*
	 * public static function parse($uploadedFile) {
	 * $separator="\t";
	 * $result=array();
	 *
	 * //foreach line
	 * while($line=$uploadedFile->readLine()) {
	 * if($line[0]=='#')
	 * continue;
	 *
	 * //$regex="%(.+)\s+(.+)\s+\[(.+)\]\s+/(.+)/%";
	 * $res=preg_match($regex, $line, $result[]);
	 * }
	 *
	 * return $result;
	 * }
	 */
	private static function generateFilepath($chars, $dictID) {
		$datadir=Yii::getPathOfAlias('application.data.dictionaries');
		return $datadir . DIRECTORY_SEPARATOR . ($chars ? 'chars' : 'words') . $dictID . '.sql';
	}
	/**
	 * 
	 * @param Dictionary $dictModel
	 * 			the dictionary to insert into
	 * @return number
	 * 			number of rows affected
	 */
	public static function executeSQL($dictModel) {
		$dictID=$dictModel->id;
		$charsFilepath=self::generateFilepath(true, $dictID);
		$phrasesFilepath=self::generateFilepath(false, $dictID);
		
		$r=0;
		$r+=Yii::app()->db->createCommand(file_get_contents($charsFilepath))->execute();
		$r+=Yii::app()->db->createCommand(file_get_contents($phrasesFilepath))->execute();
		
		return $r;
	}
	
	/**
	 * Generates SQL files that are ready to be inserted into the database directly.
	 * 
	 * @param UploadedFile $uploadedFile        	
	 * @param Dictionary $dictModel        	
	 * @return boolean|string
	 */
	public static function prepareSQL($uploadedFile, $dictModel) {
		$datadir=Yii::getPathOfAlias('application.data.dictionaries');
		
		$separator="\t";
		$badLines=false;
		// foreach line
		$regex="^\s*(\S++)\s++(\S++)\s++\[([^\]]++)\]\s*+/?+(.*?)/?+$";
		
		$dictID=$dictModel->id;
		$charsFilepath=self::generateFilepath(true, $dictID);
		$phrasesFilepath=self::generateFilepath(false, $dictID);
		
		$charsSQLhandle=fopen($charsFilepath, 'wb');
		$phrasesSQLhandle=fopen($phrasesFilepath, 'wb');
		
		//@TODO turn these to constants
		$joinCommands=true; // if FALSE, each entry has it own command; if TRUE, the whole dictionary is only two INSERT INTO commands
		$insertIntoWords='INSERT INTO `dict_entry_char` (`dictionaryId`, `simplified`, `traditional`, `transcription`, `translation`) VALUES ';
		$insertIntoPhrases='INSERT INTO `dict_entry_phrase` (`dictionaryId`, `simplified_begin`, `simplified_rest`, `traditional_begin`, `traditional_rest`, `transcription`, `translation`) VALUES ';
		
		if ($joinCommands) {
			fwrite($charsSQLhandle, $insertIntoWords);
			fwrite($phrasesSQLhandle, $insertIntoPhrases);
		}
		
		$encoding=Yii::app()->params['annotatorEncoding'];
		
		// the entries are to be separated with commas - so we write a comma before every entry, except before the first one
		$charsFirstCommaOmitted=false;
		$phraseFirstCommaOmitted=false;
		
		$duplicates=array();
		$currentValue=array();
		
		// we have to work with the following line as well - in order to be able to join duplicates
		// so load the first line
		while ($line=$uploadedFile->readLine()) {
			if ($line[0] == '#')
				continue;
				
			// we need to use mb_ereg (otherwise false whitespace in the middle of a multibyte char would be matched)
			$currentRes=mb_ereg($regex, $line, $currentValue);
			
			if ($currentRes === FALSE) { // check if failed to match
				$badLines[]=$line;
				continue;
			}
			
			$phrase=mb_strlen($currentValue[1], $encoding) > 1;
			$handle=$phrase ? $phrasesSQLhandle : $charsSQLhandle;
			
			$currentValue[3]=strtolower($currentValue[3]);
			
			// Eliminate duplicates (this is necessary because the translation serves as primary key in db)
			// as a duplicate is considered an entry that has the same traditional and transcription as this one.
			
			// if the following line defines the same entry, it might be a duplicate
			// see if any of the following lines has the same transcription
			//
			// For some reason, the entries are not always sorted alphabetically by transcription in the input data,
			// so we have to look several lines ahead.
			// We assume there is a maximum of one duplicate per entry.
			for($i=1; strpos($peekline=$uploadedFile->peekline($i), $currentValue[1] . ' ') === 0; $i++) {
				$peekRes=mb_ereg($regex, $peekline, $peekValue);
				if ($currentValue[3] == strtolower($peekValue[3])) {
					// we have a dupliacte - save and go to next line
					$duplicates[$currentValue[3]]=$currentValue[4];
					continue 2;
				}
			}
			//check if any duplicates were saved concerning current line
			if (isset($duplicates[$currentValue[3]])) {
				$currentValue[4].='/' . $duplicates[$currentValue[3]];
				unset($duplicates[$currentValue[3]]);
			}
			
			// all this is here just to have commas between elements (except before the first and after the last)
			if ($joinCommands) {
				if ($phrase) {
					if (!$phraseFirstCommaOmitted)
						$phraseFirstCommaOmitted=true;
					else
						fwrite($phrasesSQLhandle, ",");
				} else {
					if (!$charsFirstCommaOmitted)
						$charsFirstCommaOmitted=true;
					else
						fwrite($charsSQLhandle, ",");
				}
			}
			
			// escape the data as we are creating a mysql command directly
			$traditional=mysql_escape_string($currentValue[1]);
			$simplified=mysql_escape_string($currentValue[2]);
			$transcription=mysql_escape_string($currentValue[3]);
			$translation=mysql_escape_string($currentValue[4]);
			
			if (!$joinCommands) {
				if (!$phrase)
					fwrite($charsSQLhandle, $insertIntoWords);
				else
					fwrite($phrasesSQLhandle, $insertIntoPhrases);
			}
			
			if (!$phrase)
				fwrite($handle, "($dictID, '$simplified', '$traditional', '$transcription', '$translation')");
			else {
				list($simplified_begin, $simplified_rest)=DictEntryPhrase::splitInTwo($simplified);
				list($traditional_begin, $traditional_rest)=DictEntryPhrase::splitInTwo($traditional);
				
				fwrite($handle, "($dictID, '$simplified_begin', '$simplified_rest', '$traditional_begin', '$traditional_rest', '$transcription', '$translation')");
			}
				
			if (!$joinCommands)
				fwrite($handle, ";");
		}
		
		if ($joinCommands) {
			// end with a semicolon
			fwrite($charsSQLhandle, ';');
			fwrite($phrasesSQLhandle, ';');
		}
		
		fclose($charsSQLhandle);
		fclose($phrasesSQLhandle);
		
		return $badLines;
	}
	
	/*
	 * Expected format:
	 * (\S++) (\S++) \[([^\]]++)\] /?+(.*?)/?+$
	 * TRADIT SIMPLI [TRANS] /TRANSC/
	 * or
	 * TRADIT SIMPLI [TRANS] TRANSC
	 *
	 * $traditional, $simplified, $transcription, $translation
	 */
	/***
	 * 
	 * @param UploadedFile $uploadedFile
	 * @param Dictionary $dictModel
	 * @return boolean|string
	 */
	public static function parseAndAdd($uploadedFile, $dictModel) {
		$separator="\t";
		$badLines=false;
		// foreach line
		while ($line=$uploadedFile->readLine()) {
			if ($line[0] == '#')
				continue;
			
			$currentValue=array();
			
			$regex="^\s*(\S++)\s++(\S++)\s++\[([^\]]++)\]\s*+/?+(.*?)/?+$";
			
			// we need to use mb_eregi (otherwise false whitespace in the middle of a multibyte char would be matched)
			$res=mb_eregi($regex, $line, $currentValue);
			
			if ($res === FALSE) { // check if failed to match
				$badLines[]=$line;
				continue;
			}
			
			$dictModel->addEntry($currentValue[1], $currentValue[2], strtolower($currentValue[3]), $currentValue[4]);
		}
		
		return $badLines;
	}
}