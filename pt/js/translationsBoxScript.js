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
             return;
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

        //boxdata is an array of lengeth = n*3 + 1  
        //quadruples of cn-primary, cn-alt, transcription, translations
        //last element is the tags
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

            for(i=0; i<boxdata.length-3; i+=4) {
            	var cn=boxdata[i];
            	var cnAlt=boxdata[i+1];
            	
            	htmldata+='<div class="ch">';
            	htmldata+=cn;
            	if(cnAlt!='' && cn!=cnAlt) {
            		htmldata+=" [";
            		htmldata+=boxdata[i+1];
            		htmldata+="]";
            	}
            	htmldata+='</div>'; 
              
              htmldata+='<div class="pinyin">';
              htmldata+=boxdata[i+2];
              htmldata+='</div>';

              htmldata+='<ul>';
              for(j=0; j<boxdata[i+3].length; j++) {
                 htmldata+='<li>'+boxdata[i+3][j]+'</li>';
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
        