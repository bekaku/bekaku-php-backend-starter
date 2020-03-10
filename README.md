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
Username : admin
Password : P@ssw0rd
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

## Site Connection
Config your site connection at `my-app`/data/configuration/app.php
```php
    'secure' => false,
    'url' => 'localhost',//your server's domain or ip
    'url_port' => '80',//your http port
    'ssl_port' => '443',//your https port
```

## Configuration Path
Config your application path at `my-app`/.htaccess
 
SET ENVIRONMENT VARIABLE IN HTACCESS

To enable mod_env in ubuntu use the following commands.

 ```
sudo a2enmod env
sudo service apache2 restart
 ```
 
 ```
SetEnv MYAPP_ENV envValue
 ```


 ```.htaccess
#PROJECT_HOME is path after your web server DocumentRoot
SetEnv PROJECT_HOME /my-app

#PROJECT_DATA_HOME is real path of 'data' folder
SetEnv PROJECT_DATA_HOME D:/xampp/htdocs/my-app/data

#PROJECT_DATA_DISPLAY is path for access from public
SetEnv PROJECT_DATA_DISPLAY /my-app/data
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
