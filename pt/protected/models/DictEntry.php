<?php

abstract class DictEntry extends CActiveRecord {
	/**
	 * Splits the translation string (e.g. "asdf/asdf/sadf") into an array of translations ("asdf", "asdf", "sadf")
	 * @return array
	 */
	public function getTranslationsArray() {
		return preg_split('#/#', $this->translation, null, PREG_SPLIT_NO_EMPTY);
	}

	public abstract function getLength();
	
	/**
	 *
	 * @param boolean $characterModeSimplified
	 * 			if the simplified version should be returned (as opposed to the traditional)
	 */
	public function getText($characterModeSimplified) {
		if($characterModeSimplified)
			return $this->simplified;
		else
			return $this->traditional;
	}
}

?>