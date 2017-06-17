# yii2-migrate

Extended database migration support for Yii2

## Description

Place migrations into subdirs like migrationPath/year/month/m_*
 
## Installation

Add to composer:

```composer
"require": {
    "sklight/yii2-migrate" : "dev-master"
},
"repositories": [
    {
        "type": "vcs",
        "url":  "https://github.com/sklight/yii2-migrate.git"
    }
]
```

Add a new controller map in controllerMap section of your application's configuration file, for example:

```php
'controllerMap' => [
    'migrate' => 'sklight\migrate\controllers\MigrateController',
],
```

## Usage

```bash
./yii migrate/*
```
