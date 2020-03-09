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
        'host'      => 'localhost',
        'database'  => 'bekaku_php',
        'username'  => 'root',
        'password'  => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
		    'port'    => '3306',
        'strict'    => false,
    ),
```
## Configuration
