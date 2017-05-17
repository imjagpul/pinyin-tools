<?php
class CharacterMode {
	//this could be refactored to an Enum, just as AnnotatorMode
	const CHARMOD_SIMPLIFIED_ONLY=1;
	const CHARMOD_TRADITIONAL_ONLY=2;
	const CHARMOD_CONVERT_TO_SIMPLIFIED=3;
	const CHARMOD_CONVERT_TO_TRADITIONAL=4;
	const CHARMOD_ALLOW_BOTH_PREFER_SIMP=5;
	const CHARMOD_ALLOW_BOTH=5;
	
}