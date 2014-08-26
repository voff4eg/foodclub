<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->AddHeadString('<link href="/bitrix/components/custom/store.banner.vertical/templates/.default/store.css"  type="text/css" rel="stylesheet" />',true);

if (CModule::IncludeModule("advertising")){
	$banner1 = CAdvBanner::Show("store_vertical");
	$banner2 = CAdvBanner::Show("store_vertical");
	$banner3 = CAdvBanner::Show("store_vertical");
	$arResult["banner"] = $banner1.$banner2.$banner3;
	$this->IncludeComponentTemplate();
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
						'ADMIN_EDIT_LINK' => $arButtons["submenu"]["edit_iblock"]["ACTION"],
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

	if($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["NAME"]))
	{
		if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
			$APPLICATION->AddChainItem(
				$arResult["NAME"]
				,strlen($arParams["IBLOCK_URL"]) > 0? $arParams["IBLOCK_URL"]: $arResult["LIST_PAGE_URL"]
			);
		else
			$APPLICATION->AddChainItem($arResult["NAME"]);
	}

	if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
	{
		foreach($arResult["SECTION"]["PATH"] as $arPath)
		{
			$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
		}
	}

	return $arResult["ELEMENTS"];
}

?>