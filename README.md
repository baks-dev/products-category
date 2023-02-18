# BaksDev Product Category

![Version](https://img.shields.io/badge/version-6.2-blue) ![php 8.1+](https://img.shields.io/badge/php-min%208.1-red.svg)

Модуль категорий продукции

## Установка

``` bash
$ composer require baks-dev/products-category
```

## Дополнительно

Добавить диреткорию и установить права для загрзуки обложек категорий:

``` bash
$ sudo mkdir <path_to_project>/public/upload/product_category_cover
$ sudo sudo chmod 773 <path_to_project>/public/upload/product_category_cover
``` 

Для сжатия и загрузки файлов на ["Модуль CDN файловых ресурсов"](https://github.com/baks-dev/files-cdn), необходимо запустить очередь из сообщений `async_files_resources`

``` bash
$ php bin/console messenger:consume async_files_resources --time-limit=3600
``` 

Установка файловых ресурсов в публичную директорию (javascript, css, image ...):

``` bash
$ php bin/console baks:assets:install
```

Роли администартора с помощью Fixtures

``` bash
$ php bin/console doctrine:fixtures:load --append
```

Изменения в схеме базы данных с помощью миграции

``` bash
$ php bin/console doctrine:migrations:diff

$ php bin/console doctrine:migrations:migrate
```

## Журнал изменений ![Changelog](https://img.shields.io/badge/changelog-yellow)

О том, что изменилось за последнее время, обратитесь к [CHANGELOG](CHANGELOG.md) за дополнительной информацией.

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

