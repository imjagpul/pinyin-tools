#converts the anki chinese deck to tab separated format

#remove HTML tags
s/<[^>]*>/ /g

#remove stroke count
s/\t.*strokes/\t/

#delete tags
s:/.*$::

#delete entry numbers
s/#[0-9]*[a-z]*//

#remove Unit.*Panel.[0-9]
#s/Unit.*Panel.[0-9]*//
#s/, Page [0-9]*, Panel [0-9]*//

#remove extra spaces
s/  */ /g

#remove space on the beginning and the end of the line
s/^ //
s/ $//

#remove spaces around the tab char
s/ *\t */\t/

#convert to XML
s:^(\([^)]*\))\(.*\)\t\(.*\)$:<item><cat>\1</cat><Q>\2</Q><A>\3</A></item>:
#s:^(\(.*\))\(.*\)\t\(.*\)$:<item><cat>\1</cat><Q>\2</Q><A>\3</A></item>:
#s:^(\(.*\))\(.*\)\t\(.*\)$:<item><cat>\1</cat><Q>\2</Q><A>\3</A></item>:
#s:^(\(.*\))\(.*\)\t\(.*\)$ :<item><cat>\1</cat><Q>\2</Q><A>\3</A></item>:
