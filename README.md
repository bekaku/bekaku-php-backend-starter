# PHP backend system

Requirements
------------

Only supported on PHP 5.6 and up.

## Usage

### 1. Download this repository
```
git clone https://github.com/bekaku/bekaku-php-backend-starter my-app
```

Repository will be downloaded into `my-app/` folder

## Database

Database file located at `my-app`/data/files/bekaku_php.sql and you can use below command for restore to your db.

```
$ mysql -uroot -p your_db_name < your_backup_file_path
```
example on windows
```
$ mysql -uroot -p bekaku_php < E:\bak\db\bekaku_php.sql
```
example on Ubuntu
```
$ mysql -uroot -p bekaku_php < /var/tmp/bekaku_php.sql
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
        'driver'    => 'mysql',
        'host'      => 'YOUR_MYSQL_SERVER_IP',//locahost
        'database'  => 'bekaku_php',
        'username'  => 'YOUR_MYSQL_USERNAME',//root
        'password'  => 'YOUR_MYSQL_PASSWORD',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
	'port'    => '3306',
        'strict'    => false,
    ),
```
## Configuration
 Config your application path at `my-app`/data/configuration/app.php 
 
 ```php
$GLOBALS_PROJECT_NAME = "my-app";/your application path name
$GLOBALS_PROJECT_PATH = "/githup/$GLOBALS_PROJECT_NAME/";//path after your web server DocumentRoot 

$GLOBALS_PROJECT_APP_DATA_DISPLAY = "/githup/my-app/data";//path for access from public
$GLOBALS_PROJECT_APP_DATA_UPLOAD = "D:/xampp/htdocs/githup/my-app/data/";//real path of 'data' folder
```
If you Move your data folders to outside DocumentRoot. You can map Alias directory at 'apacheFolder\conf\httpd.conf' Or /etc/apache2/apache2.conf from ubuntu.

Windows
```
<IfModule mod_alias.c>
    Alias /myCdn/ "D:/myPath/data/"
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
    Alias /myCdn/ "/var/myPath/data/"
    <Directory "/var/myPath/data">
        Options Indexes MultiViews
        AllowOverride None
        Order allow,deny
        Allow from all
    </Directory>
</IfModule>
```


