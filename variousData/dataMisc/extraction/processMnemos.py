#!/usr/bin/python
# -*- coding: utf-8 -*-
import re
import pprint

#this script outputs the mnemos in square-brackets-format
#and also lists non-compilant mnemos

#regex = re.compile("f\(\s*([^,]+)\s*,\s*([^,]+)\s*\)<span style="font-weight:600; color:#ff0000;">[^<][^<]*</span>")
regex = re.compile("<span style=\"font-weight:600;?\s*\">([^<]+)</span>")
regexpron = re.compile("<span style=\"font-weight:600;?\s*color[^\"]+\">([^<]+)</span>")
archetypes =  re.compile("giant|fairy|Teddy|dwarf|fairie|Teddie|dwarve")
tones =  (re.compile("giant"),
re.compile("fair"),
re.compile("Tedd"),
re.compile("dwarf")
);
			

#姿	beauty	He looked several <span style="font-weight:600;">times </span>on the <span style="font-weight:600;">woman</span>. Is it her <span style="font-weight:600;">beauty </span>that attracts him? He also looks to the <span style="font-weight:600; color:#ff0000;">giant </span>sitting next to him - but he only hears &quot;<span style="font-weight:600; color:#ff0000;">zzz...</span>&quot; (the giant is obviously not caught by her beauty).	times 次 + woman 女
#char keyword mnemo comp

outfile=open("processedMnemosOkOutput", "w")

def tryMatch(comp, marked):
	#print(marked+" ### "+comp)
	return marked in comp

with open("mnemosHibernatedConverted.txt", "r") as f:	
	for line in f:
		parts=line.strip().split("\t")
		(char, keyword, mnemo, compTotal)=parts
		comp=compTotal.split("+")
		
		#locate marked keyword and compositions using regex
		marked=regex.findall(mnemo)
		if(len(marked)<3):
			print("Not enough keywords found:"+char)
			continue
		#pprint.pprint(marked)
			
		#decide which is the main keyword
		markedkw=None
		for m in marked:
			if keyword in m:
				markedkw=m
		if markedkw is None:
			print("Unable to identify main keyword:"+char)
			continue			
			
		#decide which composition corresponds to which char
		if len(comp)!=(len(marked)-1):
			print("Inconsistent component count:"+char)
			continue
		
		data=dict()
		for m in marked:
			if markedkw == m:
				continue

			#try to match the composition
			for c in comp:
				#if not matched already
				if c in data:
					continue

				if tryMatch(c, m):
					data[c]=m
					break
					
		if(len(data)< (len(comp)-1)):
			print("Unable to match marked data to components:"+char)
			continue
		elif len(data) == (len(comp)-1):
			#automatch the last one
			#find the one that is not yet matched
			for c in comp:
				if not c in data:
					for m in marked:
						if m not in data.itervalues():
							data[c]=m
							print("automatching <<"+c+">> to <<"+m+">>  at "+char+" ")
							break
			
			
		#parse archetype and pronuncation
		markedpron=regexpron.findall(mnemo)
		if(len(markedpron)<2):
			print("Unable to mark pronunciation:"+char)
			continue
			
		toneArch=None
		pronArch=None
		toneNum=0
		if archetypes.search(markedpron[0]):
			toneArch=markedpron[0]
			pronArch=markedpron[1]
		elif archetypes.search(markedpron[1]):
			pronArch=markedpron[1]
			toneArch=markedpron[0]
		else:
			print("No archetype found:"+char)
			continue
			
		#check tone number
		for t in tones:
			if t.search(toneArch):
				toneNum=tones.index(t)

		#output results
		mnemoOutput=mnemo
		for m in regex.finditer(mnemo):
			if m.group(1) == markedkw:
				mnemoOutput=mnemoOutput.replace(m.group(0), "[k]"+m.group(1)+"[/k]", 1)
			else: # data[c]=m
				c=next((name for name, age in data.items() if age == m.group(1)), "???")
				mnemoOutput=mnemoOutput.replace(m.group(0), "[c="+c+"]"+m.group(1)+"[/c]", 1)

		for m in regexpron.finditer(mnemo):
			if m.group(0) == toneArch:
				mnemoOutput=mnemoOutput.replace(m.group(0), "[t="+str(toneNum)+"]"+m.group(1)+"[/t]", 1)		
			else:
				mnemoOutput=mnemoOutput.replace(m.group(0), "[p]"+m.group(1)+"[/p]", 1)					

		outfile.write(mnemoOutput);
		outfile.write("\n");
outfile.close();
#print("hello")

#    for line in f:
#       result = regex.search(line)
