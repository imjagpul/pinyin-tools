//Used in the char editing form.
$(document).ready(function() {

	decorate("[k]", function() {return $("#Char_keyword").val();}, "[/k]", $("#Char_keyword").parent());
});

function decorate(pre, nullValueFunc, post, appendToTag) {
	var markButton=$('<input type="button" value="Mark" />');
	
	markButton.click(function() {
		smartMark(pre, nullValueFunc(), post);
	});
	
	markButton.appendTo(appendToTag);
	
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