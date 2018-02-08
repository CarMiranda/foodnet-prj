#!/bin/bash
SQLUser='kdfood'
SQLPass='kdfood'
SQLDB='KooDeFood'
DLDir='daily_logs'

res=`mysql -u $SQLUser -p$SQLPass $SQLDB < clean_daily.sql`
now=( `date '+%T %Y-%m-%d'` )
if [ $? -eq 0]; then
    echo "[CLEAN] Performed daily cleaning successfully at ${now[0]} on ${now[1]}." >> $DLDir/mysql_daily.log
    exit 0
else
    echo $res >> $DLDir/mysql_derr_${now[1]}.log
    echo "[CLEAN] An error occurred while performing daily cleaning at ${now[0]} on ${now[1]}. Please refer to $DLDir/mysql_err_${now[1]}.log for more details."
    exit 1
fi