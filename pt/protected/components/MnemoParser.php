<?php
class MnemoParser {
	const STANDARD="{FOREACH_COMP}[c{COMP#}]{COMP_NAME}[/c]{/FOREACH_COMP} [k]{KEYWORD}[/k] ";

	private static $archeTypes=array(
				'two giants',
				'giant',
				'two fairies',
				'fairy',
				'two Teddies',
				'teddy',
				'two dwarfs',
				'two dwarves',
				'dwarf',
				'robot',
				'ghostly' //hack
		);
			
	private static $archeTypesSorted=array(
				1=>array('two giants',
				'ghostly giant',
				'giant'),
				2=>array('two fairies',
				'ghostly fairy',
				'fairy'),
				3=>array('two Teddies',
				'ghostly teddy',
				'teddy'),
				4=>array('two dwarfs',
				'two dwarves',
				'ghostly dwarf',
				'dwarf'
				),
				5=>array('robot'),
		);		
	
	static function suggestMiddleToNew($char) {
		$result=$char->mnemo;
		$kw=$char->keyword;
		$foundTone=false;
		
		//check if it has two lines, otherwise fail
		$lines=split("\n", $char->mnemo);
		$newLines=array();
		for ($i=0; $i<count($lines); $i++) {
			$lines[$i]=trim($lines[$i]);
			if(!empty($lines[$i])) {
				$newLines[]=$lines[$i];
			}
		}
		$lines=$newLines;
		
		if(count($lines)!=2) {
			return -1;
		}
		list($first, $second)=$newLines;
		
		//on the second line, search for an archetype
		foreach(self::$archeTypesSorted as $tone =>$variants) {
			foreach($variants as $v) {
				if(stripos($second, $v)!==FALSE) {
					$result=preg_replace("@$v@i", "[s$tone]$0[/s]", $result);
					$foundTone=true;
					break 2;
				}
			}
		}
		if(!$foundTone) {
			return -2;
		}
		
		//on the second line, search for a soundword
		//see if it matches the dictionary
		
		//on the first line, search for composition and keyword
		if(stripos($first, $char->keyword)!==FALSE) {
			$result=preg_replace("@$kw"."[a-z]*@i", "[k]$0[/k]", $result, 1);
		} else {
			return -3; //no keyword found
		}
		
		//search also for the components
		//first need to get them, as they are not set
		$compositions=$char->components;
		if(empty($compositions)) {
			$s=new Suggestion();
			$compositions=$s->suggestComposition($char);
			if(count($compositions)==0) {
				return -4;//no component available
			} else if(count($compositions)>1) {
				return -5;//multiple components available
			}
			
			//we take the only option
			$compositions=array_shift($compositions);
			var_dump($compositions);die;
			foreach($compositions as $c) {
// 				$c
				$ckw=$c->keyword;
				$cnum=$c->id;
				if(stripos($first, $ckw)===FALSE) {
					return -6; //unmatched keyword
				} else {
					$result=preg_replace("@$ckw"."[a-z]*@i", "[c$cnum]$0[/c]", $result, 1);
				}
				
			}
		}
		
		return $result;
	}
	

	static function colorToTone($color) {
		$toneColor=NULL;
		switch($color) {
			case "0000ff":
				$toneColor=4; break;
			case "ffaa00":
				$toneColor=2; break;
			case "00aa00":
				$toneColor=3; break;
			case "ff0000":
				$toneColor=1; break;
			default:
				throw new Exception();
		}
		
		return $toneColor;
	}
		
	static function archetypePos($word, $toneColor=NULL) {
		if($toneColor==null) 
			foreach(self::$archeTypesSorted as $toneColor => $archetype) {
				$val=self::archetypePos($word, $toneColor);
				if($val!==false) return $val;
			}

		//check if any of the archetypes are present
		foreach(self::$archeTypesSorted[$toneColor] as $archetype) {
			$archetypePos=stripos($word, $archetype);
			if($archetypePos!==false) {
				//found archetype
				return array($archetypePos, $archetype);
			}
		}
		
		//nothing found
		return false;
	}
	
