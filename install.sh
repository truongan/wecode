
usage(){
cat << EOF
usage: $0 [-a admin_user_name] [-e admin_email] -u db_user -p password -s site_url
base url will be set in config.php

OPTIONS:
	-a admin username default to be "abc"
	-p admin password
	-e admin email default to be "abc@def.com"
	-s site url
EOF
}

username="abc"
email="abc@def.com"
db_user=""
db=""
password=""
site_url=""
while getopts "ha:e:p:" ops ; do
	case "${ops}" in
		h)	usage ;;
		a)	username=${OPTARG};;
		e)	email=${OPTARG};;
		u)	db_user=${OPTARG};;
		d)	db=${OPTARG};;
		p)	password=${OPTARG};;
		s)	site_url=${OPTARG};;
		*)	usage; exit 1;;
	esac
done

if [ "$db" = "" ]; then
	usage; exit 1
fi
if [ "$db_user" = "" ]; then
	usage; exit 1
fi
if [ "$site_url" = "" ]; then
	usage; exit 1
fi
if [ "$password" = "" ]; then
	usage; exit 1
fi

composer install
cp .env.example .env
site_url=`printf "%q" "site_url"`
password=`printf "%q" "password"`
sed -i "s/APP_URL.*/APP_URL=$site_url/g" .env
sed -i "s/DB_USERNAME.*/DB_USERNAME=$db_user/g" .env
sed -i "s/DB_DATABASE.*/DB_DATABASE=$db/g" .env
sed -i "s/DB_PASSWORD.*/DB_PASSWORD=$password/g" .env

php artisan key:generate
php artisan migrate:refresh 
php artisan db:seed --class=installation_seeding

php artisan add_admin $username $email $password
