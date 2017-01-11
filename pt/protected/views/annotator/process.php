<?php
/* @var $this AnnotatorController */
/* @var $id Integer */

// $baseUrl=Yii::app()->baseUrl;
// $cs=Yii::app()->clientScript;
// $cs->registerCoreScript('jquery');

// $cs->registerScriptFile($baseUrl.'/js/main.js');
// $cs->registerScriptFile($baseUrl.'/js/tooltip.js');
// $cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');

//$cs->registerCssFile($baseUrl.'/css/main-gi.css');

// $cs->registerScriptFile($baseUrl.'/js/main.js');
// $cs->registerScriptFile($baseUrl.'/js/tooltip.js');
// $cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
// $cs->registerCssFile($baseUrl.'/css/main-gi.css');

//define the worker function


Yii::app()->clientScript->registerScript('worker',
'function sendRequest() {'.
		
		CHtml::ajax(array('type' => 'POST',
            'url'=>$this->createUrl('processBackground', array('id'=>$id)),
'success'=> 'function(data) {
				    if(data.status=="progress") {
						/* $( "#log" ).append( "<p>Chunk processed...</p>" ); */
						$("#workProgressBar").progressbar( "option", "max", data.count);
						$("#workProgressBar").progressbar( "value", data.current);
						sendRequest();
				    } else if(data.status=="continueWordsDict") {
						$( "#log" ).append( "<p>Generated words dictionary.</p>" );
						sendRequest();
				    } else if(data.status=="continuePhrasesDict") {
						$( "#log" ).append( "<p>Generated phrases dictionary.</p>" );
						sendRequest();
				    } else if(data.status=="ok") {
						$( "#log" ).append( "<p>Done!</p>");
						document.location = "'.$this->createUrl('process', array('id'=>$id)).'";
				    } else if(data.status=="error") {
						$( "#workProgressBar" ).progressbar( "option", "disabled", true );
						$( "#log" ).append( "<p>Error processing file!</p>");
					} else {
						$( "#workProgressBar" ).progressbar( "option", "disabled", true );
						$( "#log" ).append( "<p>Unknown error!</p>");
					}
				}
				',
				'error'=> 'function(data) {
						$( "#log" ).append( "<p>Failed!</p>");
						$( "#workProgressBar" ).progressbar( "option", "disabled", true );
				}
				'
				), CClientScript::POS_END)
		
		.'}'
		);

//scheldue to start the worker
Yii::app()->clientScript->registerScript('startWorker', 'sendRequest()', CClientScript::POS_READY);

?>
<h1>Processing</h1>
<div>
<?php 
$this->widget('zii.widgets.jui.CJuiProgressBar',array(
		'value'=>0,
		'id'=>'workProgressBar'
));
?>
</div>
<div id="log"></div>
