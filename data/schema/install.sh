#!/bin/bash

# 
# douggr/zf-rest
# 
# @link https://github.com/douggr/zf-rest for the canonical source repository
# @version 2.0.0
#
# For the full copyright and license information, please view the LICENSE
# file distributed with this source code.
# 

DATABASE="$1"
USERNAME="$2"
PASSWORD="$3"
HOSTNAME="$5"
PORT="$4"

if [ "" = "$DATABASE" ]; then
  echo "ERROR 1046 (3D000) at line 1: No database selected"
  exit 1
fi

if [ "" = "$USERNAME" ]; then
  USERNAME="root"
fi

if [ "" = "$PASSWORD" ]; then
  PASSWORD="root"
fi

if [ "" = "$HOSTNAME" ]; then
  HOSTNAME="127.0.0.1"
fi

if [ "" = "$PORT" ]; then
  PORT="3306"
fi

[ -f tmp.sql ] && rm -f tmp.sql

for SQL in *.sql; do
  echo "Running $SQL… "

  cat $SQL | sed "s/%DATABASE%/$DATABASE/g" > tmp.sql
  mysql -u"$USERNAME" -p"$PASSWORD" -h"$HOSTNAME" -P$PORT < tmp.sql

  if [ 0 != $? ]; then
    exit 1
  fi

  [ -f tmp.sql ] && rm -f tmp.sql
done

exit 0
#EOF
