<?php
class Utilities {
	//taken from php manual ( function.ini-get.html )
	public static function return_bytes($val) {
	    $val = trim($val);
	    $last = strtolower($val[strlen($val)-1]);
	    switch($last) {
	        // The 'G' modifier is available since PHP 5.1.0
	        case 'g':
	            $val *= 1024;
	        case 'm':
	            $val *= 1024;
	        case 'k':
	            $val *= 1024;
	    }
	
	    return $val;
	}
		
	public static function getPhpMaxUploadInBytes() {
		return 
		  	min(self::return_bytes(ini_get('upload_max_filesize')), 
				self::return_bytes(ini_get('post_max_size')));
	}
	
	public static function colorAsHex($col) {
		return sprintf("%'06x", $col);
	}	
	
	public static function parseColorAsHex($value) {
		if (!preg_match('/^[0-9A-F]{6}$/i', $value))
			return null;
		
		return 0+"0x$value";
	}	
}