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
	 * @param Enum $characterMode
	 * 			if the simplified version should be returned (as opposed to the traditional)
	 */
	public function getText($characterMode) {
		switch($characterMode) {
			case CharacterMode::CHARMOD_SIMPLIFIED_ONLY:
			case CharacterMode::CHARMOD_CONVERT_TO_SIMPLIFIED:
			case CharacterMode::CHARMOD_ALLOW_BOTH_PREFER_SIMP:
				return $this->simplified;
			default:
				return $this->traditional;
				
		}
	}
	
}

?>