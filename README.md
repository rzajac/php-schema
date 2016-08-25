## Database schema

Library and command line tool for exporting and examining database schema.

## Supported databases

At the moment only MySQL is supported.

## Export modes:

There are two export modes:

- **php_file** - creates includable PHP file with $createStatements associative array where keys are table or view names and values are SQL CREATE statements.
- **sql** - creates file with SQL CREATE statements for all tables and views in given database.

When used as a library there is additional export type:

- **php_array** - returns to the caller the same array as in **php_file** mode.

This tool also rewrites create statements it in following way:

- resets AUTO_INCREMENT to 1
- adds CREATE TABLE IF NOT EXISTS (configurable)

## Installation

To use as a library add this to `composer.json`:

```json
{
    "require": {
        "rzajac/schema": "^0.8"
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
  "export_format": "php_array",
  "add_if_not_exists": true,
  "output_file": "tmp/schema.php"
}
```

- **export_format** - export create statements as: _php_file_, _php_array_, _sql_.
- **add_if_not_exists** - add IF NOT EXISTS to SQL create statements. 

Database `connection` spec can be found [here](https://github.com/rzajac/phptools/blob/master/src/Db/DbConnect.php)

## Library API
 
```php
// Config in the same format as above
$db = Db::factory($dbConfig);
 
// Get table.
$table = $db->dbGetTableDefinition('tableName');
 
// Get get create statement.
$createStatement = $table->getCreateStatement();
 
// Other examples.

$columns = $table->getColumns();
$indexes = $table->getIndexes();

$column = $table->getColumnByName('id');

$column->isAutoincrement();
$column->isPartOfPk();

```

For more info see documentation [here](docs/index.md).

## Running unit tests

Create test table and user:

```sql
CREATE USER 'testUser'@'localhost' IDENTIFIED BY 'testUserPass';

CREATE DATABASE testSchemaLib DEFAULT CHARACTER SET = 'utf8';
GRANT CREATE ROUTINE, CREATE VIEW, ALTER, SHOW VIEW, CREATE, ALTER ROUTINE, EVENT, INSERT, SELECT, DELETE, TRIGGER, GRANT OPTION, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, CREATE TEMPORARY TABLES, INDEX ON `testSchemaLib`.* TO 'testUser'@'localhost';
```

Run tests:

```
$ vendor/bin/phpunit
```

When you have XDebug enabled running unit tests creates coverage report in `coverage` directory.

## License

Released under the Apache License 2.0.
