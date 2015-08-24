<?php
/* @var $this CharController */
/* @var $model Char */
/* @var $form CActiveForm */


$baseUrl=Yii::app()->baseUrl;
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
$cs->registerScriptFile($baseUrl.'/js/main.js');
$cs->registerScriptFile($baseUrl.'/js/tooltip.js');
$cs->registerScriptFile($baseUrl.'/js/charEditorGUI.js');
$cs->registerCssFile($baseUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
$cs->registerCssFile($baseUrl.'/css/main-gi.css');

$suggestURL=$this->createUrl("char/suggest");
$suggestURL2=$this->createUrl("char/suggestComposition");
$suggestURLcompMultiple=$this->createUrl("char/suggestCompositions");
$systemChangedURL=$this->createUrl("char/suggestSystemChanged");

//@TODO replace the script to CHtml::ajax() 
////@TODO maybe include using the script this / 
// $suggest=<<<EOL
// EOL;
// $cs->registerScript('formsuggest', $suggest);
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'char-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); 

/*
	<p class="note">Fields with <span class="required">*</span> are required. 
	Keyword suggestion is enabled.
	Click on the <span class="sticky">highlighted fields</span> to edit them.</p>

 */
?>
	<p class="note">
		Click on the <span class="sticky">highlighted fields</span> to edit
		them.
	</p>
	<?php //@TODO settings - enable / disable keywords suggest 
	?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row sticky">
		<?php echo $form->labelEx($model,'system'); ?>
		<?php echo $form->dropDownList($model,'system', CHtml::listData($systemList, 'id', 'name')); ?>
		<?php echo $form->error($model,'system'); ?>
	</div>

	<div class="row sticky">
		<?php echo $form->labelEx($model,'chardef'); ?>
		<?php echo $form->textField($model,'chardef',array('size'=>5,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'chardef'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'keyword'); ?>
		<?php echo $form->textField($model,'keyword',array('size'=>10,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'keyword'); ?>		
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'transcription', array('id'=>'transcriptionLabel')); ?>
		<?php echo $form->textField($model,'transcription',array('size'=>10,'maxlength'=>25, 'class'=>'with-tooltip')); ?>
		<?php echo $form->error($model,'transcription'); ?>
		<div class="tooltip">If nothing is entered the transcription will be
			taken from the dictionary.</div>
	</div>

	<div class="row">
	<?php echo $form->label($model,'components', array('id'=>'transcriptionLabel')); ?>
	</div>
 
    <?php 
    $compositionsEditor=CompositionsEditor::create($model, $this);
    $compositionsEditor->outputEditable(); 
    ?>
		
	<div class="row">
		<?php echo $form->labelEx($model,'mnemo'); ?>
	</div>

	<div class="row" id="mnemo-editor-row">
        <?php echo MnemonicsEditor::create($model->systemValue, $this)->createEditable($model->mnemo); 
	?>
		<?php echo $form->error($model,'mnemo'); ?>
		<?php 
		$text=CHtml::encode(MnemoParser::suggestOldToNew($model, true));
		
		//$text=str_replace("t's", "O", $text);
		echo $text;
		
		//somewhat sly, since an apostrophe gets converted to entities, but the echo converts it back to apostrophe
		$text=str_replace("&#039;", "\\&#039;", $text);
		?>
		<input type="button" value="copy" onclick="$('#Char_mnemo').val('<?php echo $text;?>')">
		<input type="button" value="remove tags" onclick="$('#Char_mnemo').val($('#Char_mnemo').val().replace(/<(?:.|\n)*?>/gm, ''))">
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes'); ?>
	</div>
	<div class="row">
		<?php echo $form->textArea($model,'notes',array('rows'=>3, 'cols'=>80)); ?>
		<?php echo $form->error($model,'notes'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes2'); ?>
	</div>
	<div class="row">
		<?php echo $form->textArea($model,'notes2',array('rows'=>1, 'cols'=>80)); ?>
		<?php echo $form->error($model,'notes2'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'notes3'); ?>
	</div>
	<div class="row">
		<?php echo $form->textArea($model,'notes3',array('rows'=>1, 'cols'=>80)); ?>
		<?php echo $form->error($model,'notes3'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div>
<!-- form -->

<script type="text/javascript">
/*<![CDATA[*/

//$("body").data("componentsNames", []);
<?php
//Yii::app()->assetManager;

$URLHolder=9999;
?>
var picURL={
		view: "<?php echo $compositionsEditor->baseScriptUrl ?>/view.png",
		update: "<?php echo $compositionsEditor->baseScriptUrl ?>/update.png",
		del: "<?php echo $compositionsEditor->baseScriptUrl ?>/delete.png",
}

var newCompositionsURLHolder="<?php echo $URLHolder;?>";

var newCompositionsURL={
		view: "<?php echo Yii::app()->createUrl("char/view",array("id"=>$URLHolder)); ?>",
		update: "<?php echo Yii::app()->createUrl("char/update",array("id"=>$URLHolder)); ?>",
}


function addSingleComponentUnparsed(html) {
	$('#components-grid table tbody tr td.empty').parent().remove();

	//empty the input field as well
	$('#add_component').val("");
	
	addSingleComponent(JSON.parse(html));
}
function addComponent(html) { 
	objs=JSON.parse(html);
	
	$('#components-grid table tbody tr td.empty').parent().remove();
	
	for(var i=0; i<objs.length; i++) { 
		addSingleComponent(objs[i]);
	}
}

function addSingleComponent(obj) {
	if(obj.length==0) {
		// @TODO change this silent ignore to a special row (that adds it as an entry to the system ; or a non interactive closeable warning
		//	alert("Nothing found for the given input. Input the desired keyword or character. If the compomnent is not in the system yet, add it first.");
		return;
	}
					
	//If it is odd then the next one should be even..
	var table = $('#components-grid table').first('tbody'); 
	var number = ((table.find('tr').size())%2 === 0)?'odd':'even';
	var html = '<tr class="'+number+'">'
	if(obj.length==1) {
		$("body").data("componentsNames")[obj[0][3]]=obj[0][1];
		html+='<td>'+obj[0][0]+'</td><td>'+obj[0][1]+'</td><td>'+obj[0][2]+'</td>'

		var viewUrl=newCompositionsURL['view'].replace(newCompositionsURLHolder,obj[0][3]);
		var updateUrl=newCompositionsURL['update'].replace(newCompositionsURLHolder,obj[0][3]);
		html+='<td><a class="view" title="View" href="'+viewUrl+'"><img src="'+picURL['view']+'" alt="View" /></a> <a class="update" title="Update" href="'+updateUrl+'"><img src="'+picURL['update']+'" alt="Update" /></a> <a class="delete" title="Delete" href="#"><img src="'+picURL['del']+'" alt="Delete" /></a><input type="hidden" value="'+obj[0][3]+'" name="components[]" id="components" /></td>';
	} else {
		html+='<td colspan="3"><select name="components[]" id="Char_system" class="full">';
		//more than one - add as choice box
		for(i=0; i<obj.length; i++) {
			$("body").data("componentsNames")[obj[i][3]]=obj[i][1];
			html+='<option value="'+obj[i][3]+'"> '+obj[i][0]+' - '+obj[i][1]+' ('+obj[i][2]+')</option>';
		}
		html+='</select></td>';
		html+='<td><a class="delete" title="Delete" href="#"><img src="'+picURL['del']+'" alt="Delete" /></a></td>';
	}
	
	html+='</tr>';
	table.append(html);	
}


jQuery(function($) {
	$('body').on('change', '#Char_chardef', function () {
		//var system=$('#Char_system').val();
		var chardef=$(this).val();

		if(chardef.length<1)
			return;

		$.ajax({'type':'GET',
		'url':'<?php echo $suggestURL; ?>',
		'data':{'system': $('#Char_system').val(),
		'chardef': chardef
		},
		'cache':false,
		'success':function(html) {
			obj=JSON.parse(html);
			if(!$("#Char_keyword").val())
				$("#Char_keyword").val(obj.keyword);
			if(!$("#Char_mnemo").val())
				$("#Char_mnemo").val(obj.mnemo);
			$("#components-grid").replaceWith(obj.compositions); <?php /* @TODO : protect if already changed */ ?>
			$("#dict-portlet").replaceWith(obj.dict);
		}
		});
	});
	
});

function getComponentsId() {
	var a=[]; 
	$("[name='components[]']").each(function(index) {
		a[index]=$(this).val();
	});
	return a;
}

jQuery(function($) {
	$('body').on('change', '#Char_system', function () {
		var components=(getComponentsId().join(';'));
		
		$.ajax({'type':'GET',
			'url':'<?php echo $systemChangedURL; ?>',
			'data':{'system': $('#Char_system').val(),
			'components':components
			
			},
			'cache':false,
			'success':function(html) {
				obj=JSON.parse(html);
				oldval=$("#Char_mnemo").val();
				$("#mnemo-editor-row").html(obj.mnemoeditor);
				$("#Char_mnemo").val(oldval);
				//@TODO implement the following id:COMPONENTS-EDITOR				
// 				$("#components-grid").replaceWith(obj.compositions);
			}
			});
	});
});
	
	
jQuery(function($) {
	$('body').on('change', '#suggestions', function () {

		var chardef=$(this).val();

		if(chardef.length<1)
		 	return;

		$.ajax({'type':'GET',
			'data':{
				'system':$('#Char_system').val(),
				'comps':chardef
				},
				'url':'<?php echo $suggestURLcompMultiple; ?>',
				//'success': $( "body" ).data( "addComponent")
				'success': addComponent,
				});
	});
	
});


jQuery(function($) {
	$('body').on('click', '#matbut-auto', function () {
		var toAdd=[];
		var addingIndex=0;
		
		//components
		var components=getComponentsId();

		if(components.length==0) toAdd[addingIndex++]="wheel";
		else {
			for(var comp in components) {
				var compName=$("body").data("componentsNames")[components[comp]];
				if(compName==undefined) compName="";
				toAdd[addingIndex++]= "[c"+components[comp]+"]"+compName+"[/c]";
			}
		}

		//keyword
		toAdd[addingIndex++]= "[k]"+$('#Char_keyword').val()+"[/k]";
		
		//transcription
		var num=getToneFromTranscription();
		//@TODO check if it is a number
		if(num!="") {
			
			//archetypes
			toAdd[addingIndex++]= "[a"+num+"][/a]";
			
			//soundwords
			toAdd[addingIndex++]= "[s"+num+"][/s]";
		}
		//apply
		$("#Char_mnemo").val($("#Char_mnemo").val()+toAdd.join(" "));
	});
});
	
/*]]>*/
</script>