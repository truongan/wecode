#!/bin/bash

usage(){
cat << EOF
usage: $0 user_name admin_name class_name 
EOF
}

if [ -z $1 ]; then
	usage
	exit 1
fi


if [ -z $2 ]; then
	usage
	exit 1
fi

if [ -z $3 ]; then
	usage
	exit 1
fi
user=$1
admin=$2
class=$3

domain="$class.2020.wecode"
db=`echo $class | sed 's/\.//g'`
dbprefix="$user"_"$db"
dbpass=$(cat /dev/urandom | tr -dc 'a-z' | fold -w 15 | head -n 1)

echo $user $class $db $dbprefix $dbpass $domain
#exit
/usr/local/hestia/bin/v-add-domain $user $domain 
/usr/local/hestia/bin/v-add-database $user $db $db $dbpass

classupper=`echo $class | tr '[:lower:]' '[:upper:]'`
#echo "su $user -c \"cd ~/web/$domain/private; /opt/setup.multilang.sh -u $dbprefix -p $dbpass -n $classupper -a $admin\""
su $user -c "cd ~/web/$domain/private; /opt/setup.sh -u $dbprefix -p $dbpass -n $classupper -a $admin"
