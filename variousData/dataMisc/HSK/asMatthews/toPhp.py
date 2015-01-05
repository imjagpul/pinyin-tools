#!/usr/bin/python
#takes the characters (but not words) from the first column (from inside "")
#and formats it as a php array
import sys
import re
#filename=sys.argv[1]
#groupname=re.compile("(.+?)\.csv").match(filename).group(1).replace("-","_")

groupname="hsk_matthews";
i=open("toBeImported.txt", "r");
o=open("hsk-matthews.php", "w"); #joined files
o.write("<?php ");

o.write("global $"+groupname+";$"+groupname+"=array(");
pat=re.compile('^([^\t]+?)\t.+\t#(.+)$')
for line in i:
    m=pat.match(line)
    if m is None:
		continue
    if(len(m.group(1))<4):
		o.write("'")
		o.write(m.group(2))
		o.write("'")
		
		o.write("=>")
		
		o.write("'")
		o.write(m.group(1))
		o.write("',")
        #o.write("\n")
o.write(");");
i.close();
o.close();

