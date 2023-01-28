# BaksDev Product Category

![Version](https://img.shields.io/badge/version-6.2-blue) ![php 8.1+](https://img.shields.io/badge/php-min%208.1-red.svg)

Модуль категорий продукции

## Установка

``` bash
$ composer require baks-dev/products-category
```

## Дополнительно

Установка файловых ресурсов (javascript, css, image ...):

``` bash
$ php bin/console baks:assets:install
```

Роли администартора с помощью Fixtures

``` bash
$ php bin/console doctrine:fixtures:load --append
```
Внесите изменения в схеме базы данных с помощью миграции 

``` bash
$ php bin/console doctrine:migrations:diff

$ php bin/console doctrine:migrations:migrate
```

## Журнал изменений ![Changelog](https://img.shields.io/badge/changelog-yellow)

О том, что изменилось за последнее время, обратитесь к [CHANGELOG](CHANGELOG.md) за дополнительной информацией.

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

