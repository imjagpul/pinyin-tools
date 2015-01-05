#converts the anki chinese deck to tab separated format

#remove HTML tags
s/<[^>]*>/ /g

#remove *Chinesey.com
s/^.*Chinesey.com//

#remove Unit.*Panel.[0-9]
#s/Unit.*Panel.[0-9]*//
s/, Page [0-9]*, Panel [0-9]*//

#remove extra spaces
s/  */ /g

#remove space on the beginning and the end of the line
s/^ //
s/ $//

#remove spaces around the tab char
s/ *\t */\t/

#convert to XML
s:^\(.*\)Unit \([0-9]*\)\(.*\)$:<item><cat>Unit \2</cat>\1 \3:
s:</cat>\(.*\)\t\(.*\):</cat><Q>\1</Q><A>\2</A></item>:
