
usage(){
cat << EOF
usage: $0 [-a admin_user_name] [-e admin_email] -p admin_password
base url will be set in config.php

OPTIONS:
	-a admin username default to be "abc"
	-p admin password
	-e admin email default to be "abc@def.com"
EOF
}

composer install
php artisan key:generate
php artisan migrate:refresh --seed