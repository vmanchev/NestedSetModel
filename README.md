# NestedSetModel as ZF2 module

## Current functionality includes:
1. Add new node (on any level)
2. Edit node name
3. Delete node (on any level)

## How to install?

1. Import the nestedsetmodel.sql file from /NestedSetModel/config
2. Update your main /config/autoload/global.php file to:

```php
<?php
return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=YOUR_DB_NAME;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'YOUR_DB_USERNAME',
        'password' => 'YOUR_DB_PASSWORD',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
    ),
);
```

Note, you should have Pdo and Pdo_MySQL already installed.

3. Update your database connection settings. You could move the 
username and password to a local.php file.

4. Add "NestedSetModel" to your main /config/application.config.php file, 
under the "modules" section.

5. Point your browser to http://your-domain/catagory 
