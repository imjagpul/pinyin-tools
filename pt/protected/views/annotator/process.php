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
				    if(data.status=="continue") {
						$( "#log" ).append( "<p>Chunk processed...</p>" );
						sendRequest();
				    } else if(data.status=="ok") {
						$( "#log" ).append( "<p>Done!</p>");
				    } else if(data.status=="error") {
						$( "#log" ).append( "<p>Error processing file!</p>");
					} else {
						$( "#log" ).append( "<p>Unknown error!</p>");
					}
				}
				'
				), CClientScript::POS_END)
		
		.'}'
		);

//scheldue to start the worker
Yii::app()->clientScript->registerScript('startWorker', 'sendRequest()', CClientScript::POS_READY);

?>
<h1>Processing</h1>
<div id="log"></div>
