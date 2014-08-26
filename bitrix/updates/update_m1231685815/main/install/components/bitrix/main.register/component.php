<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

//echo "<pre>"; print_r($arParams); echo "</pre>";

// apply default param values
$arDefaultValues = array(
	"SHOW_FIELDS" => array(),
	"REQUIRED_FIELDS" => array(),
	"AUTH" => "Y",
	"USE_BACKURL" => "Y",
	"SUCCESS_PAGE" => "",
	//"CACHE_TYPE" => "A",
	//"CACHE_TIME" => "3600",
);

foreach ($arDefaultValues as $key => $value)
{
	if (!is_set($arParams, $key)) $arParams[$key] = $value;
}

// if user registration blocked - return auth form
if (COption::GetOptionString("main", "new_user_registration", "N") == "N")
{
	$APPLICATION->AuthForm(array());
}

// apply core fields to user defined
$arDefaultFields = array(
	"LOGIN",
	"PASSWORD",
	"PASSWORD_CONFIRM",
	"EMAIL",
);

$arResult["SHOW_FIELDS"] = array_merge($arDefaultFields, $arParams["SHOW_FIELDS"]);
$arResult["REQUIRED_FIELDS"] = array_merge($arDefaultFields, $arParams["REQUIRED_FIELDS"]);

// use captcha?
$arResult["USE_CAPTCHA"] = COption::GetOptionString("main", "captcha_registration", "N") == "Y" ? "Y" : "N";
// start values
$arResult["VALUES"] = array();
$arResult["VALUES"]["PERSONAL_WWW"] = "http://";
$arResult["VALUES"]["WORK_WWW"] = "http://";

$arResult["ERRORS"] = array();

// register user
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_REQUEST["register_submit_button"]) && !$USER->IsAuthorized())
{
	// check emptiness of required fields
	foreach ($arResult["SHOW_FIELDS"] as $key)
	{
		if ($key != "PERSONAL_PHOTO" && $key != "WORK_LOGO")
		{
			$arResult["VALUES"][$key] = $_REQUEST["REGISTER"][$key];
			if (in_array($key, $arResult["REQUIRED_FIELDS"]) && strlen($arResult["VALUES"][$key]) <= 0)
			{
				$arResult["ERRORS"][$key] = GetMessage("REGISTER_FIELD_REQUIRED");
			}
		}
		else
		{
			$_FILES["REGISTER_FILES_".$key]["MODULE_ID"] = "main";
			$arResult["VALUES"][$key] = $_FILES["REGISTER_FILES_".$key];
			if (in_array($key, $arResult["REQUIRED_FIELDS"]) && !is_uploaded_file($_FILES["REGISTER_FILES_".$key]["tmp_name"]))
			{
				$arResult["ERRORS"][$key] = GetMessage("REGISTER_FIELD_REQUIRED");
			}
		}
	}
	
	// check captcha
	if ($arResult["USE_CAPTCHA"] == "Y")
	{
		if (!$APPLICATION->CaptchaCheckCode($_REQUEST["captcha_word"], $_REQUEST["captcha_sid"]))
		{
			$arResult["ERRORS"][] = GetMessage("REGISTER_WRONG_CAPTCHA");
		}	
	}
	
	// if there;s no any errors - create user
	if (count($arResult["ERRORS"]) <= 0)
	{
		$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
		if($def_group != "")
			$arResult['VALUES']["GROUP_ID"] = explode(",", $def_group);

		$bOk = true;

		$GLOBALS["USER_FIELD_MANAGER"]->EditFormAddFields("USER", $arResult["VALUES"]);

		$events = GetModuleEvents("main", "OnBeforeUserRegister");
		while($arEvent = $events->Fetch())
		{
			if(ExecuteModuleEvent($arEvent, &$arResult['VALUES']) === false)
			{
				if($err = $APPLICATION->GetException())
					$arResult['ERRORS'][] = $err->GetString();

				$bOk = false;
				break;
			}
		}
		
		if ($bOk)
		{
			$user = new CUser();
			$ID = $user->Add($arResult["VALUES"]);
		}
		
		if (intval($ID) > 0)
		{
			// set user group
			//$sGroups = COption::GetOptionString("main", "new_user_registration_def_group", "");
			//CUser::SetUserGroup($ID, explode(",", $sGroups));
		
			// authorize user
			if ($arParams["AUTH"] == "Y")
			{
				if (!$arAuthResult = $USER->Login($arResult["VALUES"]["LOGIN"], $arResult["VALUES"]["PASSWORD"]))
				{
					$arResult["ERRORS"][] = $arAuthResult;
				}
			}
			else
			{
				$register_done = true;
			}
			
			$arResult['VALUES']["USER_ID"] = $ID;

			$event = new CEvent;
			$event->Send("NEW_USER", SITE_ID, $arResult['VALUES']);
		}
		else
		{
			$arResult["ERRORS"][] = $user->LAST_ERROR;
		}
		
		$events = GetModuleEvents("main", "OnAfterUserRegister");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEvent($arEvent, &$arResult['VALUES']);
	}
}

