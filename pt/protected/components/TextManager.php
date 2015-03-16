<?php
class TextManager {
	/**
	 * Returns the desired TextData.
	 * @param String $id
	 * @throws Exception
	 * @return TextData
	 */
		public function getText($id) {
			if($id!="sanzijing")
				throw new Exception("Not yet implemented.");
			return new TextData();
		}
} 
?>