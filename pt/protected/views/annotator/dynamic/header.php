<?php
$cs=Yii::app()->clientScript;
$cs->registerCoreScript('jquery');
//generated css for tones (depending on user settings)

//registerCss sucks
//$cs->registerCss('tones', UserSettings::getCurrentSettings()->tonesCss);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type"
	content="text/html; charset=<?php echo $charset; ?>">
<title></title>
<style type="text/css"> /*
			<![CDATA[ */
a {
	text-decoration: none;
	color: #<?php echo$colors['FG_UNTAGGED'] ?>;
}

a.t {
	text-decoration: none;
	color: #<?php echo$colors['FG'] ?>;
}

#box {
	border: 1px solid #1A40B0;
	background-color: #<?php echo$colors['BG_BOX'] ?>;
	padding: 5px;
	z-index: 100;
	visibility: hidden;
	width: 500px;
	position: absolute;
}

.tags {
	background-color: #<?php echo$colors['BG_TAGBOX'] ?>;
	font-size: large;
}

.ch {
	background-color: #<?php echo$colors['BG_BOX_CH'] ?>;
	font-size: x-large;
}

.pinyin {
	background-color: #<?php echo$colors['BG_TRANSCRIPTION'] ?>;
	font-size: large;
}

body {
	font-size: x-large;
	background-color: #<?php echo$colors['BG'] ?>;
}
<?php /* @TODO inculde the following only if needed (if parallel table is shown) */?>
table.parallel tr:first-child td:first-child {
	width: 30%;
}

table.parallel td:last-child {
	background-color: #<?php echo$colors['BG_PARALLEL'] ?>;
}

.grip {
	width: 20px;
	height: 30px;
	margin-top: -3px;
	background-image: url('../images/grip.png');
	margin-left: -5px;
	position: relative;
	z-index: 88;
	cursor: e-resize;
}

.grip:hover {
	background-position-x: -20px;
}

.dragging .grip {
	background-position-x: -40px;
}

<?php echo UserSettings::getCurrentSettings()->tonesCss; ?>
/* ]]> */
</style>
<script type="text/javascript" language="JavaScript">

        var boxshown=false; //if the translation box object is shown now
        var boxobject; //the translation box object
        var lastX;
        var lastY;
        var hidesoon=false;
        var expected=false;
        
        function init() {
           if(document.getElementById) boxobject=document.getElementById('box');
           else if(document.all) boxobject=document.all['box'];
           
           document.onmousemove=mm;
        }

        function rightBorder() { return document.body.clientWidth }
        function bottomBorder() { return document.body.clientHeight }
        function min(a,b) { return a>b ? b : a }
        
        //on mouse move - update the position of the tooltip
        function mm(e) {
           var x=e.pageX;
           var y=e.pageY;

           if(hidesoon) {
            if(lastX!=x || lastY!=y) {
             hidesoon=false;
             boxshown=false;
             boxobject.style.visibility="hidden";            
             return
            }
           }
           
           if(!boxshown) return;

           lastX=e.pageX;
           lastY=e.pageY;

           x+=3;           
           y+=3;
             
           x=min(rightBorder()-boxobject.offsetWidth, x)
           //y=min(bottomBorder()-boxobject.offsetHeight, y)
           /*
           if(y>bottomBorder()-boxobject.offsetHeight) {
             y=y-boxobject.offsetHeight-3
             if(y<0) 
          }  */
            //y=min(bottomBorder()-boxobject.offsetHeight, y)
           boxobject.style.left=x+"px";
           boxobject.style.top=y+"px";
           
           boxobject.style.visibility="visible";
        }

        function showBox(t) {
            boxshown=true;
            hidesoon=false;
            expected=t;
        }

        function setBoxdata(boxdata, t) {
            if(expected!=t)
                return false;
            if(boxdata.length==0) {
                return false; 
            }

            htmldata='';

          //tags
            var tagsText=boxdata[boxdata.length-1];
            if(tagsText!="") {
	            htmldata+='<div class="tags">';
	            htmldata+=tagsText; 
	            htmldata+='</div>';
            }

            //dict
            for(i=0; i<boxdata.length-2; i+=3) {
              htmldata+='<div class="ch">';
              htmldata+=boxdata[i];
              htmldata+='</div>'; 
              
              htmldata+='<div class="pinyin">';
              htmldata+=boxdata[i+1];
              htmldata+='</div>';

              htmldata+='<ul>';
              for(j=0; j<boxdata[i+2].length; j++) {
                 htmldata+='<li>'+boxdata[i+2][j]+'</li>';
              }
              
              htmldata+='</ul>';
            }          

            if(htmldata=="") {
                //hidesoon=true;
                boxshown=false;
                boxobject.style.visibility="hidden";
            }
            boxobject.innerHTML=htmldata;
        
        }
        

        function setBoxdataWithComponents(componentsData, boxdata, t) {
            var result;
            if(componentsData.length>0)
            	result=componentsData.concat(boxdata);
            else
                result=boxdata;
            
            setBoxdata(result, t);
        	
        }
        
        function box(boxdata,t) { //show box
          showBox(t);
          setBoxdata(boxdata,t);
        }
        
        //setBoxdataWithComponents(componentsData, charCache[t], t);
        function boxWithComponents(componentsData, boxdata, t) {
        	showBox(t);
        	setBoxdataWithComponents(componentsData, charCache[t], t)
        }
        
        function hb() { //hide box
            hidesoon=true;        
        }
        
        </script>


</head>
<body>
<?php if($prependText!==NULL) echo $prependText; ?>
	<div id="box"></div>
	<script type="text/javascript" language="JavaScript">
	init();
	</script>
<div class="x">
