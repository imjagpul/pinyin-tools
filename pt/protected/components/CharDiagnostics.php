<?php
define('DISABLE_SYSTEMS_CHECK', false); //HERE : tohle nejde, jinak se tam zacnou motat components
class CharDiagnostics {
	 const NOT_FOUND="nf";
	 const MISMATCH="(mismatch)";
	 const AUTOCONVERTIBLE="Autoconvertible components from notes.";
	 
	 static function getTranscriptionDictionaries($targetLanguageId, $transcriptionId) {
	 	return Dictionary::model()->findAllByAttributes(array(
	 		'targetLanguageId' =>$targetLanguageId,
	 		'transcriptionId'=>$transcriptionId));
	 } 
	 static function getTranscriptionFromDictionary($char, $targetLanguageId, $transcriptionId) {
	 	$dicts=getTranscriptionDictionaries($targetLanguageId, $transcriptionId);
	 	
	 	//return all found
	 	
	 } 

	 private static $hardcodedCharAliases=array(
	 	'竹'=>16391,
	 		'小'=>16016,
	 		'享'=>21542
	 );
	 private static $hardcodedKwAliases=array(
	 	'handshake'=>16020, //
	 	'Harry'=>15961,
	 	'harry'=>15961,
	 	'unicorn'=>15948,
	 	'only'=>16303,
	 	'kid'=>15966,
	 	'lack'=>16441,
	 	'dry'=>16134,
	 	'upright'=>15988,
	 	'city'=>16094,
	 		'swine'=>16112,
	 		'seashell'=>16593,
	 		'collectivelly'=>16811,
	 		'mutually'=>16184,
	 		'break (stick or bone)'=>19512,
	 		'octopus'=>15971,
	 		'always' => 21557,
	 		'pay out'=>16465,
	 		'woods'=>17017,
	 		'cross'=>15951,
	 		'clothing'=>16547,
	 		'occupied'=>16208,
	 		'mouth'=>15952
	 		//'hillock'=>20699
	 		
	 );
	 private static $hardcodedWholeAliases=array(
	 	'hand'=>16170,
	 	'heart'=>16412,
	 	'water'=>16062,
	 	'fire'=>16206,
	 	'thread'=>16272,
	 	'mouth'=>15952,
	 	'tree'=>15957,
	 	'eye'=>16091,
	 	'words'=>16046,
	 	'cover'=>16071,
	 	'sign'=>16536,
	 	'bundel'=>16618,
	 		'nail'=>16068,
	 		'hand'=>16170,
	 		'house'=>16088,
	 		'food'=>16350,
	 		'city'=>16094,
	 		'city 阝'=>16094,
	 		'county without stick 县'=>38437,
	 		'hair'=>16725,
	 		'baret'=>16083, //typo
	 		'barret'=>16083, //typo
	 		'beret'=>16083, //typo
	 		'berret'=>16083, //typo
	 		'secound'=>16508,
	 		'hole'=>16795,
	 		'drop'=>15973,
	 		'moon'=>16036,
	 		'ice'=>16439,
	 		'sash (冖 on top of 巾)'=>16534,
	 		'door 门'=>15985,
	 		'plant'=>16145,
	 		'home'=>16113,
	 		'foot'=>16340,
	 		'gold'=>16337,
	 		'several 几'=>15954,
	 		'spread 尃'=>17097
	 		
	 );
	 
	 /**
	  *
	  * @param String $chardef
	  * @param Integer[] $systemsID
	  * @return Char[]
	  */	 
	 static function getCharsByChar($chardef, $systemsID) {
	 	if(array_key_exists($chardef, self::$hardcodedCharAliases)) {
	 		return Char::model()->findAllByPk(self::$hardcodedCharAliases[$chardef]);
	 	}
	 	
	 	if(DISABLE_SYSTEMS_CHECK)
	 		return Char::model()->findAllByAttributes(array('chardef'=>$chardef));
	 	else
	 		return Char::model()->findAllByAttributes(array('chardef'=>$chardef), "system IN (".implode(",",$systemsID).")");
	 	
	 }
	 /**
	  * 
	  * @param String $keyword
	  * @param Integer[] $systemsID
	  * @return Char[]
	  */
	 static function getCharsByKeyword($keyword, $systemsID) {
	 	if(array_key_exists($keyword, self::$hardcodedKwAliases)) {
	 		return Char::model()->findAllByPk(self::$hardcodedKwAliases[$keyword]);
	 	}
	 	 
	 	if(DISABLE_SYSTEMS_CHECK)
	 		$result= Char::model()->findAllByAttributes(array('keyword'=>$keyword));
	 	else
	 		$result= Char::model()->findAllByAttributes(array('keyword'=>$keyword), "system IN (".implode(",",$systemsID).")");
	 	
	 	if(count($result)==0) {
	 		$keyword=str_replace('&quot;', '', $keyword);
	 		if(DISABLE_SYSTEMS_CHECK)
	 			$result= Char::model()->findAllByAttributes(array('keyword'=>$keyword));
	 		else
	 			$result= Char::model()->findAllByAttributes(array('keyword'=>$keyword), "system IN (".implode(",",$systemsID).")");
	 	}
	 	
	 	if(count($result)==0) {
	 		$keywordText=$keyword;
		 	foreach(array("to", "a", "the") as $prefix) {
		 		$keyword=$prefix." ".$keywordText;
		 			if(DISABLE_SYSTEMS_CHECK)
		 				$result=array_merge(Char::model()->findAllByAttributes(array('keyword'=>$keyword)));
		 			else
		 				$result=array_merge($result, Char::model()->findAllByAttributes(array('keyword'=>$keyword), "system IN (".implode(",",$systemsID).")"));
		 	}
	 	}
	 	 
	 	
	 	return $result;
	 }
	 
