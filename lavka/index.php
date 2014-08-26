<?
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetPageProperty("description", "Товары которые предлагают магазины партнеры");
$APPLICATION->SetPageProperty("keywords", "Лавка");

}else{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	
	if (CModule::IncludeModule("advertising")){
	$strBanner_right = CAdvBanner::Show("right_banner");
	//$strBanner_middle = CAdvBanner::Show("middle_banner");
	}
}

$APPLICATION->SetTitle("Лавка Foodclub.ru");


?>
	<div id="content">
		<div id="text_space">
			<?if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'): ?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array(
				"AREA_FILE_SHOW" => "file", 
				"PATH" => "/include/lavka.html", 
				"AREA_FILE_RECURSIVE" => "N", 
				"EDIT_TEMPLATE" => "" 
				)
			);?>
			<?endif?>		
		
			<?$APPLICATION->IncludeComponent("custom:store.banner.horlavka", "", Array(),false);?>			
		</div>
		<div id="banner_space">
			<?if(strlen($strBanner_right) > 0){?><div class="banner"><h5>Реклама</h5><?=$strBanner_right?></div><?}?>
			<!--div class="banner"><h5>Реклама</h5><a href=""><img src="/images/infoblock/banner.jpg" width="240" height="400" alt=""></a></div-->
		</div>
		<div class="clear"></div>
	</div>



<?
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
}else{
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
}?>