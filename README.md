# Работа с Docker

Используйте [Данный](https://github.com/AlexandrFiner/docker-compose-lamp) образ Docker

> PHP 8.0.10

Скопирауйте настройки окружения

``cp sample.env .env``

Укажите путь до проекта Laravel 

``DOCUMENT_ROOT=``

# Работа с Laravel

Скопирауйте настройки окружения

`` cp .env.example .env ``

Сгенерируйте ключ шифрования сессий и кук

`` php artisan key:generate ``

Укажите данные для подключения к базе данных

````
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
````

Произведите миграцию и заполните тестовые данные

`` php artisan migrate --seed ``

# Postman

https://www.postman.com/milliards/workspace/testwork-mello

# Тесты

Для запуска тестов используйте команду

`` php artisan test ``