	 /**
	  * 
	  * @param String $text
	  * @param Integer[] $systemsID
	  * @return boolean|Char[]
	  */
	 static function guessComponent($text, $systemsID) {
	 	//split in [keyword][char]
	 	//check if both match
	 	
	 	//first check, if both are present 
	 	//the last char
	 	
	 	$encoding=Yii::app()->params['fileUploadEncoding'];
	 	
	 	$text=preg_replace('/^\p{Z}+|\p{Z}+$/u','',$text);//trim asian spaces
	 	$text=trim(strip_tags($text));
	 	$text=str_replace('}', '', str_replace('{', '',$text));
// 	 	$text=str_replace(')', '', str_replace('(', '',$text));

	 	$text=str_replace('&nbsp;', '', $text);
	 	
	 	if(array_key_exists($text, self::$hardcodedWholeAliases)) {
	 		return Char::model()->findAllByPk(self::$hardcodedWholeAliases[$text]);
	 	}

	 	$len=mb_strlen($text, $encoding);

	 	if($len==0) 
	 		return false; //nothing to guess
	 	if($len==1) { //if it is only one character long, it is most probably the desired character
	 		return self::getCharsByChar($text, $systemsID);
	 	}	

	 	$lastChar=mb_substr($text,$len-1, 1, $encoding);
	 	$beforeLastChar=mb_substr($text,$len-2, 1, $encoding);
	 	

	 	if($lastChar!==")" && $beforeLastChar!==" " && $beforeLastChar!=="　") { //note the second is the unicode 0x3000 (asian space)
		 	//the whole $text is only the keyword - let´s try to find it
		 	return self::getCharsByKeyword($text, $systemsID);
	 	}

	 	$keywordText=NULL;
	 	$charText=NULL;
	 	 
	 	if($lastChar==")") {
	 		$matches=array();
	 		$result=preg_match('/^(.+)\((.+)\)$/', $text, $matches);
	 		
	 		if(!$result) return false;
	 		
	 		$keywordText=$matches[1];
	 		$charText=$matches[2];
	 	} else {
			$keywordText = mb_substr ( $text, 0, $len - 2, $encoding );
	 		$charText=mb_substr ( $text, $len - 1, 1, $encoding );
	 	}
	 	
		// if it is longer, we need to see if there is a single space separated character at the end
		// we can assume there are both parts present
		$chars = self::getCharsByChar ( $charText, $systemsID );
		$keyword = self::getCharsByKeyword ( trim ( $keywordText), $systemsID );
		
		// see if both match
		if (count ( $chars ) !== count ( $keyword )) {
			
			if (count ( $chars ) == 1) {
				if (count ( $keyword ) > 0)
					foreach ( $keyword as $k ) {
						if ($k->id == $chars [0]->id) {
							return $chars; // if one of the keywords matches the only result from char, return it
						}
					}
				else
					// also if it is basically the same, but an article or "to" is missing
					foreach ( array (
							"to",
							"a",
							"the" 
					) as $prefix ) {
						if ($prefix . " " . $keywordText === $chars [0]->keyword) {
							return $chars;
						}
					}
			}
			
			return self::MISMATCH;
		} else if (count ( $chars ) > 1) {
			return array_merge ( $chars, $keyword ); // there are multiple, just return as it is
		} else if (count ( $chars ) == 0) {
			return self::NOT_FOUND;
		} else if ($chars [0]->id === $keyword [0]->id) {
			return $chars; // only one and both match - great, let's return it
		} else {
			return self::MISMATCH; // the char and the keyword do not match
		}
	 	
	 }
	 
