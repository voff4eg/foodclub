<?
$MESS ['MAIN_ADMIN_GROUP_NAME'] = "Администраторы";
$MESS ['MAIN_ADMIN_GROUP_DESC'] = "Полный доступ к управлению сайтом.";
$MESS ['MAIN_EVERYONE_GROUP_NAME'] = "Неавторизованные пользователи";
$MESS ['MAIN_EVERYONE_GROUP_DESC'] = "Все неавторизованные на сайте пользователи.";
$MESS ['MAIN_DEFAULT_SITE_NAME'] = "Сайт по умолчанию";
$MESS ['MAIN_DEFAULT_LANGUAGE_NAME'] = "Russian";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_DATE'] = "DD.MM.YYYY";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_DATETIME'] = "DD.MM.YYYY HH:MI:SS";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_CHARSET'] = "windows-1251";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_DATE'] = "DD.MM.YYYY";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_DATETIME'] = "DD.MM.YYYY HH:MI:SS";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_CHARSET'] = "windows-1251";
$MESS ['MAIN_MODULE_NAME'] = "Главный модуль";
$MESS ['MAIN_MODULE_DESC'] = "Ядро системы";
$MESS ['MAIN_INSTALL_DB_ERROR'] = "Не могу соединиться с базой данных. Проверьте правильность введенных параметров";
$MESS ['MAIN_NEW_USER_TYPE_NAME'] = "Зарегистрировался новый пользователь";
$MESS ['MAIN_NEW_USER_TYPE_DESC'] = "
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
";
$MESS ['MAIN_USER_INFO_TYPE_NAME'] = "Информация о пользователе";
$MESS ['MAIN_USER_INFO_TYPE_DESC'] = "
#USER_ID# - ID пользователя
#STATUS# - Статус логина
#MESSAGE# - Сообщение пользователю
#LOGIN# - Логин
#CHECKWORD# - Контрольная строка для смены пароля
#NAME# - Имя
#LAST_NAME# - Фамилия
#EMAIL# - E-Mail пользователя
";
$MESS ['MAIN_NEW_USER_CONFIRM_TYPE_NAME'] = "Подтверждение регистрации нового пользователя";
$MESS ['MAIN_NEW_USER_CONFIRM_TYPE_DESC'] = "
#USER_ID# - ID пользователя
#LOGIN# - Логин
#EMAIL# - EMail
#NAME# - Имя
#LAST_NAME# - Фамилия
#USER_IP# - IP пользователя
#USER_HOST# - Хост пользователя
#CONFIRM_CODE# - Код подтверждения
";
$MESS ['MAIN_NEW_USER_EVENT_NAME'] = "#SITE_NAME#: Зарегистрировался новый пользователь";
$MESS ['MAIN_NEW_USER_EVENT_DESC'] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------

На сайте #SERVER_NAME# успешно зарегистрирован новый пользователь.

Данные пользователя:
ID пользователя: #USER_ID#

Имя: #NAME#
Фамилия: #LAST_NAME#
E-Mail: #EMAIL#

Login: #LOGIN#

Письмо сгенерировано автоматически.";
$MESS ['MAIN_USER_INFO_EVENT_NAME'] = "#SITE_NAME#: Регистрационная информация";
$MESS ['MAIN_USER_INFO_EVENT_DESC'] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------
#NAME# #LAST_NAME#,

#MESSAGE#

Ваша регистрационная информация:

ID пользователя: #USER_ID#
Статус бюджета: #STATUS#
Login: #LOGIN#

Для смены пароля перейдите по следующей ссылке:
http://#SERVER_NAME#/bitrix/admin/index.php?change_password=yes&lang=ru&USER_CHECKWORD=#CHECKWORD#

Сообщение сгенерировано автоматически.";
$MESS ['MAIN_NEW_USER_CONFIRM_EVENT_NAME'] = "#SITE_NAME#: Подтверждение регистрации нового пользователя";
$MESS ['MAIN_NEW_USER_CONFIRM_EVENT_DESC'] = "Информационное сообщение сайта #SITE_NAME#
------------------------------------------

Здравствуйте,

Вы получили это сообщение, так как ваш адрес был использован при регистрации нового пользователя на сервере #SERVER_NAME#.

Ваш код для подтверждения регистрации: #CONFIRM_CODE#

Для подтверждения регистрации перейдите по следующей ссылке:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Вы также можете ввести код для подтверждения регистрации на странице:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Внимание! Ваш бюджет не будет активным, пока вы не подтвердите свою регистрацию.

---------------------------------------------------------------------

Сообщение сгенерировано автоматически.";
?>