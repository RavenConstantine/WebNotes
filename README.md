# WebNotes
Скрины
![screenshot1](img/1.jpg) 
![screenshot2](img/2.jpg) 
![screenshot3](img/3.jpg) 
![screenshot4](img/4.jpg) 
![screenshot5](img/5.jpg) 
![screenshot6](img/6.jpg) 

## Установка
Меняйте в .env

APP_NAME=[Название_сайта]

APP_DEBUG=[Выводить_ошибки?]

APP_URL=[Адрес_сайта]


Копируйте базы данных

Меняйте в .env

DB_HOST=[Название_хоста_сервера]

DB_DATABASE=[Название_базы_данных_на_сервере]

DB_USERNAME=[Логин_пользователя]

DB_PASSWORD=[Пароль_пользователя]


Меняйте в public/.htaccess

Удалите -MultiViews если сервер его не поддерживает

Вверх добавте "DyrectoryIndex public/index"

Замените "RewriteRule ^ index.php [L]" на "RewriteRule ^ public/index.php [L]"

Перенести этот файл в корневой каталог (notes-raven)

Подключится к серверу

Скопировать туда (notes-raven)