#!/usr/bin/python
#takes the characters (but not words) from the first column (from inside "")
#and formats it as a php array
import sys
import re
filename=sys.argv[1]
groupname=re.compile("(.+?)\.csv").match(filename).group(1).replace("-","_")

i=open("../orig/"+filename, "r");
#o=open("../charsonly/"+groupname+".php", "w"); #split files
o=open("../charsonly/hskdata.php", "a"); #joined files
o.write("global $"+groupname+";$"+groupname+"=array(");
o.write("global $"+groupname+";$"+groupname+"=array(");
pat=re.compile('^"([^"]+?)"')
for line in i:
    m=pat.match(line)
    if m is None:
        continue
    if(len(m.group(1))<4):
        o.write("'")
        o.write(m.group(1))
        o.write("',")
        #o.write("\n")
o.write(");");
i.close();
o.close();

