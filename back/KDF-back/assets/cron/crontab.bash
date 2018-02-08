SHELL=/bin/bash
PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/run-mysql
#MAILTO="dev@koodepouce.fr"
HOME=/

# Database cleaning and backing up
#   m   H   d   M   D
    00  03  *   *   *   mysql   clean.daily     >   /dev/null 2>&1
    05  03  *   *   *   mysql   backup.daily    >   /dev/null 2>&1
    10  03  *   *   07  mysql   backup.weekly   >   /dev/null 2>&1
    15  03  01  *   *   mysql   backup.monthly  >   /dev/null 2>&1
    20  03  01  01  *   mysql   backup.yearly   >   /dev/null 2>&1