<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//Передаются параметры
//num - число рецептов, которые передаются детально, остальные только id
//type - тип данных, по которым произошла сортировка (Кухня, Метки, Основной ингредиент)
//data - id кухни или метка, в общем данные из фильтра
function rus2translit($string)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => "",  'ы' => 'y',   'ъ' => "",
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
 
        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}



if(isset($_REQUEST["type"]) && isset($_REQUEST["data"])){
	$data = array("cuisine"=>"kitchen","dish"=>"dish_type","ingredient"=>"main_ingredient");
	$obCache = new CPHPCache;
	$site_id = $_REQUEST["siteId"];
	if($obCache->InitCache(86400, "all".$_REQUEST["type"]."_".md5($_REQUEST["data"])."_".$site_id, "all_")){
		$vars = $obCache->GetVars();
		$str = $vars["str"];
	}else{
		CModule::IncludeModule("iblock");
		global $CACHE_MANAGER;
		$CACHE_MANAGER->StartTagCache("all");
		$key = 0;
		$recipes = array();
		$id = array();
		if($_REQUEST["type"]=="tag")
		{
			
			if(CModule::IncludeModule("search"))
			{
				$allTags=array();
				$adminTags=file($_SERVER["DOCUMENT_ROOT"]."/admintags.txt");

				$SqlReqestCount=48-count($adminTags);

				$allTags=$adminTags;
				
				if(CModule::IncludeModule('search'))
				{
					$rsTags = CSearchTags::GetList(
						array(),
						array(
							"MODULE_ID" => "iblock",
						),
						array(
							"CNT" => "DESC",
						),
						$SqlReqestCount
					);
					while($arTag = $rsTags->Fetch())
						{
							$allTags[]=$arTag['NAME'];
							$i++;
						}
				}			
			}
			
			$transTags=array();
			foreach($allTags as $tag1)
			{
				$transTags[rus2translit(trim($tag1))]=$tag1;
			}
			
			
			$arAllFilter = array("?TAGS"=>$transTags[$_REQUEST["data"]], "SITE_ID"=>'s1');
		}
		else{
			$arAllFilter = array("PROPERTY_".$data[ $_REQUEST["type"] ]=>$_REQUEST["data"],"ACTIVE"=>"Y","PROPERTY_lib"=>"Y","SITE_ID"=>SITE_ID);
		}
		if(SITE_ID == "s1"){
		    $arAllFilter["IBLOCK_ID"] = 5;
		    $site_dir = "/";
		}elseif(SITE_ID == "fr"){
		    $arAllFilter["IBLOCK_ID"] = 24;
		    $site_dir = "/fr/";
		}

		$rsRecipes = CIBlockElement::GetList(array("ACTIVE_FROM"=>"DESC","DATE_CREATE"=>"DESC"),$arAllFilter,false,false,array("NAME","ID", "CODE","PREVIEW_PICTURE","DETAIL_PICTURE","CREATED_BY","PROPERTY_comment_count"));
		while($arRecipe = $rsRecipes->GetNext()){
			$key++;
			if($key <= $_REQUEST["num"]){
				if(intval($arRecipe["PREVIEW_PICTURE"])){
					$arFile = CFile::GetFileArray($arRecipe["PREVIEW_PICTURE"]);
					$src = "http://www.foodclub.ru".$arFile["SRC"];
				}else{
					$src = "";
				}
				$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				$recipes[] = array("name"=>$arRecipe["NAME"],"href"=>$site_dir."detail/".($arRecipe["CODE"] ? $arRecipe["CODE"] : $arRecipe["ID"])."/","src"=>$src, "author"=>(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0 ? $arUser["NAME"]." ".$arUser["LAST_NAME"] : $arUser["LOGIN"]), "comments"=>intval($arRecipe["PROPERTY_COMMENT_COUNT_VALUE"]));
			}else{
				$id[] = $arRecipe["ID"];
			}
		}
		$str = array("recipes"=>$recipes,"id"=>$id);
		$CACHE_MANAGER->RegisterTag("all_recipes_result_".SITE_ID."_".$_REQUEST["data"]);
		$CACHE_MANAGER->EndTagCache();
	}
	if($obCache->StartDataCache()):
		echo json_encode($str);
		$obCache->EndDataCache(array(
			"str"    => $str
		)); 
	endif;
}
?>