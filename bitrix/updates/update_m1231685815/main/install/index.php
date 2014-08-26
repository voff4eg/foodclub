<?
global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class main extends CModule
{
	var $MODULE_ID = "main";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function main()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/install/index.php"));
		include($path."/classes/general/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}
		else
		{
			$this->MODULE_VERSION = SM_VERSION;
			$this->MODULE_VERSION_DATE = SM_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("MAIN_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("MAIN_MODULE_DESC");
	}

	function InstallDB()
	{
		global $DB, $DBType, $DBHost, $DBLogin, $DBPassword, $DBName, $APPLICATION;

		if (!is_object($APPLICATION))
			$APPLICATION = new CMain;

		$DB = new CDatabase;
		$DB->DebugToFile = false;
		$DB->debug = true;

		if (!defined("DBPersistent"))
			define("DBPersistent", false);

		if (!$DB->Connect($DBHost, $DBName, $DBLogin, $DBPassword))
		{
			$APPLICATION->ThrowException(GetMessage("MAIN_INSTALL_DB_ERROR"));
			return false;
		}

		$result = $DB->Query("SELECT * FROM b_module WHERE ID='main'", true);
		$success = $result && $result->Fetch();
		if ($success)
			return true;

		if ($DBType == "mysql" && defined("MYSQL_TABLE_TYPE") && strlen(MYSQL_TABLE_TYPE)>0)
			$DB->Query("SET table_type = '".MYSQL_TABLE_TYPE."'", true);

		$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/".$DBType."/install.sql");
		if ($errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		$group = new CGroup;
		$arGroups = Array(
			Array(
				"ID" => 1,
				"ACTIVE" => "Y",
				"C_SORT" => 1,
				"NAME" => GetMessage("MAIN_ADMIN_GROUP_NAME"),
				"ANONYMOUS" => "N",
				"DESCRIPTION" => GetMessage("MAIN_ADMIN_GROUP_DESC")
			),
			Array(
				"ID" => 2,
				"ACTIVE" => "Y",
				"C_SORT" => 2,
				"NAME" => GetMessage("MAIN_EVERYONE_GROUP_NAME"),
				"ANONYMOUS" => "Y",
				"DESCRIPTION" => GetMessage("MAIN_EVERYONE_GROUP_DESC")
			)
		);

		foreach ($arGroups as $arGroup)
		{
			$rsGroup = CGroup::GetByID($arGroup["ID"]);
			if ($rsGroup->Fetch())
				continue;

			unset($arGroup["ID"]);

			$success = (bool)$group->Add($arGroup);
			if (!$success)
			{
				$APPLICATION->ThrowException($group->LAST_ERROR);
				return false;
			}
		}

		$arLanguages = Array(
			Array(
				"LID" => LANGUAGE_ID,
				"ACTIVE" => "Y",
				"SORT" => 1,
				"DEF" => "Y",
				"NAME" => GetMessage("MAIN_DEFAULT_LANGUAGE_NAME"),
				"FORMAT_DATE" => GetMessage("MAIN_DEFAULT_LANGUAGE_FORMAT_DATE"),
				"FORMAT_DATETIME" => GetMessage("MAIN_DEFAULT_LANGUAGE_FORMAT_DATETIME"),
				"CHARSET" => (defined("BX_UTF") ? "UTF-8" : GetMessage("MAIN_DEFAULT_LANGUAGE_FORMAT_CHARSET"))
			)
		);

		if (LANGUAGE_ID == "ru")
			$arLanguages[] = Array(
				"LID" => "en",
				"ACTIVE" => "Y",
				"SORT" => 2,
				"DEF" => "N",
				"NAME" => "English",
				"FORMAT_DATE" => "MM/DD/YYYY",
				"FORMAT_DATETIME" => "MM/DD/YYYY HH:MI:SS",
				"CHARSET" => (defined("BX_UTF") ? "UTF-8" : "windows-1251")
			);

		$lang = new CLanguage;
		foreach ($arLanguages as $arLanguage)
		{
			$rsLang = CLanguage::GetByID($arLanguage["LID"]);
			if ($rsLang->Fetch())
				continue;

			$success = (bool)$lang->Add($arLanguage);
			if (!$success)
			{
				$APPLICATION->ThrowException($lang->LAST_ERROR);
				return false;
			}
		}

		$arSite = Array(
			"LID" => "s1",
			"ACTIVE" => "Y",
			"SORT" => 1,
			"DEF" => "Y",
			"NAME" => GetMessage("MAIN_DEFAULT_SITE_NAME"),
			"DIR" => "/",
			"FORMAT_DATE" => GetMessage("MAIN_DEFAULT_SITE_FORMAT_DATE"),
			"FORMAT_DATETIME" => GetMessage("MAIN_DEFAULT_SITE_FORMAT_DATETIME"),
			"CHARSET" =>  (defined("BX_UTF") ? "UTF-8" : GetMessage("MAIN_DEFAULT_SITE_FORMAT_CHARSET")),
			"LANGUAGE_ID" => LANGUAGE_ID,
		);

		$rsSites = CSite::GetByID($arSite["LID"]);
		if (!$rsSites->Fetch())
		{
			$site = new CSite;
			$success = (bool)$site->Add($arSite);
			if (!$success)
			{
				$APPLICATION->ThrowException($site->LAST_ERROR);
				return false;
			}
		}

		RegisterModule("main");
		RegisterModuleDependences('iblock', 'OnIBlockPropertyBuildList', 'main', 'CIBlockPropertyUserID', 'GetUserTypeDescription', 100, '/modules/main/tools/prop_userid.php');
		RegisterModuleDependences('main','OnUserDelete','main', 'CFavorites','OnUserDelete');
		RegisterModuleDependences('main','OnLanguageDelete','main', 'CFavorites','OnLanguageDelete');
		RegisterModuleDependences('main','OnUserDelete','main', 'CUserOptions','OnUserDelete');
		RegisterModuleDependences('main','OnChangeFile','main', 'CMain','OnChangeFileComponent');
		RegisterModuleDependences('main','OnUserTypeRightsCheck','main', 'CUser','UserTypeRightsCheck');

		COption::SetOptionString("main", "PARAM_MAX_SITES", "2");
		COption::SetOptionString("main", "distributive6", "Y");
		COption::SetOptionString("main", "new_license7_sign", "Y");
		COption::SetOptionString("main", "GROUP_DEFAULT_TASK", "1");

		if (LANGUAGE_ID == "ru")
			COption::SetOptionString("main", "vendor", "1c_bitrix");
		else
			COption::SetOptionString("main", "vendor", "bitrix");

		COption::SetOptionString("main", "admin_lid", LANGUAGE_ID);
		COption::SetOptionString("main", "update_site", "www.bitrixsoft.com");

		CAgent::AddAgent("CEvent::CleanUpAgent();","main", "Y", 86400);
		CAgent::AddAgent("CCaptchaAgent::DeleteOldCaptcha(3600);","main", "N", 3600);

		return true;
	}

	function UnInstallDB()
	{
		global $DBType, $DBHost, $DBLogin, $DBPassword, $DBName, $APPLICATION;

		if (!is_object($APPLICATION))
			$APPLICATION = new CMain;

		$DB = new CDatabase;
		$DB->DebugToFile = false;
		//$DB->debug = true;
		define("DBPersistent", false);

		if (!$DB->Connect($DBHost, $DBName, $DBLogin, $DBPassword))
		{
			$APPLICATION->ThrowException(GetMessage("MAIN_INSTALL_DB_ERROR"));
			return false;
		}

		$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/".$DBType."/uninstall.sql");
		if ($errors !== false)
		{
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

		return true;
	}

	function InstallEvents()
	{
		$arEventTypes = Array();
		$arEventTypes[] = Array(
			"LID" => (LANGUAGE_ID == "ru" ? "ru" : "en"),
			"EVENT_NAME" => "NEW_USER",
			"NAME" => GetMessage("MAIN_NEW_USER_TYPE_NAME"),
			"DESCRIPTION" => GetMessage("MAIN_NEW_USER_TYPE_DESC"),
			"SORT" => 1
		);

		$arEventTypes[] = Array(
			"LID" => (LANGUAGE_ID == "ru" ? "ru" : "en"),
			"EVENT_NAME" => "USER_INFO",
			"NAME" => GetMessage("MAIN_USER_INFO_TYPE_NAME"),
			"DESCRIPTION" => GetMessage("MAIN_USER_INFO_TYPE_DESC"),
			"SORT" => 2
		);

		$arEventTypes[] = Array(
			"LID" => (LANGUAGE_ID == "ru" ? "ru" : "en"),
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"NAME" => GetMessage("MAIN_NEW_USER_CONFIRM_TYPE_NAME"),
			"DESCRIPTION" => GetMessage("MAIN_NEW_USER_CONFIRM_TYPE_DESC"),
			"SORT" => 3
		);

		$type = new CEventType;
		foreach ($arEventTypes as $arEventType)
			$type->Add($arEventType);

		$arMessages = Array();
		$arMessages[] = Array(
			"EVENT_NAME" => "NEW_USER",
			"LID" => "s1",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#DEFAULT_EMAIL_FROM#",
			"SUBJECT" => GetMessage("MAIN_NEW_USER_EVENT_NAME"),
			"MESSAGE" => GetMessage("MAIN_NEW_USER_EVENT_DESC")
		);

		$arMessages[] = Array(
			"EVENT_NAME" => "USER_INFO",
			"LID" => "s1",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",
			"SUBJECT" => GetMessage("MAIN_USER_INFO_EVENT_NAME"),
			"MESSAGE" => GetMessage("MAIN_USER_INFO_EVENT_DESC")
		);

		$arMessages[] = Array(
			"EVENT_NAME" => "NEW_USER_CONFIRM",
			"LID" => "s1",
			"EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
			"EMAIL_TO" => "#EMAIL#",
			"SUBJECT" => GetMessage("MAIN_NEW_USER_CONFIRM_EVENT_NAME"),
			"MESSAGE" => GetMessage("MAIN_NEW_USER_CONFIRM_EVENT_DESC")
		);

		$message = new CEventMessage;
		foreach ($arMessages as $arMessage)
			$message->Add($arMessage);

		return true;
	}

	function UnInstallEvents()
	{
		return true;
	}

	function InstallFiles()
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/bitrix", $_SERVER["DOCUMENT_ROOT"]."/bitrix", true, true);
		CopyDirFiles($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/install/admin", $_SERVER['DOCUMENT_ROOT']."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/tools", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/images", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/components/bitrix", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix", true, true);

		return true;
	}

	function UnInstallFiles()
	{
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/install/bitrix/", $_SERVER["DOCUMENT_ROOT"]."/bitrix");
		DeleteDirFilesEx("/bitrix/js/");
		DeleteDirFilesEx("/bitrix/admin/");
		DeleteDirFilesEx("/bitrix/components/bitrix");
		DeleteDirFilesEx("/bitrix/templates/");
		DeleteDirFilesEx("/bitrix/tools/");
		DeleteDirFilesEx("/bitrix/themes/");
		DeleteDirFilesEx("/bitrix/images/");

		return true;
	}

	function DoInstall()
	{

	}

	function DoUninstall()
	{

	}
}
?>