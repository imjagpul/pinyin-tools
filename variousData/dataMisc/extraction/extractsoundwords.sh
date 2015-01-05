#!/bin/sh
#needle example:
# <span style="font-weight:600; color:#ff0000;">zareeba</span>

# <span style="font-weight:600; color:#ff0000;">[^<]+</span>
REGEX='<span style="font-weight:600; color:#ff0000;">zareeba</span>'
#echo \'$REGEX

grep '<span style="font-weight:600; color:#ff0000;">[^<][^<]*</span>' <mnemosHibernated.txt
#grep $REGEX <mnemosHibernated.txt
#grep '$REGEX' <mnemosHibernated.txt
