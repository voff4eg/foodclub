<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->AddHeadScript("/js/file-upload/js/vendor/jquery.ui.widget.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.iframe-transport.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload-fp.js");
$APPLICATION->AddHeadScript("/js/file-upload/js/jquery.fileupload-ui.js");
$APPLICATION->AddHeadScript("/bitrix/components/custom/profile_avatar/templates/.default/jquery.fileupload-ui.js");
$APPLICATION->AddHeadString('<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/cors/jquery.xdr-transport.js"></script><![endif]-->');
//$APPLICATION->SetAdditionalCSS(__DIR__."/additional.css");
//$APPLICATION->SetTemplateCSS(__DIR__."/additional.css");
//echo __DIR__."additional.css";
//$APPLICATION->AddHeadScript("/js/add-script.js");

//echo $this->GetRelativePath();
//echo $this->GetTemplate();
$arResult["DEFAULT_BACKGROUND"] = "/images/default/background_default.jpg";

if(!isset($arParams["USER_ID"])){
	if(intval(CUser::GetID())){
		$rsUser = CUser::GetByID(CUser::GetID());
		if($arUser = $rsUser->Fetch()){
			if(intval($arUser["UF_BACKGROUND"])){
				$arResult["BACKGROUND"] = CFile::GetPath($arUser["UF_BACKGROUND"]);
			}			
		}
	}
}else{
	if(intval($arParams["USER_ID"])){
		$rsUser = CUser::GetByID(intval($arParams["USER_ID"]));
		if($arUser = $rsUser->Fetch()){
			if(intval($arUser["UF_BACKGROUND"])){
				$arResult["BACKGROUND"] = CFile::GetPath($arUser["UF_BACKGROUND"]);
			}			
		}
	}
}

$this->IncludeComponentTemplate();
$template = & $this->GetTemplate();
$templateFile = $template->GetFile();
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="'.str_replace("template.php", "", $templateFile).'additional.css">');
$APPLICATION->AddHeadScript(str_replace("template.php", "", $templateFile).'add-script.js');
?>