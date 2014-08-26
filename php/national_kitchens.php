<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if (!CModule::IncludeModule("blog"))
{
	ShowError(GetMessage("BLOG_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("socialnetwork"))
{
	ShowError(GetMessage("SONET_MODULE_NOT_INSTALL"));
	return;
}
global $USER;

$Items = array();
$rsBlogs = CBlog::GetList(array(),array("!=ID"=>51),false,false);
while ($arBlog = $rsBlogs->GetNext()) {
	if(strpos($arBlog["NAME"],"кухня") !== false || strpos($arBlog["NAME"],"Тайланд") !== false || strpos($arBlog["NAME"],"Америка") !== false || strpos($arBlog["NAME"],"Греция") !== false || strpos($arBlog["NAME"],"Австрия") !== false){
		$arItem[ $arBlog["ID"] ] = $arBlog["NAME"];
		$Items[] = $arBlog["ID"];
	}
}
echo "<pre>";print_r($arItem);echo "</pre>";

$dbPost = CBlogPost::GetList(
	array(),
	array("BLOG_ID" => $Items),
	false,
	false,
	array("ID", "TITLE", "BLOG_ID", "AUTHOR_ID", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DATE_CREATE", "DATE_PUBLISH", "KEYWORDS", "PUBLISH_STATUS", "ATRIBUTE", "ATTACH_IMG", "ENABLE_TRACKBACK", "ENABLE_COMMENTS", "VIEWS", "NUM_COMMENTS", "NUM_TRACKBACKS", "CATEGORY_ID")
);
$arPosts = array();
while($arPost = $dbPost->Fetch()){
	$arPosts[] = $arPost["ID"];
}
//Перенос постов в Национальную кухню
foreach($arPosts as $keyPost){
	$updateID = CBlogPost::Update($keyPost,array("BLOG_ID"=>"51"));
	if(IntVal($updateID)>0)
	{
	    echo "Сообщение [".$updateID."] изменено.";
	}
	else
	{
	    if ($ex = $APPLICATION->GetException())
		echo $ex->GetString();
	}
}

$rsKitchens = CSocNetGroup::GetList(Array("NAME"=>"ASC"), Array(), false, false);
$Kitchens = array();
while ($arKitchen = $rsKitchens->GetNext()) {
	if((strpos($arKitchen["NAME"],"кухня") !== false || strpos($arKitchen["NAME"],"Тайланд") !== false || strpos($arKitchen["NAME"],"Америка") !== false || strpos($arKitchen["NAME"],"Греция") !== false || strpos($arKitchen["NAME"],"Австрия") !== false) && $arKitchen["ID"] != 82){
		$Kitchens[] = $arKitchen["ID"];
	}	
}
echo "<pre>Kitchens";print_r($Kitchens);echo "</pre>";
//Удаление кухонь
foreach($Kitchens as $kitchen){
	$arFields = array(
		"CLOSED" => "Y",
		"VISIBLE" => "N",
	);
	/*if (!CSocNetGroup::Delete($kitchen)){
		if ($e = $APPLICATION->GetException())
			$errorMessage .= $e->GetString();
	}*/
	if (!CSocNetGroup::Update($kitchen, $arFields)){
		if ($e = $GLOBALS["APPLICATION"]->GetException())
			$errorMessage .= $e->GetString();
	}
}
