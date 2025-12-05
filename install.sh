
usage(){
cat << EOF
usage: $0 [-a admin_user_name] [-e admin_email] -u db_user -d db -p password -s site_url
base url will be set in config.php

OPTIONS:
	-a admin username default to be "abc"
	-p database and admin password
	-e admin email default to be "abc@def.com"
	-d database name
	-s site url with format "http://..."
EOF
}
echo "Running install.sh in `pwd`"
username="abc"
email="abc@def.com"
db_user=""
db=""
password=""
site_url=""
while getopts "ha:e:p:u:d:s:" ops ; do
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

if [ "$db_user" = "" ]; then
	usage; exit 1
fi
if [ "$db" = "" ]; then
	db=$db_user
fi
if [ "$site_url" = "" ]; then
	usage; exit 1
fi
if [ "$password" = "" ]; then
	usage; exit 1
fi
echo $site_url


php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'ed0feb545ba87161262f2d45a633e34f591ebb3381f2e0063c345ebea4d228dd0043083717770234ec00c5a9f9593792') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# wget https://github.com/composer/composer/releases/download/2.1.9/composer.phar

php composer.phar install
cp .env.example .env
# site_url=`printf "%q" "$site_url"`
# password=`printf "%q" "$password"`

sed -i "s,APP_URL.*,APP_URL=$site_url,g" .env # Can't use / character here because it's url
sed -i "s/DB_USERNAME.*/DB_USERNAME=$db_user/g" .env
sed -i "s/DB_DATABASE.*/DB_DATABASE=$db/g" .env
sed -i "s/DB_PASSWORD.*/DB_PASSWORD=$password/g" .env

php artisan key:generate
php artisan migrate:refresh
php artisan db:seed --class=installation_seeding

php artisan add_admin truonganpn truonganpnt@ttafs.uit.edu.vn $password
php artisan add_admin $username $email $password
