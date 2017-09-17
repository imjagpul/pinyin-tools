<?php

/**
 * How the input is processed.
*/
class CharacterModeInput {
	/** The input is left as-is. */
	const CHARMOD_NO_CONVERSION=1;
	/** All input is converted to simplified characters. */
	const CHARMOD_CONVERT_TO_SIMPLIFIED=2;
	/** All input is converted to traditional characters. */
	const CHARMOD_CONVERT_TO_TRADITIONAL=3;
	
}
