<?php
/**
 * Which variant is used in displaying the dictionary results.
 */
class CharacterModeAnnotations {
	/** Only the simplified variant is to be displayed. */ 
	const CHARMOD_SIMPLIFIED_ONLY=1;
	/** Only the traditional variant is to be displayed. */ 
	const CHARMOD_TRADITIONAL_ONLY=2;
	/** Both variants are to be displayed, simplified first. */ 
	const CHARMOD_ALLOW_BOTH_PREFER_SIMP=3;
	/** Both variants are to be displayed, traditional first. */ 
	const CHARMOD_ALLOW_BOTH_PREFER_TRAD=4;
	
}