
<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


function mysetDate($d)
{
	//str_replace(" ", "T", str_replace(".", "-",$arPost["DATE_PUBLISH"]) )
	$m1=explode(" ", $d);
	$m2=explode(".", $m1[0]);
	return $m2[2]."-".$m2[1]."-".$m2[0]."T".$m1[1];
}


function TimeEncode($iTime)
{
	$iTZ = date("Z", $iTime);
	$iTZHour = intval(abs($iTZ)/3600);
	$iTZMinutes = intval((abs($iTZ)-$iTZHour*3600)/60);
	$strTZ = ($iTZ<0? "-": "+").sprintf("%02d:%02d", $iTZHour, $iTZMinutes);
	return date("Y-m-d",$iTime)."T".date("H:i:s",$iTime).$strTZ;
}






CModule::IncludeModule("blog"); 
//Сортировка 
$SORT = Array("ID" => "ASC");  

//Выбираемые из базы поля, "UF_ONMAIN" - пользовательское свойство типа "Да/нет"
$SELECT = array("ID", "TITLE", "BLOG_ID", "AUTHOR_ID", "DATE_PUBLISH");  

//Фильтруем по нашему свойству, ищем записи, где оно установлено как "Да"
$arFilter = Array(); 
$arGroups=array();
$dbPosts = CBlogPost::GetList(
        $SORT,
        $arFilter,
        false,
        false,
        $SELECT
    );
// print_r($dbPosts);
  //echo '<br/><br/>';
  
while ($arPost = $dbPosts->Fetch())
{
     // print_r($arPost);
    //echo '<br/><br/>';
$i++;
	

	if($arGroups[$arPost["BLOG_ID"]])
	{
	//	echo "www.foodclub.ru/"."blogs/group/".$arGroups[$arPost["BLOG_ID"]]."/blog/".$arPost['ID']."/ : ".str_replace(" ", "T", str_replace(".", "-",$arPost["DATE_PUBLISH"]) );
		echo"
<url>
	<loc>http://www.foodclub.ru/"."blogs/group/".$arGroups[$arPost["BLOG_ID"]]."/blog/".$arPost['ID']."/</loc>
	<lastmod>".TimeEncode(MakeTimeStamp(ConvertDateTime($arPost["DATE_PUBLISH"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"))."</lastmod>
</url>";
	}
	else
	{
		$SORT1 = Array("DATE_CREATE" => "DESC", "NAME" => "ASC");
		$arFilter1 = Array("ID" => $arPost["BLOG_ID"]);	
		$arSelectedFields1 = array();
		$dbBlogs = CBlog::GetList($SORT1,$arFilter1,false,false,$arSelectedFields1);		
		while ($arBlog = $dbBlogs->Fetch())
		{			
			$arGroups[$arPost["BLOG_ID"]]=$arBlog['SOCNET_GROUP_ID'];			
		}
		if($arGroups[$arPost["BLOG_ID"]])
		{			
		echo"
<url>
	<loc>http://www.foodclub.ru/"."blogs/group/".$arGroups[$arPost["BLOG_ID"]]."/blog/".$arPost['ID']."/</loc>
	<lastmod>".TimeEncode(MakeTimeStamp(ConvertDateTime($arPost["DATE_PUBLISH"], "DD.MM.YYYY HH:MI:SS"), "DD.MM.YYYY HH:MI:SS"))."</lastmod>
</url>";
		}
		else 
		{
			
		}
	}
}

//echo "<br><br><br>".$i;

?>





<br>
<br>
<br>

<?
// выберем все активные блоги, привязанные к текущему сайту.
// результат будет отсортирован сначала по дате создания, затем по названию блога
// выберутся только необходимые нам поля: Идентификатор блога, Название блога, Адрес блога,
// Идентификатор владельца блога и Дату создания блога

?>