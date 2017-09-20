</div>
<!-- a temporary workaround to allow scrolling low-->
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<script type="text/javascript" language="JavaScript">
var charCache=new Array();
var compCache=new Array();

var spec=<?php echo json_encode(array('s'=>$systemID, 'd'=>$dictionariesID)); ?>;
var searching="searching";
var ignoredChars="<?php echo AnnotatorEngine::ignoredCharsJs; ?>";
var componentLength=<?php echo Yii::app()->params['dynamicAnnotatorCompositionLengthLimit']; ?>;

function isAlphaNumeric(str) {
	  var code, i, len;

	  for (i = 0, len = str.length; i < len; i++) {
	    code = str.charCodeAt(i);
	    if (!(code > 47 && code < 58) && // numeric (0-9)
	        !(code > 64 && code < 91) && // upper alpha (A-Z)
	        !(code > 96 && code < 123)) { // lower alpha (a-z)
	      return false;
	    }
	  }
	  return true;
	};

function isNotIgnored(val) {
	return ignoredChars.indexOf(val)==-1 && !isAlphaNumeric(val);
}

function getComponents(t,current) {

	var components=new Array();
	var compText='';//note the component does not include the first character
	var currentLength=1;
	while(current.prop('class')!="b" && currentLength<componentLength) {
		current=current.next();
		compText=compText+current.text();
		components[currentLength-1]=compText;
		currentLength=currentLength+1;
	}	

	return components;
}

function getComponentsData(t, components) {
	var toBeSearched=[];
	//var componentsData=[];
	var componentsData=[];
	
	for(var i=0;i<components.length;i++) {
		var c=components[i];
		var cFull=t+c;
		
		if(!(cFull in compCache)) {
			compCache[cFull]=searching;
			
			toBeSearched.push(c);
		} else if(compCache[cFull]==searching) {
			//nothing found (does not exist)
			//OR
			//searching but no response yet - just ignore it	
		} else {
			//componentsData[cFull]=compCache[cFull];
			var entry=compCache[cFull];
			for(var j=0;j<entry.length;j++) {
				componentsData.push(cFull); //text
				componentsData.push(cFull); //HACK text
				componentsData.push(entry[j][0]); //transcription
				componentsData.push(entry[j][1]); //translations
			}
			
		}
	}

	return {
		"componentsData":componentsData,
		"toBeSearched":toBeSearched
	};
}

function boxReply(data,t,components,componentsData) {
	
	if(data[0]!=null) {
		charCache[t]=data[0];
	}
	phrasesData=data[1];

	var needsReload=false;
	for(var p in phrasesData) {
		needsReload=true;
		compCache[p]=phrasesData[p];
	}
	
	if(needsReload) 	
		componentsData=getComponentsData(t,components).componentsData;
	
	setBoxdataWithComponents(componentsData, charCache[t], t);
}

function showBoxFunction() { //called on hover of a char
	//highlight a word when hovered
	//$(this).css("background-color","yellow");
	
	var t= $(this).text();
	var components=getComponents(t, $(this));
	var r=getComponentsData(t,components);
	
	var componentsData=r.componentsData;
	spec['c']=r.toBeSearched;
	spec['t']=t;
	spec['o']=1;
	
	if(!(t in charCache)) {
		charCache[t]=searching;
		spec['o']=0;
	}

	if(!spec['o'] || spec['c'].length>0) {
		$.getJSON('<?php echo Yii::app()->createUrl('annotator/box'); ?>', spec,
				function(data) {boxReply(data,t,components,componentsData);} );

		//indicate that box is desired but wait for data to load
		showBox(t);
	} else if(charCache[t]==searching) {
		showBox(t);
	} else {
		//box(charCache[t], t);
		//setBoxdataWithComponents(componentsData, charCache[t], t);
		boxWithComponents(componentsData, charCache[t], t);
		//boxWithComponents(charCache[t], componentsData, t);
	}
};

function isBlank(str) {
    return (!str || /^\s*$/.test(str));
}

function preprocessDiv(i,val) {
		var valJ=$(val);

		var text=valJ.text();
		
		if(isBlank(text))
			return; //empty chunk - nothing do
			
		//split in chunks of 100 
		var chunks=text.match(/.{1,100}/g);
		if(chunks==null)
			return;
		
		valJ.html(" ");
		
		$.each(chunks, function(i,val){
			//wrap each word in an auxiliary span tag  
			$('<span/>').text(val+" ").appendTo(valJ);
		});

		valJ.children("span").one("mouseover",function(){
			var characters=$(this).text().split("");

			var me=$(this);
			$(this).html("");
			
			$.each(characters, function(i,val){
				var nextIgnored=true;
				if(characters.length>(i+1) && isNotIgnored(characters[i+1]))
					nextIgnored=false;
				
				//wrap each word in a span tag  
				//(unless it is an ignored char like whitespace or punctation)
				
				if(isNotIgnored(val)) {
					var added=$('<span/>').text(val);

					if(nextIgnored) //the characters that precede word boundaries are marked (needed for component suggestion)
						added.toggleClass("b");
					
					added.appendTo(me);
					added.on("mouseover",showBoxFunction);
					added.on("mouseout",hb);
				} else {
					me.append(val);
				}
			});
		});
}

function preprocess() {
	$("div.x").each(preprocessDiv);
}

preprocess();
</script>
</body>
</html>
