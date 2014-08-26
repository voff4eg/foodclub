<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$APPLICATION->IncludeComponent("bitrix:socialnetwork.events_dyn", ".default", Array(
	"PATH_TO_USER"	=>	"/blogs/user/#user_id#/",
	"PATH_TO_GROUP"	=>	"/blogs/group/#group_id#/",
	"PATH_TO_MESSAGE_FORM"	=>	"/blogs/messages/form/#user_id#/",
	"PATH_TO_MESSAGE_FORM_MESS"	=>	"/blogs/messages/form/#user_id#/#message_id#/",
	"PATH_TO_MESSAGES_CHAT"	=>	"/blogs/messages/chat/#user_id#/",
	"PATH_TO_SMILE"	=>	"/bitrix/images/socialnetwork/smile/",
	"MESSAGE_VAR"	=>	"message_id",
	"PAGE_VAR"	=>	"page",
	"USER_VAR"	=>	"user_id"
	)
);
?>