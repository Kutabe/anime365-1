# Manual installation
If you're using Windows, then we strongly suggest you to use "ready to use" package. [See more](README.md#windows).

Ubuntu is used in examples for Linux.

## 1. Download and install PostgreSQL 9.6
### Windows:
[Download](http://www.enterprisedb.com/products/pgdownload.do#windows) and install. Installer will ask you password for "postgres" user. You can use "anime365" or your own (don't forget to write/remember this password).

### Linux:
Do not use PostgreSQL from your system repository (typically it's quite old), use official PostgreSQL repository instead. Don't forget to install postgresql-contrib.
```
echo 'deb http://apt.postgresql.org/pub/repos/apt/ xenial-pgdg main' | sudo tee --append /etc/apt/sources.list.d/pgdg.list
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
apt-get install postgresql-9.6 postgresql-contrib-9.6
```

## 2. Create user and database for PostgreSQL.
### Windows:
Use your favorite SQL editor (for example Navicat or pgAdmin) and create user "anime365", then database "anime365" (select "anime365" as owner).
Then execute query `create extension pg_trgm;` in database "anime365" (you should login as postgres).

### Linux:
```
sudo -i -u postgres # Login as postgres (that is superuser for PostgreSQL)
createuser --pwprompt anime365 # Create user "anime365", you'll have to type a password (for example "anime365")
createdb -O anime365 anime365 # Create database "anime365" with owner "anime365"
psql -d anime365 -c 'create extension pg_trgm;' # Load extension pg_trgm (required for our project)
exit # Exit from sudo session
```
Check if you installed postgresql-contrib if you getting an error when executing `create extension pg_trgm;`.


## 3. Import SQL dump
### Windows:
Use your favorite SQL editor (for example Navicat or pgAdmin), login as "anime365" and select database "anime365". Download [dev_dump.sql.gz](https://smotret-anime.ru/content/dev_dump.sql.gz), unzip it, and then import.
If during import you getting an errors, check if extension pg_trgm installed (see above).

You can also import using script Tools/Import_db.cmd from [our package for Windows](../README.md#windows).

### Linux:
```
curl -o dev_dump.sql.gz https://smotret-anime.ru/content/dev_dump.sql.gz # Download SQL dump
gunzip -c dev_dump.sql.gz | psql --set ON_ERROR_STOP=on --username=anime365 --dbname=anime365 --host=127.0.0.1 # Import it
```

## 4. Install nginx and PHP 7.1
### Windows:
Download [nginx for Windows](http://nginx.org/ru/download.html) and [PHP for Windows](http://windows.php.net/download/) (select PHP 7.1 x64 Non Thread Safe).

### Linux:
Install PHP 7.1 + nginx, and also other packages used in our project.
```
apt-get update && apt-get install software-properties-common python-software-properties -y && add-apt-repository ppa:ondrej/php -y && add-apt-repository ppa:jonathonf/ffmpeg-3 -y && apt-get update && apt-get install ffmpeg atool unzip screen mkvtoolnix aria2 curl p7zip p7zip-full redis-server php7.1-cli php7.1-fpm php7.1-mbstring php7.1-curl php-imagick php7.1-pgsql php7.1-xml nginx -y
```

## 5. Download and setup project
```
git clone https://github.com/a365/a365.git
cd a365
composer global require "fxp/composer-asset-plugin:^1.2.0" -vv --profile
composer install -vv --profile
```

If you don't have composer, you can download it [here](https://getcomposer.org/download/).

## 6. Setup nginx to use PHP-FPM
Edit nginx.conf (you can also use /etc/nginx/sites-enabled/ in Linux) as in example:
```
server {
        root /your/path; # Change to your path (folder with sources)

        ...
        
        location / {
            try_files $uri $uri/ /index.php?$args;
        }
        location ~ ^/index.php$ {
            # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
            set $fsn /upload.php;
            fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME /your/path/www/index.php; # Change to your path (folder with sources)
            fastcgi_param DOCUMENT_ROOT /your/path; # Change to your path (folder with sources)
            client_max_body_size 100m;
        }
        
        ...
	}
```

## Change PHP settings
Edit `php.ini` and change `cgi.fix_pathinfo = 0;`.
### And also (if you using Windows)
Activate (uncomment in php.ini) these extensions:
```
extension=php_curl.dll
extension=php_intl.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_pgsql.dll
```

## 7. Change config/db.php
Open `config/db.php` and change password to the password you used during PostgreSQL installation.

(that not necessary if you used password "anime365")