	static function archetypeToToneColor($archetype) {
		foreach(self::$archeTypesSorted as $toneColor => $archetypeArray) {
			foreach($archetypeArray as $archetypeVal) {
				if($archetype==$archetypeVal) {
					return $toneColor;
				}
			}
		}

		throw new Exception("archetype '$archetype' not recognized");
	}
	
	static function checkLeftover($original, $match, $candidates) {
		$leftover=trim(str_ireplace($match, "", $original));
		
		if(strlen($leftover)==0) return NULL;
		
		foreach ( $candidates as $c) {
			if (stripos ($leftover, $c ) !== FALSE) { // found another within the string
				return $c;
			}
		}
		
		return NULL;
	}
	
	static function doubleMatch($original, $firstID, $firstKw, $secondID, $secondKw) {
// 		var_dump($original);die;
		
		$firstNew="[c$firstID]".$firstKw."[/c]";
		$secondNew="[c$secondID]$secondKw"."[/c]";
		
		$result=str_replace($firstKw, $firstNew, $original);
		$result=str_replace($secondKw, $secondNew, $result);
		
		return $result;
	}
	
	static function archetypeDoReplace($archetypePos, $archetype, $whole, $word, $whitespace, $newMnemo, $toneColor=null) {
		if(empty($archetype)) {
			throw new Exception("empty archetype");
		}
		
		//both soundwords are sometimes connected in one markup
		//let's check if it is the case
		$leftover=trim(str_ireplace($archetype, "", $word));
		$extra=0; //how many matches found minus one
		
		if($toneColor==NULL) $toneColor=self::archetypeToToneColor($archetype);
		
		if(strlen($leftover)>0) {
		//there is both archetype and soundword in this tag
			// 						$newMnemo.="BOTH_JOINED ($word; #$leftover#)";
			//before or after?
			$extra++;
			$archetypeNew="[a$toneColor]".substr($word, $archetypePos, strlen($archetype))."[/a]";
			$leftoverNew="[s$toneColor]$leftover"."[/s]";
		
			if($archetypePos===0) {
			$wholeNew=$archetypeNew.' '.$leftoverNew.$whitespace;
			} else {
			$wholeNew=$leftoverNew.' '.$archetypeNew.$whitespace;
			}
			$newMnemo=str_replace($whole, $wholeNew, $newMnemo);
		
			} else {
			//there is only archetype in this tag
			//$newMnemo.="ONLY";
			$newMnemo=str_replace($whole, "[a$toneColor]$word"."[/a]$whitespace", $newMnemo);
			}
			
		return array($newMnemo,$extra,$toneColor);
	}
	
	static function preprocess($newMnemo) {
		$newMnemo=str_replace("<i>", "", $newMnemo);
		$newMnemo=str_replace("</i>", "", $newMnemo);
		$newMnemo=str_replace("<br>", "", $newMnemo);
		$newMnemo=str_replace("<br />", "", $newMnemo);
		$newMnemo=str_replace('<span style="font-weight:600; font-style:italic;">', "", $newMnemo);
		$newMnemo=str_replace("<b>.</b>", ".", $newMnemo);
		$newMnemo=str_replace("<b>wheel</b>", "[m]wheel[/m]", $newMnemo);
		$newMnemo=str_replace("<b>wheel </b>", "[m]wheel[/m] ", $newMnemo);
		$newMnemo=str_replace("<b>wheels</b>", "[m]wheels[/m]", $newMnemo);
		$newMnemo=str_replace("<b>wheels </b>", "[m]wheels[/m] ", $newMnemo);
		
		//hardcoded synonyms
		$kw=array(
				'Harry'=>15961,
			'octopus'=>15971,
			'biplane'=>15949,
			'shake hands'=>16020,
			'shakes hands'=>16020,
		);
		
		$totalCount=0;
		foreach($kw as $keyword=>$id) {
			$count=0;
			$newMnemo=str_replace("<b>$keyword </b>", "[c$id]$keyword"."[/c] ", $newMnemo, $count);
			if($count>0) $totalCount+=$count;
			$newMnemo=str_replace("<b>$keyword</b>", "[c$id]$keyword"."[/c]", $newMnemo, $count);
			if($count>0) $totalCount+=$count;
		}
		return array($newMnemo, $totalCount);
	}
	
