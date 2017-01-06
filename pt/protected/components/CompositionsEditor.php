<?php
Yii::import('zii.widgets.grid.CGridView');
class CompositionsEditor extends CGridView {

	/**
	 * 
	 * @var Char $char
	 */
	private $char;
	/**
	 * 
	 * @var Compostion[] $components
	 */
	private $components;
	/**
	 * 
	 * @var CBaseController $owner
	 */
	private $owner;
	
	/**
	 *
	 * @param Char $model
	 * @return string
	 */
	public function formatCompositionTxt($model) {
		return $model->chardef." ".$model->keyword." (".$model->systemValue->name.")";
	}
	
	private static function createDataProvider($charId) {
		return new CActiveDataProvider ( 'Composition', array (
				'pagination' => false,
				'criteria' => array (
						//since charId=NULL condition (at new records) behaves funny,
						//we put a condition that always fail
						//(using ActiveDataProvider (in contrast to ArrayDataProvider(array())) preserves the labels intact)
						'condition' => $charId!==NULL ? "charId=$charId" : "FALSE",
						'with' => array (
								'subchar'
						)
				)
		)
		);
	}
	
	private function echoSuggestionText() {
		$charModel=$this->char;
		$allComponents=Suggestion::suggestComposition($charModel);
		
		if(count($allComponents)<1) {
			return ''; //do not display any text if no suggestions found
		} else {
			$listData=array();		
	
			//wformat the suggestions in the listdata format
			foreach ($allComponents as $JSON=>$subs) {
				$listData[$JSON]=implode(' ', CJSON::decode($JSON));
			}
		
			?>
			<div class="">
			<?php echo CHtml::dropDownList('suggestions', '', $listData, array('prompt'=>'Alternative composition')); ?>
			</div>
<?php 
		} 
	}
	
	private function fillIfEmpty() {
		if(count($this->char->components)>0)
			return;
		
		 //Suggestion::suggestComposition($this->char);
		 //CharController#actionSuggestCompositions
		 //Suggestion::matchKeywordForCompositionFormatted
	}
	
	public static function findBaseScriptUrl($owner) {
		//$widget=Yii::app()->getWidgetFactory()->createWidget($owner,'CompositionsEditor');
// 		$widget=self::create(" ", $owner);
// 		$widget->init();
// 		return $widget->baseScriptUrl;
		return Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('zii.widgets.assets')).'/gridview';
	}
	
	public static function create($char, $owner, $fillIfEmpty=false) {
		$dataProvider=NULL;
		$dataProvider=self::createDataProvider($char->id);
		
		$widget=Yii::app()->getWidgetFactory()->createWidget($owner,'CompositionsEditor', array(
				'id'=>'components-grid',
				'dataProvider'=>$dataProvider,
				'selectableRows'=>0,
				'summaryText'=>'',
				'emptyText'=>"No composition data set.",
				'columns'=>array(
						'subchar.chardef',
						'subchar.keyword',
						'subchar.systemName',
							
						array('class'=>'CompositionsButtonColumn',
// 								'template'=>'{view} {update} {delete} {hiddenid}',								
								'updateButtonUrl'=> 'Yii::app()->createUrl("char/update",array("id"=>$data->subcharId))',
								'viewButtonUrl'=> 'Yii::app()->createUrl("char/view",array("id"=>$data->subcharId))',
// 								'updateButtonUrl'=> 'Yii::app()->createUrl("char/update")',
// 								'viewButtonUrl'=> 'Yii::app()->createUrl("char/view")',
								'deleteButtonUrl'=> '"#"',  //'Yii::app()->createUrl("")',
								'buttons' => array(
										'delete' => array( //the delete button simply removes the row (including the hidden field)
// 												'url'=>'...',       // a PHP expression for generating the URL of the button
												'click'=>'function(){$(this).parent().parent().remove(); return false;}',     // a JS function to be invoked when the button is clicked
										),
										),
								
						))));
		$widget->char=$char;
		$widget->owner=$owner;
		$widget->components=$char->components;
		
		if($fillIfEmpty)
			$widget->fillIfEmpty();
		
		return $widget;
	}

	/**
	 * 
	 * @return CompositionsButtonColumn
	 */
	private function getButtonsColumn() {
		return $this->columns[count($this->columns)-1];//the last column is the CompositionsButtonColumn
	}
	
	public function renderQuickBar() {
		
		echo '<tbody><tr><td colspan="3">';
		echo CHtml::textField('add_component', '', array('class'=>'full'));
		$this->renderJSData();
		echo '</td><td>';
		
		//now what was this for?
// 		$buttonsRendered=$this->getButtonsColumn()->renderDataCellContentTemplate("'+newId+'", "'+obj[0][3]+'");
// 		$deleteOnlyButton=$this->getButtonsColumn()->renderDataCellContentDeleteOnlyTemplate("'+newId+'");

		//@TODO in the select - group by system (instead of paranthesis)
		echo CHtml::ajaxButton("Add", array('char/suggestComposition'),
				array(
						'type'=>'GET',
						'data'=> array(
							'system' => "js:$('#Char_system').val()",
							'newcomp'=> "js:$('#add_component').val()"
						),
						'success'=> 'addSingleComponentUnparsed'
						),
				array('id'=>'commponentSuggest',
						'name'=>'commponentSuggest',
						'type'=>'submit'
						));
		echo '</td></tr></tbody>';
	}
	
	private function renderJSData() {
		$allRows=$this->dataProvider->getData();
		echo '<script type="text/javascript">';
		echo '$("body").data("componentsNames", []);';
		echo "\n";
		for($row=0; $row<count($allRows); $row++) {
			$data=$allRows[$row];
			?>
			$("body").data("componentsNames")[<?php echo $data->subcharId; ?>]='<?php echo Utilities::escapeStringSingleQuoteJS($data->subchar->keyword); ?>';
			<?php
			echo "\n";
		}
		echo '</script>';
		echo "\n";
	}
	
	private function renderTableBodyMultiplicating() {
		//a substitute for parent::renderTableBody()
		//that renders rows with a given count > 1 the corresponding number of times
		$data=$this->dataProvider->getData();
		$n=count($data);
		echo "<tbody>\n";
// 		$dataJS=array();
		
		//the only problem is that even / odd marking get also grouped, but let's say it's a feature
		if($n>0)
		{
// 			$realRow=0;
			for($row=0;$row<$n;$row++) {
				$data=$this->dataProvider->data[$row];
// 				if($data)
				for($i=0;$i<$data->count;$i++){
					$this->renderTableRow($row);
// 					$realRow++;
					
					//collect the 
					//$dataJS[$data->subcharId]=$data->subchar->keyword;
					 
				}
			}
		}
		else
		{
			echo '<tr><td colspan="'.count($this->columns).'" class="empty">';
			$this->renderEmptyText();
			echo "</td></tr>\n";
		}
		echo "</tbody>\n";
	}
	
	public function renderTableBody() {
		$this->renderTableBodyMultiplicating();
		$this->renderQuickBar();
	}
	
	public function outputEditable() {
		$this->init();
		$this->run();
	}
	
	public function renderContent() {
		//echo the suggestions listbox before the table
		//(we place it here to keep it inside the <div> of the grid
		$this->echoSuggestionText(); 
		parent::renderContent();
	}
}