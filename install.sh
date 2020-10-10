
usage(){
cat << EOF
usage: $0 [-a admin_user_name] [-e admin_email] -p admin_password -s site_url
base url will be set in config.php

OPTIONS:
	-a admin username default to be "abc"
	-p admin password
	-e admin email default to be "abc@def.com"
EOF
}

username="abc"
email="abc@def.com"
password=""
site_url=""
while getopts "ha:e:p:" ops ; do
	case "${ops}" in
		h)	usage ;;
		a)	username=${OPTARG};;
		e)	public=${OPTARG};;
		p)	password=${OPTARG};;
		s)	site_url=${OPTARG};;
		*)	usage; exit 1;;
	esac
done

if [ "$site_url" = "" ]; then
	usage; exit 1
fi
if [ "$password" = "" ]; then
	usage; exit 1
fi

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:refresh 
php artisan db:seed --class=installation_seeding

php artisan add_admin $username $email $password
