## Database schema

Library and command line tool for exporting and examining database schemas.

## Supported databases

At the moment only MySQL is supported.

## Export modes:

There are two export modes:

- **phpFile** - creates includable PHP file with $createStatements associative array where keys are table names and values are SQL CREATE statements.
- **sql** - creates file with CREATE statements for all tables in given database.

When used as a library there is additional export type:

- **phpArray** - returns PHP array with create statements to the caller.

This tool also rewrites create statements it in following way:

- resets AUTO_INCREMENT to 1
- adds CREATE TABLE IF NOT EXISTS (configurable)
- adds DROP TABLE IF EXISTS (configurable)


## Installation

To use as a library add this to `composer.json`:

```json
{
    "require": {
        "rzajac/schema": "0.6.*"
    }
}
```

To install as a command line tool:

```
$ composer global require rzajac/schema
```

## How to use

Run:

```
$ ./schema export -h
Usage:
  export [options]

Options:
  -c, --config[=CONFIG]  The path to configuration JSON file. If not set it will search for db_config.json in current directory
  -h, --help             Display this help message
  -q, --quiet            Do not output any message
  -V, --version          Display this application version
      --ansi             Force ANSI output
      --no-ansi          Disable ANSI output
  -n, --no-interaction   Do not ask any interactive question
  -v|vv|vvv, --verbose   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
 Export database schema
```

## Configuration file

```json
{
  "connection": {
    "username": "testUser",
    "password": "testUserPass",
    "host": "localhost",
    "port": "3306",
    "database": "test",
    "driver": "mysql"
  },
  "export_type": "php_array",
  "add_if_not_exists": true,
  "output_file": "tmp/schema.php"
}
```

- **export_type** - export create statements as: _php_file_, _php_array_, _sql_.
- **add_if_not_exists** - add IF NOT EXISTS to SQL create statements. 

Database `connection` spec can be found [here](https://github.com/rzajac/phptools/blob/master/src/Db/DbConnect.php)

## Library API
 
```php
// Config in the same format as above
$schema = SchemaFactory::make($dbConfig);
 
// Get create statements
$tableCreateStatement = $schema->dbGetCreateStatement('tableName');
 
// Get see TableDefinition class
$tableDefinition = $schema->dbGetTableDefinition('tableName'); 
```

See [TableDefinition](src/TableDefinition.php) class.

Class documentation can be found [here](docs/index.md).

## Where this script can be used

You could use it in unit tests. The tool exports the CREATE statements as includable PHP file. 
The file contains an associative array where keys are database table names and values are CREATE statements.
See [http://someguyjeremy.com/blog/database-testing-with-phpunit](http://someguyjeremy.com/blog/database-testing-with-phpunit) 
where Jeremy Harris shows how you can load fixtures just for specific tables in your tests.

## Also see

If you like to keep your database schema and data in sync across many instances take a 
look at my schema sync project [https://github.com/rzajac/dbupdate](https://github.com/rzajac/dbupdate).

## Running unit tests

```sql
CREATE USER 'testUser'@'localhost' IDENTIFIED BY 'testUserPass';

CREATE DATABASE testSchemaLib DEFAULT CHARACTER SET = 'utf8';
GRANT CREATE ROUTINE, CREATE VIEW, ALTER, SHOW VIEW, CREATE, ALTER ROUTINE, EVENT, INSERT, SELECT, DELETE, TRIGGER, GRANT OPTION, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, CREATE TEMPORARY TABLES, INDEX ON `testSchemaLib`.* TO 'testUser'@'localhost';
```

## License

Released under the Apache License 2.0.
