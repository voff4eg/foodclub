<?
$MESS ['MAIN_ADMIN_GROUP_NAME'] = "Administrators";
$MESS ['MAIN_ADMIN_GROUP_DESC'] = "Full access.";
$MESS ['MAIN_EVERYONE_GROUP_NAME'] = "Anonymous";
$MESS ['MAIN_EVERYONE_GROUP_DESC'] = "Applied to everyone by default.";
$MESS ['MAIN_DEFAULT_SITE_NAME'] = "Default site";
$MESS ['MAIN_DEFAULT_LANGUAGE_NAME'] = "English";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_DATE'] = "MM/DD/YYYY";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_DATETIME'] = "MM/DD/YYYY HH:MI:SS";
$MESS ['MAIN_DEFAULT_LANGUAGE_FORMAT_CHARSET'] = "iso-8859-1";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_DATE'] = "MM/DD/YYYY";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_DATETIME'] = "MM/DD/YYYY HH:MI:SS";
$MESS ['MAIN_DEFAULT_SITE_FORMAT_CHARSET'] = "iso-8859-1";
$MESS ['MAIN_MODULE_NAME'] = "Main module";
$MESS ['MAIN_MODULE_DESC'] = "The product kernel ";
$MESS ['MAIN_INSTALL_DB_ERROR'] = "Cannot connect to the database. Please check the parameters.";
$MESS ['MAIN_NEW_USER_TYPE_NAME'] = "New user was registered";
$MESS ['MAIN_NEW_USER_TYPE_DESC'] = "
#USER_ID# - User ID
#LOGIN# - Login
#EMAIL# - EMail
#NAME# - Name
#LAST_NAME# - Last Name
#USER_IP# - User IP
#USER_HOST# - User Host
";
$MESS ['MAIN_USER_INFO_TYPE_NAME'] = "Account Information";
$MESS ['MAIN_USER_INFO_TYPE_DESC'] = "
#USER_ID# - User ID
#STATUS# - Account status
#MESSAGE# - Message for user
#LOGIN# - Login
#CHECKWORD# - Check string for password change
#NAME# - Name
#LAST_NAME# - Last Name
#EMAIL# - User E-Mail
";
$MESS ['MAIN_NEW_USER_CONFIRM_TYPE_NAME'] = "New user registration confirmation";
$MESS ['MAIN_NEW_USER_CONFIRM_TYPE_DESC'] = "
#USER_ID# - User ID
#LOGIN# - Login
#EMAIL# - E-mail
#NAME# - First name
#LAST_NAME# - Last name
#USER_IP# - User IP
#USER_HOST# - User host
#CONFIRM_CODE# - Confirmation code
";
$MESS ['MAIN_NEW_USER_EVENT_NAME'] = "#SITE_NAME#: New user has been registered on the site";
$MESS ['MAIN_NEW_USER_EVENT_DESC'] = "Informational message from #SITE_NAME#
---------------------------------------

New user has been successfully registered on the site #SERVER_NAME#.

User details:
User ID: #USER_ID#

Name: #NAME#
Last Name: #LAST_NAME#
User's E-Mail: #EMAIL#

Login: #LOGIN#

Automatically generated message.";
$MESS ['MAIN_USER_INFO_EVENT_NAME'] = "#SITE_NAME#: Registration info";
$MESS ['MAIN_USER_INFO_EVENT_DESC'] = "Informational message from #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Your registration info:

User ID: #USER_ID#
Account status: #STATUS#
Login: #LOGIN#

To change your password please visit the link below:
http://#SERVER_NAME#/bitrix/admin/index.php?change_password=yes&lang=en&USER_CHECKWORD=#CHECKWORD#

Automatically generated message.";
$MESS ['MAIN_NEW_USER_CONFIRM_EVENT_NAME'] = "#SITE_NAME#: New user registration confirmation";
$MESS ['MAIN_NEW_USER_CONFIRM_EVENT_DESC'] = "Greetings from #SITE_NAME#!
------------------------------------------

Hello,

you have received this message because you (or someone else) used your e-mail to register at #SERVER_NAME#.

Your registration confirmation code: #CONFIRM_CODE#

Please use the link below to verify and activate your registration:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Alternatively, open this link in your browser and enter the code manually:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Attention! Your account will not be activated until you confirm registration.

---------------------------------------------------------------------

Automatically generated message.";
?>