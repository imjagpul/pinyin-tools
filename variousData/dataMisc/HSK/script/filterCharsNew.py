#!/usr/bin/python
# -*- coding: utf-8 -*-
#formats as a php array
#
#works, but not useful since in the source data are some characters not present as separate entries (e.g. 什)
import sys
import re
filename=sys.argv[1]
groupname="hsk"+re.compile(".+(.)\.txt").match(filename).group(1)

i=open("../data/"+filename, "r");
#o=open("../charsonly/"+groupname+".php", "w"); #split files
o=open("../charsonly2/hskdata.php", "a"); #joined files
ot=open("../charsonly2/hskdata-trad.php", "a"); #joined files

o.write("global $"+groupname+"-simp;$"+groupname+"-simp=array(");
ot.write("global $"+groupname+"-trad;$"+groupname+"-trad=array(");

pat=re.compile('^(\S+)\t(\S+)\t(\S+)\t(\S+)')
for line in i:
    m=pat.match(line)
    if m is None:
        continue
    if(len(m.group(1))<4):
        o.write("'")
        o.write(m.group(1))
        o.write("',")
        o.write("\n")
        ot.write("'")
        ot.write(m.group(2))
        ot.write("',")
        ot.write("\n")


o.write(");");
ot.write(");");

i.close();

o.close();
ot.close();

