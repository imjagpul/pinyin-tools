<?php

abstract class DictEntry extends CActiveRecord {
	/**
	 * Splits the translation string (e.g. "asdf/asdf/sadf") into an array of translations ("asdf", "asdf", "sadf")
	 * @return array
	 */
	public function getTranslationsArray() {
		return preg_split('#/#', $this->translation, null, PREG_SPLIT_NO_EMPTY);
	}

	/**
	 *
	 * @param boolean $simplified
	 * 			if the simplified version should be returned (as opposed to the traditional)
	 */
	public function getText($simplified) {
		//@TODO mayble implement "leave as it is" mode
		if($simplified) return $this->simplified;
		else return $this->traditional;
	}
	
}

?>