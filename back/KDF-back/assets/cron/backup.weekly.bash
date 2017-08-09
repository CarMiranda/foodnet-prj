#!/bin/bash
SQLUser='kdfood'
SQLPass='kdfood'
SQLDB="KooDeFood"

res=`mysql -u $SQLUser -p$SQLPass $SQLDB < clean_weekly.sql`
now=( `date '+%T %Y-%W'` )
if [ $? -eq 0]; then
    echo "Performed weekly backup successfully at ${now[0]} on ${now[1]}." >> mysql_weekly.log
    exit 0
else
    echo $res >> mysql_werr_${now[1]}.log
    echo "An error occurred while performing weekly tasks at ${now[0]} on ${now[1]}. Please refer to mysql_werr_${now[1]}.log for more details."
    exit 1
fi