	 public static function autoconvert($char) {
	 	if($char->system==6) {
	 		$comp=explode("+",$char->notes);
	 			
	 		if(count($comp)>1) {
	 			$autofixable=(count($char->components)===0);

	 			$result=array();
	 			// 				$errors[]="Composition in notes found.";
	 	
	 			foreach($comp as $c) {
	 				$guessed=self::guessComponent($c, $char->systemValue->allInheritedIds);
	 				if(count($guessed)!==1)
	 					$autofixable=false;
	 				else {
	 					if(!isset($result[$guessed[0]->id]))
	 						$result[$guessed[0]->id]=1;
	 					else
	 						$result[$guessed[0]->id]++;
	 				}
	 			}
	 			if($autofixable) {
	 				foreach ($result as $id=>$newComponentCount) {
	 					$composition=new Composition();
	 					$composition->charId=$char->id;
	 					$composition->subcharId=$id;
	 					$composition->count=$newComponentCount;
	 					$composition->insert();
	 				}
	 				
	 				$char->notes="";
	 				$char->save();
	 				return true;
	 			}
	 			else {
	 				return false;
	 			}
	 		}
	 	}	 	
	 }
	 
	public static function diagnose($char) {
		$mnemo=$char->mnemo; 
		$mnemosystem=$char->systemValue->mnemosystem;
		
		$errors=array();
		/*
		if($mnemosystem!='both') {
			return "No diagnostics available for this kind of systems.";
		}
		*/
		//a correct entry should contain:
		//[k]
		//2x[c#]
		//[s#]
		//[a#]
		
		//keyword should match (or approx. match) the keyword entry
		//transcription must match the soundword and archetype
		//compositions need to correspond to what is set in the char
		//no invalid <img links
		//perhaps no HTML

		$keyword=NULL;
		$archetype=NULL;
		$archetypeTone=NULL;
		$soundword=NULL;
		$soundwordTone=NULL;
		
		$matches=array();
		$result=preg_match_all("#\[k\](.+?)\[/k\]#", $mnemo, $matches);
		
		if($result===1) {
			$keyword=$result[0][1];
		} else {
			if($result===0) $errors[]="No keyword marked.";
			else if($result>1) $errors[]="Multiple keywords marked.";				
			else $errors[]="An error with keyword markup.";
		}
		
		$result=preg_match_all('#\[a(\d+)\](.+?)\[/a\]#', $mnemo, $matches);
		if($result===1) {
			$archetypeTone=$result[0][1];
			$archetype=$result[0][2];
		} else {
			if($result===0) $errors[]="No archetype marked.";
			else if($result>1) $errors[]="Multiple archetypes marked.";
			else $errors[]="An error with archetype markup.";
		}
		$result=preg_match_all('#\[s(\d+)\](.+?)\[/s\]#', $mnemo, $matches);
		if($result===1) {
			$soundwordTone=$result[0][1];
			$soundword=$result[0][2];
		} else {
			if($result===0) $errors[]="No soundword marked.";
			else if($result>1) $errors[]="Multiple soundword marked.";
			else $errors[]="An error with soundword markup.";
		}
		
		//check if the tone numbers match
		if($soundwordTone!==NULL && $archetypeTone!==NULL) {
			if($soundwordTone!=$archetypeTone) {
				$errors[]="Soundword tone does not match the archetype tone.";
			}
			
			//check if the tone numbers in the mnemo match with the ones in the transcription (as in dictionary or as manually set)
			
		}
				
		$result=preg_match_all('#\[c(\d+)\](.+?)\[/c\]#', $mnemo, $matches);
		
		if($char->system==6) {
			$comp=explode("+",$char->notes);
			
			if(count($comp)>1) {
				$autofixable=(count($char->components)===0);
				$autofixerrors="";
// 				$errors[]="Composition in notes found.";
				foreach($comp as $c) {
					$guessed=self::guessComponent($c, $char->systemValue->allInheritedIds);
					if(!is_array($guessed) || count($guessed)!==1) {
						$autofixable=false;
						
						if(is_array($guessed)) {//$autofixerrors=implode(",", $guessed);
						}else $autofixerrors.=$c." : ".$guessed;
					}
				} 
				if($autofixable)
					$errors[]=self::AUTOCONVERTIBLE;
				else
					$errors[]="Non-autoconvertible components from notes.".$autofixerrors;
			}
		}
		/*
		if(empty($errors))
			return "OK";
		
		else return implode("\r\n",$errors);
		*/
		return  $errors;
	}
}
?>