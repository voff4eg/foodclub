<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("advertising"))
{
	//CAdvBanner::SetRequiredKeywords(array("егоров"), "right_banner");
	//$APPLICATION->SetPageProperty("keywords", "егоров");
	CAdvBanner::SetRequiredKeywords(array("шварцвальдский торт"), "right_banner");
	$APPLICATION->SetPageProperty("keywords", "шварцвальдский торт");
	echo "<!--<pre>";print_r(CAdvBanner::GetKeywords());echo "</pre>-->";
	$strBannerTEST = CAdvBanner::Show("right_banner");
}
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php");
$APPLICATION->SetTitle("Заголовок страницы");
?>
<?
if (CModule::IncludeModule("advertising"))
{
	echo $strBannerTEST;
}
?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>