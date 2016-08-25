## Class Kicaj\Schema\Cmd\ExportCmd
Schema export command.

## Extends

- Symfony\Component\Console\Command\Command

## Constants

```php
const CONFIG_KEY_PATH = '_config_path_';
```

## Methods

|                                                    |                                                    |                                                    |
| -------------------------------------------------- | -------------------------------------------------- | -------------------------------------------------- |
|              [configure](#configure)               |                [execute](#execute)                 |          [getConfigPath](#getconfigpath)           |
|         [readConfigFile](#readconfigfile)          |        [fixOutputFormat](#fixoutputformat)         | [amendAndValidateConfig](#amendandvalidateconfig)  |
|      [getOutputFilePath](#getoutputfilepath)       |          [askDbPassword](#askdbpassword)           |        [checkDbPassword](#checkdbpassword)         |

## Properties

|                      |
| -------------------- |
|  [$config](#config)  |

-------

#### $config
Configuration.

```php
protected array $config = array()
```

-------
## Methods
#### configure

```php
protected function configure() : 
```

-------
#### execute

```php
protected function execute() : 
```

-------
#### getConfigPath
Get default config path if not provided as CLI option.
```php
public function getConfigPath(string $configPath) : string
```
Arguments:
- _$configPath_ **string** - The config path provided on CLI.

Returns: **string** - The path to configuration file.

-------
#### readConfigFile
Read configuration file.

Method sets _config_path_ key in config array to $configPath.
```php
public function readConfigFile(string $configPath) : array
```
Arguments:
- _$configPath_ **string** - The path to JSON configuration file.

Throws:
- Kicaj\Tools\Exception

Returns: **array**

-------
#### fixOutputFormat
Fix output format.

On CLI export as array means PHP file.
```php
public function fixOutputFormat(array $config) : array
```
Arguments:
- _$config_ **array** - The configuration array.

Returns: **array** - Fixed configuration array.

-------
#### amendAndValidateConfig
Amend configuration options.

- update to absolute paths
- set config_dir
- update Schema::CONFIG_KEY_EXPORT_FORMAT if needed
```php
public function amendAndValidateConfig(array $config) : array
```
Arguments:
- _$config_ **array** - The configuration array.

Throws:
- Kicaj\Tools\Exception

Returns: **array** - The amended and validated configuration array.

-------
#### getOutputFilePath
Returns path to output file.
```php
public function getOutputFilePath(string $configDir, string $outputPath) : string
```
Arguments:
- _$configDir_ **string** - The absolute path where configuration file is., 
- _$outputPath_ **string** - The path where output file should be put. Paths not starting with / are treated
as relative to config file.

Throws:
- Kicaj\Tools\Exception

Returns: **string**

-------
#### askDbPassword
Get database password.

Note: Will ask user for password if database password is not provided in config.
```php
public function askDbPassword() : string
```

Returns: **string**

-------
#### checkDbPassword
Get configuration array.
```php
public function checkDbPassword(array $config) : array
```
Arguments:
- _$config_ **array** - The Schema configuration array.

Returns: **array** - The configuration array.

-------
