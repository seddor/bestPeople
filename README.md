Best people of internet
=========
Проект написан на symfony2.
Для запуска проекта:
1)обновить зависимости проекта через composer
 composer update
 использовать все дефолтные настройки БД, кроме:
 database_name: bestPeople
 database_user: bestApp
 создать соответсуюшего пользователя и БД.
2)запустить пхп-сервер в директории проекта
php app/console server:run