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
 Application Path
 ```php
$GLOBALS_PROJECT_NAME = "my-app";/your application path name
$GLOBALS_PROJECT_PATH = "/githup/$GLOBALS_PROJECT_NAME/";//path after your web server root path 

$GLOBALS_PROJECT_APP_DATA_DISPLAY = "/githup/$GLOBALS_PROJECT_NAME/data";
$GLOBALS_PROJECT_APP_DATA_UPLOAD = "C:/xampp/htdocs/$GLOBALS_PROJECT_NAME/data/";
```
