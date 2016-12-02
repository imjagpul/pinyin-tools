<?php
class DictionaryFileParser
{
	/*
		public static function parse($uploadedFile) {
			$separator="\t";
			$result=array();
			
			//foreach line
			while($line=$uploadedFile->readLine()) {
				if($line[0]=='#')
					continue;
				
				//$regex="%(.+)\s+(.+)\s+\[(.+)\]\s+/(.+)/%";
				$res=preg_match($regex, $line, $result[]);
			}
			
			return $result;
		}
*/
	
	
	/*
	 * Expected format:
	 * (\S++) (\S++) \[([^\]]++)\] /?+(.*?)/?+$
	 * TRADIT SIMPLI [TRANS] /TRANSC/
	 * or
	 * TRADIT SIMPLI [TRANS] TRANSC
	 * 
	 * $traditional, $simplified, $transcription, $translation
	 */
	
		public static function parseAndAdd($uploadedFile, $dictModel) {
			$separator="\t";
			$badLines=false;
			//foreach line
			while($line=$uploadedFile->readLine()) {
				if($line[0]=='#')
					continue;
		
				$currentValue=array();

				$regex="^\s*(\S++)\s++(\S++)\s++\[([^\]]++)\]\s*+/?+(.*?)/?+$";

				//we need to use mb_eregi (otherwise false whitespace in the middle of a multibyte char would be matched)
				$res=mb_eregi($regex, $line, $currentValue);

				if($res===FALSE) { //check if failed to match
					$badLines[]=$line;
					continue;
				}
				
				$dictModel->addEntry($currentValue[1], $currentValue[2], strtolower($currentValue[3]), $currentValue[4]);
			}
			
			return $badLines;
		}
		
}