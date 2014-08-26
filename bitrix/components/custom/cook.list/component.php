<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
echo"!!";die;
CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
if($arParams["NEWS_COUNT"]<=0)
	$arParams["NEWS_COUNT"] = 20;

$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";

$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
if(!is_array($arParams["GROUP_PERMISSIONS"]))
	$arParams["GROUP_PERMISSIONS"] = array(1);

$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
{
	$arUserGroupArray = $GLOBALS["USER"]->GetUserGroupArray();
	foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
	{
		if(in_array($PERM, $arUserGroupArray))
		{
			$bUSER_HAVE_ACCESS = true;
			break;
		}
	}
}
if($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arrFilter)))
{
	/*if(!CModule::IncludeModule(""))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}*/
        
        $by_sort = "date_register";
        
        $order = "desc";
        
        //WHERE
        $arFilter = array (
                "GROUPS_ID" => array(3),
                "ACTIVE" => "Y",
        );

        $arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;
		
        $arResult["USERS"] = array();
	$rsElement = CUser::GetList($by_sort, $order, $arFilter);
        while($obElement = $rsElement->GetNextElement())
        {
                $arItem = $obElement->GetFields();

                if(array_key_exists("PREVIEW_PICTURE", $arItem))
                        $arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);
                if(array_key_exists("DETAIL_PICTURE", $arItem))
                        $arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);

                $arItem["PROPERTIES"] = $obElement->GetProperties();

                $arResult["ITEMS"][]=$arItem;
        }
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		ShowError("Ошибка");
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}

if(isset($arResult["ID"]))
{

	$arTitleOptions = null;
	if($USER->IsAuthorized())
	{
		if(
			$APPLICATION->GetShowIncludeAreas()
			|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N")
			|| $arParams["SET_TITLE"]
		)
		{
			if(CModule::IncludeModule("iblock"))
			{
				$arButtons = CIBlock::GetPanelButtons(
					$arResult["ID"],
					0,
					$arParams["PARENT_SECTION"],
					array("SECTION_BUTTONS"=>false)
				);

				if($APPLICATION->GetShowIncludeAreas())
					$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

				if(
					is_array($arButtons["intranet"])
					&& is_object($GLOBALS["INTRANET_TOOLBAR"])
					&& $arParams["INTRANET_TOOLBAR"]!=="N"
				)
				{
					foreach($arButtons["intranet"] as $arButton)
						$GLOBALS["INTRANET_TOOLBAR"]->AddButton($arButton);
				}

				if($arParams["SET_TITLE"])
				{
					$arTitleOptions = array(
						'ADMIN_EDIT_LINK' => /*$arButtons["submenu"]["edit_iblock"]["ACTION"]*/,
						'PUBLIC_EDIT_LINK' => "",
						'COMPONENT_NAME' => $this->GetName(),
					);
				}
			}
		}
	}

	$this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);

	if($arParams["SET_TITLE"])
	{
		$APPLICATION->SetTitle($arResult["NAME"], $arTitleOptions);
	}
}

?>