// if user is registered - redirect him to backurl or to success_page; currently added users too
if ($USER->IsAuthorized() || $register_done)
{
	if ($arParams["USE_BACKURL"] == "Y" && strlen($_REQUEST["backurl"]) > 0) 
	{
		LocalRedirect($_REQUEST["backurl"]);
	}
	elseif (strlen($arParams["SUCCESS_PAGE"])) 
	{
		LocalRedirect($arParams["SUCCESS_PAGE"]);
	}
	//else $APPLICATION->AuthForm(array());
	//die();
}
else
{
	$arResult["VALUES"] = htmlspecialcharsEx($arResult["VALUES"]);
}

// redefine required list - for better use in template
$arResult["REQUIRED_FIELDS_FLAGS"] = array();
foreach ($arResult["REQUIRED_FIELDS"] as $field)
{
	$arResult["REQUIRED_FIELDS_FLAGS"][$field] = "Y";
}

// check backurl existance
$arResult["BACKURL"] = htmlspecialchars($_REQUEST["backurl"]);

// get countries list
if (in_array("PERSONAL_COUNTRY", $arResult["SHOW_FIELDS"]) || in_array("WORK_COUNTRY", $arResult["SHOW_FIELDS"])) $arResult["COUNTRIES"] = GetCountryArray();
// get date format
if (in_array("PERSONAL_BIRTHDAY", $arResult["SHOW_FIELDS"])) $arResult["DATE_FORMAT"] = CLang::GetDateFormat("SHORT");

// ********************* User properties ***************************************************
$arResult["USER_PROPERTIES"] = array("SHOW" => "N");
$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
if (is_array($arUserFields) && count($arUserFields) > 0)
{
	if (!is_array($arParams["USER_PROPERTY"]))
		$arParams["USER_PROPERTY"] = array($arParams["USER_PROPERTY"]);
	foreach ($arUserFields as $FIELD_NAME => $arUserField)
	{
		if (!in_array($FIELD_NAME, $arParams["USER_PROPERTY"]) && $arUserField["MANDATORY"] != "Y")
			continue;
		$arUserField["EDIT_FORM_LABEL"] = strLen($arUserField["EDIT_FORM_LABEL"]) > 0 ? $arUserField["EDIT_FORM_LABEL"] : $arUserField["FIELD_NAME"];
		$arUserField["EDIT_FORM_LABEL"] = htmlspecialcharsEx($arUserField["EDIT_FORM_LABEL"]);
		$arUserField["~EDIT_FORM_LABEL"] = $arUserField["EDIT_FORM_LABEL"];
		$arResult["USER_PROPERTIES"]["DATA"][$FIELD_NAME] = $arUserField;
	}
}
if (!empty($arResult["USER_PROPERTIES"]["DATA"]))
{
	$arResult["USER_PROPERTIES"]["SHOW"] = "Y";
	$arResult["bVarsFromForm"] = (count($arResult['ERRORS']) <= 0) ? false : true;
}
// ******************** /User properties ***************************************************

// initialize captcha
if ($arResult["USE_CAPTCHA"] == "Y")
{
	$arResult["CAPTCHA_CODE"] = htmlspecialchars($APPLICATION->CaptchaGetCode());
}

// set title
if ($arParams["SET_TITLE"] == "Y") $APPLICATION->SetTitle(GetMessage("REGISTER_DEFAULT_TITLE"));

// all done
$this->IncludeComponentTemplate();
?>