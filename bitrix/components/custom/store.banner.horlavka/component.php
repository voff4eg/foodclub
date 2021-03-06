<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/css/lavka.css?'.filectime($_SERVER["DOCUMENT_ROOT"]."/css/lavka.css").'">');
$APPLICATION->AddHeadScript('/bitrix/components/custom/store.banner.horlavka/script.js');
if (CModule::IncludeModule("advertising")){
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && intval($_GET['id'])!="0")
{
	$arFilter = Array(//только с этим ID	
		"ID"=>intval($_GET['id'])
	);
}else
{
	$arFilter = Array(//только активные
		"LAMP"=> "green"
	);
}


$rsAdvContract = CAdvContract::GetList($by, $order, $arFilter, $is_filtered, "N");
$contractIdList=Array();
$arContractIDs=Array();
$arBannerList = array();
$countContracts=$rsAdvContract->SelectedRowsCount();

while($arContract=$rsAdvContract->Fetch()){
	$contractList[ $arContract["ID"] ] = array('id' => $arContract["ID"],'name' => $arContract["NAME"]);
	$arContractIDs[] = $arContract["ID"];
}
if(!empty($arContractIDs)){
	$arResult["bannercontracts"] = $contractList;
	$arFilter = Array("LAMP" => 'green', "CONTRACT_ID" => $arContractIDs, "TYPE_SID"=>"store_horizontal | store_vertical");
	$rsBanners = CAdvBanner::GetList($by, $order, $arFilter, $is_filtered, "N");
	//require($_SERVER["DOCUMENT_ROOT"]."/simple_html_dom.php");
	while($arBanner = $rsBanners->Fetch()){
		preg_match_all('/(a|href)=("|\')[^"\'>]+/i',$arBanner["CODE"], $matches);
		if(!empty($matches[0])){
			$matches[0] = array_unique($matches[0]);
			foreach($matches[0] as $match){
				$match = str_replace("href=\"", "", $match);				
				$arBanner["CODE"] = str_replace($match, "/bitrix/rk.php?id=".$arBanner["ID"]."&goto=".urlencode($match), $arBanner["CODE"]);
			}
		}		
		$arBannerList[ $arBanner["CONTRACT_ID"] ][ $arBanner["ID"] ] = $arBanner;
		CAdvBanner::FixShow(array("ID"=>$arBanner["ID"],"FIX_SHOW"=>"Y","CONTRACT_ID"=>$arBanner["CONTRACT_ID"]));
	}
	$arResult["banners"] = $arBannerList;
}	
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