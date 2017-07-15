# Установка вручную
Если вы пользуетесь Windows, то настоятельно рекомендуем воспользоваться готовым пакетом, в котором всё уже установлено. Как это сделать см. [здесь](README.md#Быстрый-старт-windows).

В качестве примера Linux используется Ubuntu.

## 1. Скачайте и установите PostgreSQL 9.6
### Windows:
[Скачайте](http://www.enterprisedb.com/products/pgdownload.do#windows) и установите. Во время установки вас попросят ввести пароль от пользователя postgres. Вы можете использовать "anime365" или придумать свой (главное запишите его).

### Linux:
Не используйте PostgreSQL из репозитория вашего дистрибутива (обычно он там старый), вместо этого добавьте официальный репозиторий PostgreSQL. Не забудьте установить также postgresql-contrib.
Пример для Ubuntu 16.04:
```
echo 'deb http://apt.postgresql.org/pub/repos/apt/ xenial-pgdg main' | sudo tee --append /etc/apt/sources.list.d/pgdg.list
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
apt-get install postgresql-9.6 postgresql-contrib-9.6
```

## 2. Создайте пользователя и базу данных для PostgreSQL
### Windows:
Откройте SQL редактор (например Navicat или pgAdmin) и создайте пользователя anime365, а затем базу данных anime365 (указав anime365 владельцем).
Затем выполните запрос `create extension pg_trgm;` в базе данных anime365 (от имени пользователя postgres).

### Linux:
```
sudo -i -u postgres # Заходим от имени postgres (такой пользователь появится после установки, это суперпользователь для PostgreSQL)
createuser --pwprompt anime365 # Создаем пользователя anime365, будет запрошен новый пароль (укажите например anime365)
createdb -O anime365 anime365 # Создаем базу данных anime365 и устанавливаем пользователя anime365 владельцем
psql -d anime365 -c 'create extension pg_trgm;' # Загружаем расширение pg_trgm (требуется для нашего проекта)
exit # Выходим из сессии от имени postgres
```
Если у вас возникают ошибки при включении расширения pg_trgm, проверьте установлен ли у вас postgresql-contrib.


## 3. Импортируйте дамп
### Windows:
Откройте SQL редактор (например Navicat или pgAdmin), войдите от пользователя anime365 и выберите базу данных anime365. Скачайте [dev_dump.sql.gz](https://smotret-anime.ru/content/dev_dump.sql.gz), разахивируйте его, а затем импортируйте.
Если при импорте возникают ошибки, проверьте включено ли у вас расширение pg_trgm (см. код во втором пункте).

Также для импорта можно использовать Tools/Import_db.cmd из [нашего пакета для Windows](README.md#Быстрый-старт-windows).

### Linux:
```
curl -o dev_dump.sql.gz https://smotret-anime.ru/content/dev_dump.sql.gz # Скачиваем дамп
gunzip -c dev_dump.sql.gz | psql --set ON_ERROR_STOP=on --username=anime365 --dbname=anime365 --host=127.0.0.1 # Импортируем его в базу
```

## 4. Установите nginx и PHP 7.1
### Windows:
Скачайте актуальную версию [nginx для Windows](http://nginx.org/ru/download.html) и [PHP для Windows](http://windows.php.net/download/) (выберите PHP 7.1 x64 Non Thread Safe).

### Linux:
Установите PHP 7.1 + nginx, а также заодно другие пакеты, используемые в проекте.
```
apt-get update && apt-get install software-properties-common python-software-properties -y && add-apt-repository ppa:ondrej/php -y && add-apt-repository ppa:jonathonf/ffmpeg-3 -y && apt-get update && apt-get install ffmpeg atool unzip screen mkvtoolnix aria2 curl p7zip p7zip-full redis-server php7.1-cli php7.1-fpm php7.1-mbstring php7.1-curl php-imagick php7.1-pgsql php7.1-xml nginx -y
```


## 5. Скачайте и установите проект
```
git clone https://github.com/a365/anime365.git
cd anime365
composer global require "fxp/composer-asset-plugin:^1.2.0" -vv --profile
composer install -vv --profile
```

Если у вас не установлен composer, то вы можете скачать его [здесь](https://getcomposer.org/download/).


## 6. Направьте nginx на PHP-FPM
Отредактируйте nginx.conf (также можно использовать /etc/nginx/sites-enabled/ если у вас Linux) как в примере:
```
server {
        root /your/path; # Замените на свой путь к исходникам сайта

        ...
        
        location / {
            try_files $uri $uri/ /index.php?$args;
        }
        location ~ ^/index.php$ {
            # NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
            fastcgi_pass unix:/var/run/php/php7.1-fpm.sock; # Fox Linux
            #fastcgi_pass 127.0.0.1:9100; # For Windows
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME /your/path/www/index.php; # Замените на свой путь к исходникам сайта
            fastcgi_param DOCUMENT_ROOT /your/path; # Замените на свой путь к исходникам сайта
            client_max_body_size 100m;
        }
        
        ...
	}
```

## 7. Настройка PHP
Отредактируйте php.ini, установив `cgi.fix_pathinfo = 0;`.
### Дополнительно (для Windows)
Активируйте (раскомментируйте в php.ini) расширения:
```
extension=php_curl.dll
extension=php_intl.dll
extension=php_mbstring.dll
extension=php_openssl.dll
extension=php_pdo_pgsql.dll
```

## 8. Измените config/db.php
Откройте `config/db.php` и измените пароль на тот, который вы использовали при установке PostgreSQL.

(в этом нет необходимости если вы использовали пароль "anime365")