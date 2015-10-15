<?php
//powers of two -1
$masks=array(1,3,7,15,31,63);

/**
 * Returns the string containing utf-8 encoded character corresponding to the given codepoint (as string in hex).
 **/
function unicodeChrHex($codepoint) {
	return html_entity_decode("&#x$codepoint;"); //default is UTF-8	
}

/**
 * Returns the string containing utf-8 encoded character corresponding to the given codepoint.
 **/
function unicodeChr($codepoint) {
	settype($codepoint, "integer"); //this implementation is elegant but not very efficient
	return html_entity_decode("&#$codepoint;"); //default is UTF-8
	
    /*
	if($codepoint<=0x007F) return ord($codepoint);// backward compatible with ASCII
	else if($codepoint<=0x07FF) $len=2;
	else if($codepoint<=0xFFFF) $len=3;
	else if($codepoint<=0x1FFFFF) $len=4;
	else if($codepoint<=0x3FFFFFF) $len=5;
	else if($codepoint<=0x7FFFFFFF) $len=6;
	else throw new Exception('Codepoint is too large!');
	
	
	//handle the first byte
	$relevantInFirstByte= 7 - $bytesCount; //how many bites are relevant in the first byte
	$mask = $masks[$relevantInFirstByte-1]; //the bit mask - for the first byte
	
	*/
	
}

/**
 * Returns the unicode value (codepoint) of the first character of the given utf-8 string.
 **/
function unicodeOrd($str) {
	//powers of two -1
	global $masks; //$masks=array(1,3,7,15,31,63);
	
	//we are only interested in the first character
	$u= mb_substr($str, 0, 1, 'utf-8');
	
	//strlen is byte based
	$bytesCount=strlen($u);
	
	//see utf-8 definition
	if($bytesCount<1 || $bytesCount>6) throw new Exception('Invalid!');
	
	//utf-8 is backwards compatible with ASCII
	if($bytesCount==1) return ord($u);
	
	//At this point:
	//2 <= bytesCount =< 6
	
	//handle the first byte
	$relevantInFirstByte= 7 - $bytesCount; //how many bites are relevant in the first byte
	$mask = $masks[$relevantInFirstByte-1]; //the bit mask - for the first byte
	$result = (ord($u[0]) & $mask) << (6* ($bytesCount-1));
	
	//handle the other bytes
	for($i=1;$i<$bytesCount;$i++) {
		//there are always 6 bites in each byte relevant
		$mask=63;
		
		$addition=(ord($u[$i]) & $mask) << (6* ($bytesCount-1-$i));
		//echo('   '.$addition);
		$result+=$addition;
		//$result+= (ord($u[$i]) & $mask) << (6* ($bytesCount-1-$i));
	}
	
	//return the result
	return $result;
	
	//Example:
	//Output:
	//Expected result:     20886
	//                    0x5196
	//          10100011 0010110
	//
	//Input:
	//Url:  blabla.php?c=%E5%86%96
	//Byte values: 229 134 150 
	// 			    E5  86  96
	//11100101 10000110 10010110
	//relevant:
	//xxxx0101 xx000110 xx010110
	//     101   000110   010110
	//          10100011 0010110
}
/**
 * A utf-8 implementation of str_spli
 * @param string $str
 * 		a string in utf-8 encoding
 * @return array
 *      an array whose every element is one character from the given string
 */
function unicodeSplitToCharArray($str) {
	$len=mb_strlen($str, 'utf-8');
	$result=array();
	for ($i=0; $i<$len; $i++) {
	  $result[$i]=mb_substr($str, $i, 1, 'utf-8');
	}
	return $result;
}

//some testing
//echo unicodeChr(0x4E1C);
//$ar=unicodeSplitToCharArray('业丞丫');
//var_dump($ar);

?>
