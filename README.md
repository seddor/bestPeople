Best people of internet
=========
Проект написан на symfony2.<br>
Для запуска проекта:<br>
1)обновить зависимости проекта через composer<br>
> composer update

 использовать все дефолтные настройки БД, кроме:<br>

>database_name: bestPeople<br>
 >database_user: bestApp<br>

 создать соответсуюшего пользователя и БД.<br>

 Для создание таблиц БД: <br>
> php app/console doctrine:schema:update --force <br>

2)запустить пхп-сервер в директории проекта<br>

>php app/console server:run