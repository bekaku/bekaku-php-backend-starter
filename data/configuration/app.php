<?php

//$GLOBALS_PROJECT_PATH = "/github/php/bekaku-php-backend-starter/";//path after your web server DocumentRoot

//$GLOBALS_PROJECT_APP_DATA_DISPLAY = "/github/php/bekaku-php-backend-starter/data";//path for access from public
//$GLOBALS_PROJECT_APP_DATA_UPLOAD = "D:/php_htdocs/github/php/bekaku-php-backend-starter/data/";//real path of 'data' folder

// windows D:/php_htdocs/github/php/bekaku-php-backend-starter/data/, production linux /var/bekaku-php-backend-starter/data/, ,mac -> /Users/bekaku/bekaku-php-backend-starter/data/


return array(
    'app_name' => 'begagu',
    'locale' => 'th',
    /*
    |--------------------------------------------------------------------------
    | Production Mode
    |--------------------------------------------------------------------------
    */
    'production_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Error Logging Threshold
    |--------------------------------------------------------------------------
    |	0 = Disables logging, Error logging TURNED OFF
    |	1 = Error Messages (including PHP errors)
    |	2 = Debug Messages
    |	3 = Informational Messages
    |	4 = All Messages
    */
    'log' => array(
        'log_threshold' => 4,
        'log_path' => get_env('PROJECT_DATA_HOME') . '/logs/',
        'log_date_format' => 'Y-m-d H:i:s',
        'can_log' => true,
    ),
    /*
    |--------------------------------------------------------------------------
    | Gmail Env for send mail
    |--------------------------------------------------------------------------
    */
    'gmail' => array(
        'host' => 'smtp.googlemail.com',
        'smtp_auth' => true,
        'username' => '',
        'password' => '',
        'smtp_secure' => 'ssl',
        'port' => 465,
    ),
    'mail_notify_list' => array(
        array(
            'address' => 'xxx@gmail.com',
            'name' => 'bekaku'
        ),
    ),

    /*
  |--------------------------------------------------------------------------
  | Custom Env for send mail
  |--------------------------------------------------------------------------
  */
    'app_mail' => array(
        'host' => '',
        'smtp_auth' => true,
        'username' => '',
        'password' => '',
        'smtp_secure' => 'ssl',
        'port' => 465,
    ),
    /*
    |--------------------------------------------------------------------------
    | File and Directory Modes
    |--------------------------------------------------------------------------
    |
    | These prefs are used when checking and setting modes when working
    | with the file system.  The defaults are fine on servers with proper
    | security, but you may wish (or even need) to change the values in
    | certain environments (Apache running a separate process for each
    | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
    | always be used to set the mode correctly.
    |
    */
    'chmod_mode' => array(
        'file_read_mode' => 0644,
        'file_write_mode' => 0666,
        'dir_read_mode' => 0755,
        'dir_write_mode' => 0777,
    ),

    'upload_image' => array(
        'default_width' => 960,
        'default_height' => 960,
        'avatar_thumnail_main' => 160,
        'avatar_thumnail_second' => 40,
        'avatar_thumnail_third' => 32,
        'input_name' => 'v_file',
        'create_thumbnail' => true,
        'create_thumbnail_width' => 120,
        'create_thumbnail_exname' => '_thumb',
    ),


    /*
    |--------------------------------------------------------------------------
    | File Stream Modes
    |--------------------------------------------------------------------------
    |
    | These modes are used when working with fopen()/popen()
    |
    */
    'fopen_mode' => array(
        'fopen_read' => 'rb',
        'fopen_read_write' => 'r+b',
        'fopen_write_create_destructive' => 'wb',// truncates existing file data, use with care
        'fopen_read_write_create_destructive' => 'w+b',// truncates existing file data, use with care
        'fopen_write_create' => 'ab',
        'fopen_read_write_create' => 'a+b',
        'fopen_write_create_strict' => 'xb',
        'fopen_read_write_create_strict' => 'x+b',
    ),

    /*
    |--------------------------------------------------------------------------
    | mod_rewrite
    |--------------------------------------------------------------------------
    |mod_rewrite enable in AppServ\Apache2.2\conf and restart Apache
    */
    'url_rewriting' => true,
    'url_rewriting_extension' => '',//eg .do, .htm

    /*
    |--------------------------------------------------------------------------
    | Session And Cookie
    |--------------------------------------------------------------------------
    */
    'cookie_time' => (3600 * 24 * 30), // 30 days,


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
        'port' => get_env('DB_PORT'),//3306, 13537
        'strict' => false,
    ),
    'sqlite' => array(
        'driver' => 'sqlite',
        'database' => 'D:/database.sqlite',//path of sqlite
        'prefix' => '',
    ),
    'pgsql' => array(
        'driver' => 'pgsql',
        'host' => '',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
        'schema' => 'public',
    ),
    'sqlsrv' => array(
        'driver' => 'sqlsrv',
        'host' => 'localhost',
        'database' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',
        'prefix' => '',
    ),

    /*
    | any  == anybody can register (default)
    | admin == members must be registered by an administrator
    | root  == only the root user can register members
    */
    'can_register' => 'any',
    'default_role' => 'member',

    'base_paging_param' => 'page',
    'base_module_name' => 'module',
    /*
    | Is this a secure connection?  The default is FALSE, but the use of an
    | HTTPS connection for logging in is recommended.
    | If you are using an HTTPS connection, change this to TRUE
    */
    'secure' => false,
    'url' => 'localhost',//your server's domain or ip
    'url_port' => '80',//your http port
    'ssl_port' => '443',//your https port
    'url_rewriting_project_path' => get_env('PROJECT_HOME')."/",
    'base_project_path' => get_env('PROJECT_HOME')."/",
    'base_project_resources_path' => get_env('PROJECT_HOME') . '/resources',

    /*
     | can use "index.php" will be like "index.php?module=login" or "" empty string will be like "?module=login"
     | config .htaccess for xample.com/login for hide 'index.php?module='
     */
    'base_index_name' => 'index.php',
    'base_data_path' => get_env('PROJECT_DATA_HOME'),
    'base_data_display' => get_env('PROJECT_DATA_DISPLAY'),
);