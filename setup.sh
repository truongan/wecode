#!/bin/bash

usage(){
cat << EOF
usage: $0 [-i install_dir] [-o public_dir] -u db_user -p db_password [-n site_name] [base_url]

base url will be set in config.php

OPTIONS:
	-h show this message
	-i install directory, default to current working directory
	-o public directory to put index.php, default to be the public
	   directory in the same directory as install directory
	-u database username
	-p database password
	-d database name
	-n site name to display on top bar and construct base url for uit classes
	base_url: Will be use to set base url for the site when site_name is not specified. The https:// part must be included
EOF
}

install="`pwd`"
public=''
db_user=''
db=''
db_password=''
base_url=''
site_name=''
default_admin_name=''
while getopts "hi:o:u:p:d:n:a:" ops ; do
	case "${ops}" in
		h)	usage ;;
		i)	install=${OPTARG};;
		o)	public=${OPTARG};;
		u)	db_user=${OPTARG};;
		p)	db_password=${OPTARG};;
		d)	db=${OPTARG};;
		n)  site_name=${OPTARG};;
		a)  default_admin_name=${OPTARG};;
		*)	usage; exit 1;;
	esac
done
shift $((OPTIND-1))

base_url=$1
lw_site_name=`echo $site_name | tr '[:upper:]' '[:lower:]'`

if [ "$site_name" = "" ]; then
	if [ "$base_url" = "" ]; then
		usage; exit 1
	fi
	site_name="Wecode-Judge"
else
	if [ "$base_url" = "" ] ; then
		base_url="https://khmt.uit.edu.vn/wecode/`echo $site_name | tr '[:upper:]' '[:lower:]'`/"
	fi
fi

if [ "$db_user" = "" ]; then
	usage; exit 1
fi
echo $public
if [ "$public" = "" ]; then
	public="$install/../public_html"
fi

if [ "$db" = '' ]; then
	db="$db_user"
fi

#convert install and public to absolute path
if [ -d "$install" ]; then
	install=`readlink -f $install`
else
	echo "Installation directory: $install not found"
fi
if [ -d "$public" ]; then
	public=`readlink -f $public`
else
	echo "Public directory: $public not found"
fi

cat << EOF
"install=$install"
"public=$public"
"db_user=$db_user"
"db=$db"
"db_password=$db_password"
"base_url=$base_url"
"site_name=$site_name"
EOF

cd $install
git clone  'https://github.com/truongan/wecode' .
#git checkout working-updateci


cd $public
if cmp -s $install $public; then
	echo "Installation dir and public dir are the same; that may pose security risk"
else
	read -p "$public/index.php $public/assets $public/.htaccess will be delete permenantly. Continue?" -n 1 -r REPLY
	echo
	if [[ $REPLY =~ ^[Yy]$ ]]
	then
	  	rm index.html index.php .htacess
	  	rm -rf ./assets
		ln -s $install/public/* . 
		ln -s $install/public/.* . 
	else
		echo "Abort"
		exit 0
	fi
fi

$install/install.sh   -a $default_admin_name  -u $db_user -p $db_password -s "$base_url"