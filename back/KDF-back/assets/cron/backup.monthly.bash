#!/bin/bash
SQLUser='kdfood'
SQLPass='kdfood'
SQLDB="KooDeFood"

res=`mysql -u $SQLUser -p$SQLPass $SQLDB < clean_monthly.sql`
now=( `date '+%T %Y-%M'` )
if [ $? -eq 0]; then
    echo "[MONTHLY] Performed monthly backup successfully at ${now[0]} on ${now[1]}." >> mysql_monthly.log
    exit 0
else
    echo $res >> mysql_merr_${now[1]}.log
    echo "[MONTHLY] An error occurred while performing monthly backup at ${now[0]} on ${now[1]}. Please refer to mysql_merr_${now[1]}.log for more details."
    exit 1
fi