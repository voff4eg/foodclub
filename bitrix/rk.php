<?
define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (CModule::IncludeModule("statistic"))
{
	if (strlen($site_id)<=0)
	{
		$referer_url = strlen($_SERVER["HTTP_REFERER"])<=0 ? $_SESSION["SESS_HTTP_REFERER"] : $_SERVER["HTTP_REFERER"];
		if (strlen($referer_url)>0)
		{
			$url = parse_url($referer_url);
			$rs = CSite::GetList($v1="LENDIR", $v2="DESC", Array("ACTIVE"=>"Y", "DOMAIN"=> "%".$url["host"], "IN_DIR"=>$url["path"]));
			$arr = $rs->Fetch();
			$site_id = $arr["ID"];
		}
	}
	if (strlen($site_id)<=0) $site_id = false;
	$goto = eregi_replace("#EVENT_GID#", urlencode(CStatEvent::GetGID($site_id)), $goto);
	CStatEvent::AddCurrent($event1, $event2, $event3, $money, $currency, $goto, $chargeback, $site_id);
}
else $goto = eregi_replace("#EVENT_GID#","",$goto);
if (intval($id)>0 && CModule::IncludeModule("advertising")) CAdvBanner::Click($id);
LocalRedirect($goto);
?>