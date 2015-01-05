<?php
class MnemonicsEditor {

	/**
	 * 
	 * @var CActiveForm
	 */
// 	private $form;
	/**
	 * 
	 * @var System
	 */
	private $system;
	
	/**
	 * 
	 * @param CActiveForm $form
	 * @param System $system
	 * @param Controller $owner
	 * @return MnemonicsEditor
	 */
	public static function create($system, $owner) {
		$editor=new MnemonicsEditor();
		$editor->system=$system;
// 		$editor->form=$form;
		return $editor;
	}
	
	public function createEditable($value='') {
		$result='';
		$mnemosystem=NULL; //null value means deafualt mnemonics editor
		
		if(!is_null($this->system))
			$mnemosystem=$this->system->mnemosystem;
		
		
		if($mnemosystem!='none' && $mnemosystem!='other') {
			//the buttons that
			$result.=CHtml::button('Auto', array('id'=>'matbut-auto')); 
			//$result.="<p></p>";
			
		} else {
		}
		// in all cases
		$result.=CHtml::textArea('Char[mnemo]', $value, array('rows'=>5, 'cols'=>80));
		
		return $result;
	}
}
?>