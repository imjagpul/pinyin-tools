<?php
/**
 * 
 * $dictionaryQuery
 */
class DictionaryWidget extends CWidget {
	private $_dictionaryQuery;
	/**
	 * 
	 * @param string $d
	 * 		the queried string (or NULL)  
	 * @throws Exception
	 */
	public function setDictionaryQuery($d) {
		if(!is_string($d) && $d!==NULL) {
			throw new Exception("Dictionary query has to be a string (but is ".gettype($d).").");
		}
		$this->_dictionaryQuery = $d;
	}
	public function outputEntries($r, $formatter) {
		foreach ( $r as $entry ) {
			//display depends on the user settings
			$userVariant=UserSettings::getCurrentSettings()->variant;
			
			if($userVariant=='simplified_only') {$first = $entry->simplified; $alt=NULL;}
			else if($userVariant=='traditional_only') {$first = $entry->traditional; $alt=NULL;}
			else if($userVariant=='simplified_prefer') {$first = $entry->simplified; $alt = $entry->traditional;}
			else if($userVariant=='traditional_prefer') {$first = $entry->traditional; $alt = $entry->simplified;}
				
			?>
<p>
	<span class="cn"> <?php echo $first; ?> </span> 
	<?php if(!is_null($alt) && $first!==$alt) { ?> <br /> <span class="alternate"> <?php echo $alt; ?></span> <?php } ?> 
			 <br />
			<?php echo $formatter->format($entry->transcription); ?>
			<?php echo CHtml::hiddenField('transcriptionOriginal', $entry->transcription); ?>
			<br /> <?php echo $entry->translation; ?>
			</p>
<?php
		}
	}
	
	/**
	 * 
	 * @param Dictionary $dict
	 * @param FormattersFactory $factory
	 */
	private function outputDict($dict, $factory) {
		if(empty($this->_dictionaryQuery)) 
			return;
		
		echo '<h2>' . $dict->name . '</h2>';
		$formatter = $factory->getFormatterForDictionaryWidget ( $dict->transcriptionName ); // @TODO not fully efficiet, maybe use ID instead
		
		$criteria = new CDbCriteria ();
		$criteria->compare ( 'simplified', $this->_dictionaryQuery, false, 'AND' );
		$criteria->compare ( 'traditional', $this->_dictionaryQuery, false, 'OR' );
		$r = DictEntryChar::model ()->findAllByAttributes ( array (
				'dictionaryId' => $dict->id 
		), $criteria );
		if (! empty ( $r )) {
			$this->outputEntries ( $r, $formatter );
		}
		
		$criteria = new CDbCriteria ();
		$criteria->compare ( 'simplified_begin', $this->_dictionaryQuery, false, 'AND' );
		$criteria->compare ( 'traditional_begin', $this->_dictionaryQuery, false, 'OR' );
		$r = DictEntryPhrase::model ()->findAllByAttributes ( array (
				'dictionaryId' => $dict->id 
		), $criteria );
		
		if (! empty ( $r )) {
			echo '<h3>Phrases starting with the given character</h3>';
			$this->outputEntries ( $r, $formatter );
		}
		
		$criteria = new CDbCriteria ();
		$criteria->compare ( 'simplified_rest', $this->_dictionaryQuery, false, 'AND' );
		$criteria->compare ( 'traditional_rest', $this->_dictionaryQuery, false, 'OR' );
		$r = DictEntryPhrase::model ()->findAllByAttributes ( array (
				'dictionaryId' => $dict->id 
		), $criteria );
		if (! empty ( $r )) {
			echo '<h3>Phrases ending with the given character</h3>';
			$this->outputEntries ( $r, $formatter );
		}
	}
	
	public function run() {
		$allDicts = Dictionary::model ()->findAll (); // @TODO limit to enabled and matching dictionaries
		$factory = new FormattersFactory ();
		
		echo '<div id="dict-portlet">';
		
		if(!empty($this->_dictionaryQuery))
			foreach ( $allDicts as $dict ) {
				$this->outputDict ( $dict, $factory );
			}		
			
		echo '</div>';
	}
}