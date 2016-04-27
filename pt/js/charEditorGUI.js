//Used in the char editing form.

//Functions concerning the mnemo editor box and "Mark" buttons.

$(document).ready(function() {
	//create the "Mark" buttons
	
	decorate("[k]", function() {return $("#Char_keyword").val();}, "[/k]", $("#Char_keyword").parent());
	
	decoratePrependFunc(function () {return "[a"+getToneFromTranscription()+"]";},
			archetypeSuggest, "[/a]", $("#mnemo-editor-row"), "Archetype");
	
	decoratePrependFunc(function () {return "[s"+getToneFromTranscription()+"]";}, 
			soundwordSuggest, "[/s]", $("#mnemo-editor-row"), "Soundword");
	
	updateCompositionsButton();
	
	//mnemo-editor-row
});

function updateCompositionsButton() {
	$("table.items tbody:first tr td:nth-child(2)").each(function( index ) {
		var keyword=$( this ).text();
		decoratePrependFunc(function() {return "[c"+getCompositionID(keyword)+"]";}, function(){return keyword;}, "[/c]", $(this), "Mark");
	});
}

function getCompositionID(keyword) {	
	$("body").data("componentsNames");
	
	var components=getComponentsId();

	if(components.length==0) return "";
	else {
		for(var comp in components) {
			var compName=$("body").data("componentsNames")[components[comp]];
			if(compName==keyword) return components[comp];
		}
	}
	
}

function getToneFromTranscription() {
	//transcription
	var trans=$("#Char_transcription").val();
	if(trans.length==0)
		trans=$("#transcriptionOriginal").val(); //the first value from the dictionary

	if(trans!=null && trans.length>0) {
		return trans[trans.length-1];
	}

	return "";
}

function archetypeSuggest() {
	//TODO : need to set an array  / AJAX provided suggestion in the main form, according to mnemo rules
	return "giant";
}

function soundwordSuggest() {
	//TODO : need to set an array / AJAX provided suggestion in the main form, according to mnemo rules
	return "";
}


function decorate(pre, nullValueFunc, post, appendToTag) {
	var markButton=$('<input type="button" value="Mark" />');
	
	markButton.click(function() {
		smartMark(pre, nullValueFunc(), post);
	});
	
	markButton.appendTo(appendToTag);	
}

function decoratePrependFunc(preFunc, nullValueFunc, post, prependToTag, buttonText) {
	var markButton=$('<input type="button" value="'+buttonText+'" />');
	
	markButton.click(function() {
		smartMark(preFunc(), nullValueFunc(), post);
	});
	
	markButton.prependTo(prependToTag);	
}

function smartMark(pre, nullValue, post) {
	var fullText=$("#Char_mnemo").val();
	var selectionStart=$("#Char_mnemo").prop("selectionStart"); 
	var selectionEnd=$("#Char_mnemo").prop("selectionEnd");
	var marked=fullText.slice(selectionStart, selectionEnd);
	
	var newText;
	if(marked.length==0) {
		newText=fullText + pre + nullValue + post; 
	} else {
		newText=fullText.slice(0, selectionStart);
		newText+=pre;
		newText+=fullText.slice(selectionStart, selectionEnd);
		newText+=post;
		newText+=fullText.slice(selectionEnd, fullText.length);
	}
	
	$("#Char_mnemo").val(newText);
}