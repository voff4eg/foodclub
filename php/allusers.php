<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$filter = Array
(
    "ACTIVE"              => "Y",
);

// исходный формат
$format = "Y-m-d H:i:s";

$rsUsers = CUser::GetList(($by="personal_country"), ($order="desc"), $filter); // выбираем пользователей
//echo count($rsUsers);
$r = '<?xml version="1.0" encoding="UTF-8"?>';
$r .= "\r\n";
$r .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
$r .= "\r\n";
while ($arUser = $rsUsers->Fetch()){		
	$q = $arUser["LAST_ACTIVITY_DATE"] ? $arUser["LAST_ACTIVITY_DATE"] : date($format, strtotime($arUser["DATE_REGISTER"]));

	$r.="<url>
	<loc>http://www.foodclub.ru/profile/".$arUser["ID"]."/</loc>
	<lastmod>".str_replace(" ", "T", $q)."+04:00</lastmod>
</url>\r\n";

}

$r .= '</urlset>';
$f = fopen("sitemap_users.xml", "w");
fwrite($f, $r);
fclose($f);



echo $r;



?>