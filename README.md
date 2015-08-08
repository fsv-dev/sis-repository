# sis-repository
[![License](https://img.shields.io/badge/license-GPLv2-blue.svg)](https://img.shields.io/badge/license-GPLv2-blue.svg)

Nette\DI extension for SIS repository

## Install
```sh
$ composer require fsv-dev/sis-repository
```

## Configuration

### config.neon

```php
extensions:
	sis: SIS\DI\SISExtension
	
	sis:
    	server: 127.0.0.1
    	user: 'username'
    	password: 'password'
    	port: 1111
    	sid: 'sid'
    	protocol: 'TCP' # default value
    	charset: 'UTF8' # default value
    	facultyCode: 12345
    	
```

## Simple use

```php
$this->searchForStudent($name, $surname) // Returns simple student's info (array)
$this->getStudentInfo($sident) // Returns more information of student (array)
$this->getStudyInfo($sident) // Returns all student's programs, subjects and credits (multi array)

