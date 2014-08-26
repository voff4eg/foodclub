<?
if(isset($_REQUEST['s'])){
	$_REQUEST['s'] = htmlspecialchars(trim($_REQUEST['s']));
	if(strpos($_REQUEST['s'], "page") !== false){
		$arPager = explode("/", substr($_REQUEST['s'], strpos($_REQUEST['s'], "page")) );
		$_REQUEST['s'] = substr($_REQUEST['s'], 0, (strpos($_REQUEST['s'], "page")-1));
		$_GET = array_merge(Array("PAGEN_1"=>$arPager[1]), $_GET);
	}
} else {
	$_REQUEST['s'] = "";
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Поиск");

$strQ = str_replace(Array(" ", "/", "\"", "'"), "", StrVal(strtolower($_REQUEST['s'])));
$trans = array("а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e", "ё"=>"yo","ж"=>"j","з"=>"z","и"=>"i","й"=>"i","к"=>"k","л"=>"l", "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t", "у"=>"y","ф"=>"f","х"=>"h","ц"=>"c","ч"=>"ch", "ш"=>"sh","щ"=>"sh","ы"=>"i","э"=>"e","ю"=>"u","я"=>"ya","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D","Е"=>"E", "Ё"=>"Yo","Ж"=>"J","З"=>"Z","И"=>"I","Й"=>"I","К"=>"K", "Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P", "Р"=>"R","С"=>"S","Т"=>"T","У"=>"Y","Ф"=>"F", "Х"=>"H","Ц"=>"C","Ч"=>"Ch","Ш"=>"Sh","Щ"=>"Sh", "Ы"=>"I","Э"=>"E","Ю"=>"U","Я"=>"Ya","ь"=>"","Ь"=>"","ъ"=>"","Ъ"=>"");
$strKey = strtr($strQ, $trans);

$obCache = new CPageCache;
if($USER->IsAdmin() || $obCache->StartDataCache((3*60*60), "search".$strKey)):
	CModule::IncludeModule("iblock");
	$CFClub = CFClub::getInstance();
	
	$arSearch = explode("/", $_REQUEST['s']);
	
	// Поиск кухни
	$strheadHTML = "";
	$arKitchenID = Array();
	foreach($arSearch as $arItem){
		$rsKitchen = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>2, "NAME"=>$arItem), false, false);	
		if($arKitchen = $rsKitchen->GetNext()){
			$arKitchenID[] = $arKitchen['ID'];
		}
		$strHtml .= strtolower($arItem.", ");	
	}
	
	// Поиск типов блюд
	$arDishID = Array();
	foreach($arSearch as $arItem){
		$rsDish = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>1, "NAME"=>$arItem), false, false);	
		if($arDish = $rsDish->GetNext()){
			$arDishID[] = $arDish['ID'];
		}
	}
	
	// Поиск ингридиентов
	$arUnitID = Array();
	foreach($arSearch as $arItem){
		$rsUnit = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>3, "NAME"=>$arItem), false, false);	
		if($arUnit = $rsUnit->GetNext()){
			$arUnitID[] = $arUnit['ID'];
		}
	}
	
	if(count($arKitchenID) > 0 || count($arDishID) > 0 || count($arUnitID) > 0){
		$rsSt = CIBlockElement::GetList(Array(), Array("IBLOCK_ID"=>5, "PROPERTY_kitchen"=>$arKitchenID, "PROPERTY_dish_type"=>$arDishID), false, false);
		$arStages = Array();
		
		while($obSt = $rsSt->GetNextElement()){
			$arFields = $obSt->GetFields();
			$arProp = $obSt->GetProperties();
			
			$arStages = array_merge($arStages, $arProp['recipt_steps']['VALUE']);
			
			$arFields["PROPS"] = $arProp;
			$arRecipeData[ $arFields['ID'] ] = $arFields;
		}
		
		$rsRecipe = CIBlockElement::GetList(Array(), Array("ID"=>$arStages, "PROPERTY_ingredient"=>$arUnitID), false, false);
		while($obRecipe = $rsRecipe->GetNextElement()){
			$arProp = $obRecipe->GetProperties();
			$arResult[ $arProp['parent']['VALUE'] ] = true;
		}
		
		if($arResult == NULL) $bNull = true;
		

		$ID = array_keys($arResult);
		unset($arResult);
		
		$arNavStartParams = Array("nPageSize" => 10);
		$rsRecipes = CIBlockElement::GetList(Array("DATE_CREATE"=>"DESC"), Array("ID"=>$ID, "ACTIVE"=>"Y"), false, $arNavStartParams, Array("ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_dish_type", "PROPERTY_kitchen"));
		while($arRecipe = $rsRecipes->GetNext()){
			if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
				$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
				$arFile = $rsFile->Fetch();
				$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
				$arRecipe["PREVIEW_PICTURE"] = $arFile;
			}
			$arResult['ITEMS'][] = $arRecipe;
		}
		
		if($rsRecipes->IsNavPrint()){
			$arResult["NAV_STRING"] = $rsRecipes->GetPageNavStringEx($navComponentObject, "Рецепты", "search", "N");
		}
		
		
		$strRecipeHTML = '';
		foreach($arResult['ITEMS'] as $arRecipe){
			$strRecipeHTML .= '<li><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a><span class="author"></span></li>';
		}
		
	} else {
		$strRecipeHTML = '';
		$arNavStartParams = Array("nPageSize" => 10);
		$rsRecipes = CIBlockElement::GetList(Array("DATE_CREATE"=>"DESC"), Array("%NAME"=>$arSearch[0], "ACTIVE"=>"Y"), false, $arNavStartParams, Array("ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_dish_type", "PROPERTY_kitchen"));
		while($arRecipe = $rsRecipes->GetNext()){
			if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
				$rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
				$arFile = $rsFile->Fetch();
				$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
				$arRecipe["PREVIEW_PICTURE"] = $arFile;
			}
			$arResult['ITEMS'][] = $arRecipe;
			$strRecipeHTML .= '<li><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a><span class="author"></span></li>';
		}
		
		if($rsRecipes->IsNavPrint()){
			$arResult["NAV_STRING"] = $rsRecipes->GetPageNavStringEx($navComponentObject, "Рецепты", "search", "N");
		}
		
		if(empty($arResult['ITEMS'])){
			$bNull = true;
		}		
	}
	
	if($bNull){
		?><div id="content">
			<h1>Вы искали: <?=substr($strHtml, 0, -2)?>. Но мы ничего не нашли.</h1>
		</div>
		<?
	} else {
		?>
		<div id="content">
			<h1>Вы искали: <?=substr($strHtml, 0, -2)?></h1>
			<ul class="recipes">
			<?=$strRecipeHTML?>
			</ul>
			<?if(isset($arResult['NAV_STRING'])) echo $arResult['NAV_STRING'];?>
		</div>
		<?
	}
	$obCache->EndDataCache();
endif;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
