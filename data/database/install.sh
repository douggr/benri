#!/bin/bash

HOSTNAME="localhost"
USERNAME=$(whoami)
PASSWORD=""
DATABASE=""
PORT="3306"

usage() {
  echo "Usage: install [OPTIONS] -d database"
  echo "  -d    Database to use."
  echo "  -h    Connect to host. Default is ${HOSTNAME}."
  echo "  -p    Password to use when connecting to server. If password is not given"
  echo "        it's asked from the tty."
  echo "  -P    Port number to use for connection, built-in default is ${PORT}."
  echo "  -u    User for login if not current user. Default is ${USERNAME}."
  echo "   ?    Show this message and exit."
}

emysql() {
  printf "⇒ $1… \n  "
  mysql -b -A -h"${HOSTNAME}" -u"${USERNAME}" -p"${PASSWORD}" -P"${PORT}" $DATABASE < $1

  if [ 0 = $? ]; then
    echo "  OK"
  else
    echo "  Error (ignoring)"
  fi
}

[ 0 = $# ] && usage

while getopts ":h:u:p:P:d:C" opt; do
  case $opt in
    h)
      HOSTNAME="$OPTARG"
      ;;

    u)
      USERNAME="$OPTARG"
      ;;

    p)
      PASSWORD="$OPTARG"
      ;;

    P)
      PORT="$OPTARG"
      ;;

    d)
      DATABASE="$OPTARG"
      ;;

    \?)
      usage
      exit 0
      ;;

    *)
      usage
      exit 1
      ;;
  esac
done

for SQL in schema/*.sql; do
  emysql $SQL
done

for SQL in data/*.sql; do
  emysql $SQL
done

exit 0