	/**
	 * 
	 * @param Char $char
	 * @return string
	 */
	static function suggestOldToNew($char, $force=false) {
		$str=$char->mnemo; 
		$newMnemo=$char->mnemo;
		
		$reg='@<span style="(?:font-weight:600;)?\s*(?:font-style:italic;)?\s*color:#(.+?);">(.*?)(\s*)</span>@';
		$regB='@<(b)>(.+?)(\s*)</b>@'; //the B is in paranthesis in order to have the same group number as the pattern above
		
		$matchCount=0;
		$matchCount=preg_match_all($reg, $str, $matches);

		if($matchCount==0 && 0==preg_match_all($regB, $newMnemo)) return null;
		
		$archetypeFound=false;
		$soundwordFound=false;
// 		$toneColor=NULL;

		$blackAlternative=array(array(), array(), array(), array());


		$result=self::preprocess($newMnemo);
		$newMnemo=$result[0];
		$replacedInPreprocess=$result[1];
		$realCountOfMarked=0;
		
		//**************  first parse the parts marked with font color ***************
		for($i=0; $i<$matchCount; $i++) {
			$whole=$matches[0][$i];
			$color=$matches[1][$i];
			$word=trim($matches[2][$i]);
			$whitespace=$matches[3][$i];
			 
			$toneColor=0;
			$thisIsArchetype=false;
			
			if(strlen($word)==0) {
				//whitespace only - get rid of it
				$newMnemo=str_replace($whole, " ", $newMnemo);
				continue;
			}
				
// 			if(strlen($word)==0) continue; //skip whitespace only matches
			
			if($color=="000000") {
				//marked with color black, process later as bold
// 				$blackAlternative[]=$whole;
				
				$blackAlternative[0][]=$matches[0][$i];
				$blackAlternative[1][]=$matches[1][$i];
				$blackAlternative[2][]=$matches[2][$i];
				$blackAlternative[3][]=$matches[3][$i];
				
				continue;
			}
			
			$toneColor=self::colorToTone($color);
			
			//check if it is a (corresponding) archetype
// 			self::$archeTypesSorted
			if(!$archetypeFound) {
				$archetypePos=self::archetypePos($word, $toneColor);
				if($archetypePos!==false) {
					//found archetype
					$archetype=$archetypePos[1];
					$archetypePos=$archetypePos[0];
					
					$archetypeFound=true;
					$thisIsArchetype=true;
					
					$result=self::archetypeDoReplace($archetypePos, $archetype, $whole, $word, $whitespace, $newMnemo, $toneColor);
					$newMnemo=$result[0];
					$realCountOfMarked+=$result[1];
					
					continue;
				}
			}
			
			if(!$thisIsArchetype) {
				//this is the soundword
				$newMnemo=str_replace($whole, "[s$toneColor]$word"."[/s]$whitespace", $newMnemo);
				$soundwordFound=true;
			}
		}
		
		//**************  the process keyword and components ***************
		$matchCount=preg_match_all($regB, $newMnemo, $black);
		if($matchCount==0) $black= $blackAlternative;
		else {
			$black[0] = array_merge ( $blackAlternative [0], $black [0] );
			$black[1] = array_merge ( $blackAlternative [1], $black [1] );
			$black[2] = array_merge ( $blackAlternative [2], $black [2] );
			$black[3] = array_merge ( $blackAlternative [3], $black [3] );
		}

		//filter whitespace only
		$newBlack=array(array(), array(), array(), array());
		
		for($i=count($black[0])-1; $i>=0; $i--) {
			$whole=$black[0][$i];
// 			$color=$black[1][$i]; //not used
			$word=trim($black[2][$i]);
			$whitespace=$black[3][$i];
			

			if(strlen($word)==0) {
				//whitespace only - get rid of it
				$newMnemo=str_replace($whole, " ", $newMnemo);
				continue; 
			}
			
			$newBlack[0][] = $black [0][$i]  ;
			$newBlack[1][] = $black [1][$i]  ;
			$newBlack[2][] = $black [2][$i]  ;
			$newBlack[3][] = $black [3][$i]  ;
		}
		$black=$newBlack;

		$countOfMarked=count($black[0]);
		$keywordMatches=false;
		$componentMatches=array();
		$unmatchedMarkups=array();
		
		for($i=0; $i<count($char->components); $i++) {
			$componentMatches[]=false;
		}

		//check if component count (as set) matches the number of marked words (+1 because of keyword),
		//otherwise return as error
		$expectedComponentsCount=1-$replacedInPreprocess;
		if(!$archetypeFound) $expectedComponentsCount++; 
		if(!$soundwordFound) $expectedComponentsCount++;
		
		foreach($char->components as $comp) {
			$expectedComponentsCount+=$comp->count;
		}
		
		$realCountOfMarked+=$countOfMarked;
		for($i=0; $i<$countOfMarked; $i++) {
			$whole=$black[0][$i];
// 			$color=$black[1][$i]; //not used
			$word=trim($black[2][$i]);
// 			$word=$black[2][$i];
			$whitespace=$black[3][$i];
			
			//now we need to guess what is it what is marked

			//see if it could be a component
			//first preload all keywords
			$subcharKeywords=array();
			foreach($char->components as $c) {
				$subcharKeywords[$c->subchar->id]=$c->subchar->keyword;
			} 
			
			for($j=0; $j<count($char->components); $j++) {
				$subchar=$char->components[$j]->subchar;
				$subKw=$subchar->keyword;
				
				if(!$componentMatches[$j] && stripos($word, $subKw)!==FALSE) {
					$componentMatches[$j]=true;
					$subID=$subchar->id;
					unset($subcharKeywords[$subID]);
					
					$other=self::checkLeftover($word, $subKw, $subcharKeywords);

					if($other===NULL)
						$newMnemo=str_replace($whole, "[c$subID]$word"."[/c]$whitespace", $newMnemo);
					else { //two compoments in one markum
						$otherId=array_search($other, $subcharKeywords);
// 						self::archetypeDoReplace($archetypePos, $archetype, $whole, $word, $whitespace, $newMnemo)
						$newMnemo=str_replace($whole, self::doubleMatch($word, $subID, $subKw, $otherId, $other), $newMnemo);
						$realCountOfMarked++;
						
						//perhaps mark componentd matched
						
// 						$newMnemo=str_replace($whole, "[c$otherId]$other"."[/c]### [c$subID]$word"."[/c]$whitespace", $newMnemo);
					}
					continue 2;
				}
			}
			
			//see if it could be keyword
// 			var_dump($word);var_dump($char->keyword);
			if(!$keywordMatches && stripos($word, $char->keyword)!==FALSE) {
				//seems to be keyword
				$newMnemo=str_replace($whole, "[k]$word"."[/k]$whitespace", $newMnemo);
				$keywordMatches=true;
				continue;
			}
			
			//see if it could be archetype
			$archetypePos=self::archetypePos($word);
			if($archetypePos!==false) {
				$archetype=$archetypePos[1];
				$archetypePos=$archetypePos[0];
				
				$result=self::archetypeDoReplace($archetypePos, $archetype, $whole, $word, $whitespace, $newMnemo);
				$newMnemo=$result[0];
				$realCountOfMarked+=$result[1];
				$toneColor=$result[2];
				continue;
			}
			
			$unmatchedMarkups[]=$i;
		}
		
		$finished=false;
		if(count($unmatchedMarkups)==0) {
			$finished=true;
		}
			
		//if there is only one missing, we can guess it easily
		
		if(count($unmatchedMarkups)==1) {
			$whole=$black[0][$unmatchedMarkups[0]];
			$word=trim($black[2][$unmatchedMarkups[0]]);
			$whitespace=$black[3][$unmatchedMarkups[0]];
			
			$finished=false;
			
			if(!$keywordMatches && array_search(false, $componentMatches)===FALSE) {
				$newMnemo=str_replace($whole, "[k]$word"."[/k]$whitespace", $newMnemo);
				$finished=true;
			}
			
			for($j=0; $j<count($char->components); $j++) {
				if(!$componentMatches[$j]) {
					$componentMatches[$j]=true;
					$subchar=$char->components[$j]->subchar;
					$subID=$subchar->id;
						
					$newMnemo=str_replace($whole, "[c$subID]$word"."[/c]$whitespace", $newMnemo);
					$finished=true;
					break;
				}
			}
			
			if(!$finished) {
				if(!isset($toneColor)) {
					$finished=false;
				} else {
					//it is soundword
					$newMnemo=str_replace($whole, "[s$toneColor]$word"."[/s]$whitespace", $newMnemo);
					$finished=true;
				}
			}
		}
		
		if($expectedComponentsCount > $realCountOfMarked && !$force) { //too many marked
				return -2;
		}
		if($expectedComponentsCount < $realCountOfMarked && !$force) {
				return -3;
		}
		
// 		var_dump($expectedComponentsCount);
// 		var_dump($realCountOfMarked);
		//remove all markup also
		//all italics
		
		//leftover spans
		//$newMnemo=str_replace("</span>", "", $newMnemo);
		
		if($finished==false && !$force) {
			return -4;
		}
		
		//quick hack (because of a trim that swallows whitespace) 
		$newMnemo=str_replace("][", "] [", $newMnemo);
		
// 		return "".$changes;
		return $newMnemo;
	}
	
