<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?$APPLICATION->IncludeComponent(
	"bitrix:blog.rss.all",
	"",
	Array(
		"MESSAGE_COUNT" => "10", 
		"PATH_TO_POST" => "/blogs/group/#group_id#/blog/#post_id#/", 
		"PATH_TO_USER" => "/profile/#user_id#/", 
		"BLOG_VAR" => "blog", 
		"POST_VAR" => "post_id", 
		"USER_VAR" => "user_id", 
		"PAGE_VAR" => "", 
		"GROUP_ID" => "", 
		"TYPE" => "rss2", 
		"CACHE_TYPE" => "A", 
		"CACHE_TIME" => "86400" 
	)
);
?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>