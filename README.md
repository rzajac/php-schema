## Database schema exporter

This command line and library helps dumping database schemas to a file.

## Supported databases

At the moment only MySQL is supported.

## Export / dump modes:

There are two export modes:

- as PHP array - creates includable PHP file with $createStatments
associative array where keys are table names and values are SQL CREATE statements.
- as SQL statements - creates file with CREATE statements for all tables in given database.

This tool not only exports CREATE statements but rewrites it in following way:
- resets AUTO_INCREMENT to 1
- adds CREATE TABLE IF NOT EXISTS (configurable)
- adds DROP TABLE IF EXISTS (configurable)


## Installation

Composer install:

```json
{
    "require": {
        "rzajac/schemadump": "0.4.*"
    }
}
```

Composer globally:

```
$ ./composer.phar global require rzajac/schemadump
```

## How to use

Run:

```
$ schemadump --help

Usage: schemadump [options]

Dump MySQL table create statements for all tables in selected database.

Options:
 -h                                : Database host name or IP
 -u                                : Database user name
 -p                                : Database port
 -d                                : Database name
 -c                                : Path to config file. If passed all other options are ignored
 -o                                : Output file
 --sql                             : dump file will contain only SQL statements.
                                     By default this tool will dump create statements to
                                     PHP associative array.

 --drop-before-create=[true|false] : Add DROP TABLE SQL before each CREATE TABLE statement
 --add-if-not-exists=[true|false]  : Add CREATE IF NOT EXISTS to all CREATE TABLE statements
 --help                            : This help message

Example config file:

    $config = array
    (
        'connection'    => array
        (
            'user'     => 'dbusername',
            'pass'     => 'dbpassword',
            'host'     => '127.0.0.1',
            'port'     => '3306',
            'database' => 'my_database_name'
        ),
        'export_type' => 'phparray',
        'drop_before_create' => TRUE,
        'add_if_not_exists' => TRUE,
        'output_file' => '/tmp/schema.sql'
    );

    return $config;
```

## schemadump API
 
```php
$schemaDump = SchemaDumpFactory::make($dbConfig);
$tableCreateStatement = $schemaDump->dbGetCreateStatement('tableName');
$tableDefinition = $schemaDump->dbGetTableDefinition('tableName');
```

Configuration array spec can be found [here](https://github.com/rzajac/phptools/blob/master/src/Db/DbConnect.php#L38)

## Where this script can be used

You could use it in unit tests. The tool exports the CREATE statements as includable PHP file. 
The file contains an associative array where keys are table names and values are CREATE statements.
See [http://someguyjeremy.com/blog/database-testing-with-phpunit](http://someguyjeremy.com/blog/database-testing-with-phpunit) 
where Jeremy Harris shows how you can load fixtures just for specific tables in your tests.
Using this tool you could add creating and doping tables from your database. 
Especially when your production / staging database schema changes frequently.

## Also see

If you like to keep your database schema and data in sync across many instances take a 
look at my schema sync project [https://github.com/rzajac/dbupdate](https://github.com/rzajac/dbupdate).

## License

Released under the Apache License 2.0.

