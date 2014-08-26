<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
// This code for $_GET only. 
if (!empty($_GET["subscribe_topic"]) && check_bitrix_sessid())
{
	$FORUM_TOPIC_ID = intVal($arResult["ELEMENT"]["PRODUCT_PROPS"]["FORUM_TOPIC_ID"]["VALUE"]);
	if ($FORUM_TOPIC_ID > 0)
	{
		if ($_REQUEST["subscribe_topic"] == "Y")
			ForumSubscribeNewMessagesEx($arParams["FORUM_ID"], $FORUM_TOPIC_ID, "N", $strErrorMessage, $strOKMessage);
		elseif ($_REQUEST["subscribe_topic"] == "N")
		{
			$arFilter = array(
				"USER_ID" => $GLOBALS["USER"]->GetId(), 
				"FORUM_ID" => $arParams["FORUM_ID"], 
				"TOPIC_ID" => $FORUM_TOPIC_ID);
			$db_res = CForumSubscribe::GetList(array(), $arFilter);
			if ($db_res && $res = $db_res->Fetch())
			{
				do 
				{
					if (CForumSubscribe::CanUserDeleteSubscribe($res["ID"], $GLOBALS["USER"]->GetUserGroupArray(), $GLOBALS["USER"]->GetID()))
					{
						CForumSubscribe::Delete($res["ID"]);
					}
				}while ($res = $db_res->Fetch());
			}
		}
		BXClearCache(true, "/bitrix/forum/user/".$GLOBALS["USER"]->GetID()."/subscribe/");	
		$url = $GLOBALS["APPLICATION"]->GetCurPageParam("", array("subscribe_topic", "sessid"));
		LocalRedirect($url);
	}
}
?>