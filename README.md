# Описание проекта

//description here

# Установка

Установить зависимости:

    composer install

Создаем символьную ссылку на storage

    php artisan storage:link

#### Далее создайте .env файл и подключитесь к своей базе данных (.env.example как тут)

Выполнить миграцию:

    php artisan migrate
Запускем локальный веб-сервер:

    php artisan serve

---

#### Открываем вторую консоль

Установить зависимости фронтенд приложения:

    npm i

Запускаем проект:

    npm run dev
