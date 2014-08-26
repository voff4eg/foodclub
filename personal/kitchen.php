<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>
<?if(!$_REQUEST['u'])$_REQUEST['u']=$USER->GetId();?>
<?

if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }


$arUser = $APPLICATION->IncludeComponent("custom:profile", "", Array());

$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; рецепты пользователя на Foodclub");
?>
<div id="content">
	<div class="b-personal-page">
	<?$APPLICATION->IncludeFile("/personal/.profile_header.php", Array(
		"USER" => $arUser)
	);?>
	
	<?$APPLICATION->IncludeComponent(
		"custom:profile_menu",
		"",
		Array(
			"ROOT_MENU_TYPE" => "profile",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "profile",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => ""
		),
	false
	);?>

	<div id="text_space">
	<script src="/js/k/script.js" type="text/javascript" ></script>
	<link rel="stylesheet" type="text/css" href="/js/k/style.css">
		<?
		$APPLICATION->IncludeComponent("custom:profile.kitchen", "", array("USER"=>$arUser));
		$APPLICATION->IncludeComponent("custom:profile.kitchen.form", "", array("USER"=>$arUser));
		?>	
	</div>
	
	<div id="banner_space">
		<?$APPLICATION->IncludeComponent("custom:profile.badges", "", array("USER" => $arUser));?>
		
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
		<?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
	</div>
	<div class="clear"></div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
