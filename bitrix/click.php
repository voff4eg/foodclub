<?
define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("statistic"))
{
	if (strlen($site_id)<=0)
	{
		$referer_url = strlen($_SERVER["HTTP_REFERER"])<=0 ? $_SESSION["SESS_LAST_PAGE"] : $_SERVER["HTTP_REFERER"];
		$url = parse_url($referer_url);
		$arr = $APPLICATION->GetSiteByDir($url["path"], $url["host"]);
		$site_id = $arr["SITE_ID"];
		if (strlen($site_id)<=0) $site_id = false;
	}
	$goto = eregi_replace("#EVENT_GID#", CStatEvent::GetGID($site_id), $goto);
	CStatEvent::AddCurrent($event1, $event2, $event3, $money, $currency, $goto, $chargeback, $site_id);
}
else $goto = eregi_replace("#EVENT_GID#","",$goto);
if (intval($id)>0 && CModule::IncludeModule("advertising")) CAdvBanner::Click($id);
LocalRedirect($goto);
?>