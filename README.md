Best people of internet
=========
Проект написан на symfony2.<br>
Для запуска проекта:<br>
1. Обновить зависимости проекта через composer<br>
> composer update

 использовать все дефолтные настройки БД, кроме:<br>

>database_name: bestPeople<br>
 >database_user: bestApp<br>

2. Создать соответсуюшего пользователя и БД.<br>
 Для создание таблиц в БД: <br>
> php app/console doctrine:schema:update --force <br>

3. Запустить пхп-сервер в директории проекта<br>

>php app/console server:run