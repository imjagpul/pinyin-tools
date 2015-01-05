<?php
class CompositionsButtonColumnDataDummy {
	public $subcharId;
}

class CompositionsButtonColumn extends CButtonColumn {
	protected function renderDataCellContent($row,$data)
	{
		parent::renderDataCellContent($row,$data);
		echo CHtml::hiddenField("components[]", $data->subcharId);
	}
	
	public function renderDataCellContentTemplate($row, $subcharId) {
		//this functions calls the renderDataCellContent, with placeholders instead of the real ids
		$rowReplacement="{ROW}";
		$subcharReplacement="_SUBCHAR__HOLDER_";
		
		//we create a fake model-like object, in order to be able to use renderDataCellContent directly 
		$data=new CompositionsButtonColumnDataDummy;
		$data->subcharId=$subcharReplacement;

		//render the data
		ob_start();
		$this->renderDataCellContent($rowReplacement,$data);
		$content=ob_get_contents();
		ob_end_clean();
		
		//replace placeholders with the desired content
		//note we cannot use the desired content directly, as it would get HTML and URL escaped, which is not what we want here    
		$content=str_replace($rowReplacement, $row, $content);
		$content=str_replace($subcharReplacement, $subcharId, $content);
		
		return $content;
	}
	
	public function renderDataCellContentDeleteOnlyTemplate($row) {
		$rowReplacement="{ROW}";
		$data=new CompositionsButtonColumnDataDummy;
		
		ob_start();
		$this->renderButton('delete', $this->buttons['delete'], $row,$data);
		$content=ob_get_contents();
		ob_end_clean();
		
		//replace placeholders with the desired content
		//note we cannot use the desired content directly, as it would get HTML and URL escaped, which is not what we want here
		$content=str_replace($rowReplacement, $row, $content);
		
		return $content;
	}
}