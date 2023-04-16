Тестовое задание.

Для развертывания проекта после клонирования необходимо заполнить переменные окружения для соединения с БД и почтовым сервером, плюс выполнить следующие шаги для заполнения базы:
```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console seed:load
```

Так же необходимо указать в .env файле значение доступа к вашему smtp-серверу для почтовых рассылок в виде:
```
MAILER_DSN=smtp://user:pass@smtp.example.com:port
```