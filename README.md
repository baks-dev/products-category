# BaksDev Product Category

![Version](https://img.shields.io/badge/version-7.0.9-blue) ![php 8.2+](https://img.shields.io/badge/php-min%208.1-red.svg)

Модуль категорий продукции

## Установка

``` bash
$ composer require baks-dev/products-category
```

## Дополнительно

Добавить директорию и установить права для загрузки обложек категорий:

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

Изменения в схеме базы данных с помощью миграции

``` bash
$ php bin/console doctrine:migrations:diff

$ php bin/console doctrine:migrations:migrate
```

Тесты

``` bash
$ php bin/phpunit --group=products-category
```


## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.