	/** Takes a mnemo a produces a keyword entry. */
	static function parseOld($str) {
		
		/*
As he enters, he takes his <b>berret </b>with <b>two hands</b>, and <b>raises </b>it. 
The <span style="font-weight:600; color:#ff0000;">giant </span>has already learned this strange custom of this 
<span style="font-weight:600; color:#ff0000;">Schenke</span>.
		 */
		//BEWARE: both soundwords are sometimes connected
		
// 		$reg='span';
		$reg='@<span style="font-weight:600; color:#(.+?);">(.+?)</span>@';
		
		$tokens=array();
		$matchCount=0;
		$matchCount=preg_match_all($reg, $str, $matches);
		if($matchCount>0) {
			for($i=0;$i<count($matches[2]);$i++) {
				$m=$matches[2][$i];
				
				$mOrig=$m;
				foreach(self::$archeTypes as $a) {
					$m=str_ireplace($a, '', $m);
				}
				$m=trim($m);
				if(!empty($m)) {
					$tokens[]=$mOrig;
// 					$tokens[]=$matches[0][$i];;
				}
			}
			
// 			$tokens=array_merge($tokens, $matches[0]);
// 			$tokens=array_merge($tokens, $matches[1]);
		}
		else return NULL;
	
		return implode("; ",$tokens);
// 		return self::STANDARD.implode(" ",$tokens);
		// 		return self::STANDARD.implode(" ",$tokens);
	}	
	
	/** Takes a mnemo a produces a keyword entry. */
	static function parseStandard($str) {
		$tokens=array(); 
		//note the order is relevat
// 		preg_match_all('#\[c\d+\].+?\[/c\]#', $str, $matches);
// 		$tokens=array_merge($tokens, $matches[0]);
// 		preg_match_all('#\[k\].+?\[/k\]#', $str, $matches);
// 		$tokens[]=$matches[0][0];
		$matchCount=0;
		$matchCount=preg_match_all('#\[a\d+\].+?\[/a\]#', $str, $matches);
		if($matchCount>0)
			$tokens[]=$matches[0][0];
		else return NULL;
		
		$matchCount=preg_match_all('#\[s\d+\](.+?)\[/s\]#', $str, $matches);
		if($matchCount>0){
// 			$tokens[]=$matches[0][0];
// 			return $matches[0][0];
			return $matches[1][0];
		}else return NULL;
		
// 		return self::STANDARD.implode(" ",$tokens);
// 		return self::STANDARD.implode(" ",$tokens);
	}
	
}