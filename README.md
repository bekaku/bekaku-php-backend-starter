# PHP backend system

Requirements
------------

Only supported on PHP 5.6 and up.

## Usage

### 1. Download this repository
```
git clone https://github.com/bekaku/bekaku-php-rest-api-starter my-app
```

Repository will be downloaded into `my-app/` folder

## Database

Database file located at `my-app`/data/files/bekaku_php.sql and you can use following command for restore to your db.

```sql
$ mysql -uroot -p your_db_name < your_backup_file_path
```
example on windows
```sql
$ mysql -uroot -p bekaku_php < E:\bak\db\bekaku_php.sql
```
example on Ubuntu
```sql
$ mysql -uroot -p bekaku_php < /var/tmp/bekaku_php.sql
```
default admin username and password
```
Username : admin@bekaku.com
Password : P@ssw0rd
```
## Configuration Path
Config your application path at `my-app`/.env
 
SET ENVIRONMENT VARIABLE IN .env

 ```.env
PROJECT_HOME="/my-app"
PROJECT_DATA_HOME="D:/xampp/htdocs/my-app/data"
PROJECT_DATA_DISPLAY="/my-app/data"

APP_KEY="GBPDx9HnjYjGKu7dptpZd8tF2H5Rgz2w"

DB_CONNECTION="mysql"
DB_HOST="127.0.0.1"
DB_PORT=3306
DB_DATABASE="bekaku_php"
DB_USERNAME="root"
DB_PASSWORD=""

APP_DEFAULT_PASSWORD="P@ssw0rd"
```
If you Move your data folders to outside DocumentRoot. You can map Alias directory at `apacheFolder\conf\httpd.conf` Or `/etc/apache2/apache2.conf` in ubuntu.

Windows
```
<IfModule mod_alias.c>
    Alias /myCustomPublicPath/ "D:/myPath/data/"
    <Directory "D:/myPath/data">
        Options Indexes MultiViews
        AllowOverride None
        Order allow,deny
        Allow from all
    </Directory>	
</IfModule>
```
Ubuntu
```
<IfModule mod_alias.c>
    Alias /myCustomPublicPath/ "/var/myPath/data/"
    <Directory "/var/myPath/data">
        Options Indexes MultiViews
        AllowOverride None
        Order allow,deny
        Allow from all
    </Directory>
</IfModule>
```
Config your database connection at `my-app`/data/configuration/app.php
```php
    /*
    |--------------------------------------------------------------------------
    | DATABASE CONNECTIVITY SETTINGS
    |--------------------------------------------------------------------------
    */
    'db_default_driver' => 'mysql',
    'mysql' => array(
        'driver' => get_env('DB_CONNECTION'),
        'host' => get_env('DB_HOST'),
        'database' => get_env('DB_DATABASE'),
        'username' => get_env('DB_USERNAME'),
        'password' => get_env('DB_PASSWORD'),
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => '',
        'port' => get_env('DB_PORT'),//3306
        'strict' => false,
    ),
```

## Site Connection
Config your site connection at `my-app`/data/configuration/app.php
```php
    'secure' => false,
    'url' => 'localhost',//your server's domain or ip
    'url_port' => '80',//your http port
    'ssl_port' => '443',//your https port
```

## Project Structure
