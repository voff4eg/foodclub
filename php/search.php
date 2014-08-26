<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$strHtml = str_replace("/", ", ", $_REQUEST['s']);
$strHtml = addslashes($strHtml);
$strHtml = htmlspecialchars ($strHtml);
$strHtml = preg_replace("/[a-z0-9]/i", "", $strHtml);

$_REQUEST['s'] = str_replace("/", " или ", $_REQUEST['s']);
$_REQUEST['s'] = addslashes($_REQUEST['s']);
// заменяем все специальные символы эквивалентом
$_REQUEST['s'] = htmlspecialchars ($_REQUEST['s']);
// отрезаем все ненужные симовлы
$_REQUEST['s'] = preg_replace("/[a-z0-9]/i", "", $_REQUEST['s']); 
//global $USER;
//if(!$USER->IsAdmin()) 
	//$APPLICATION->AuthForm("Доступ закрыт.");
	
if(strtoupper($_REQUEST['s']) == "САЛАТ"){ // Done
	$strTitle = "Рецепты салатов, приготовление салатов, праздничные рецепты";
	$strDescription = "Рецепты салатов с пошаговыми фотографиями и подробными инструкциями по приготовлению.";
	$strKeywords = "рецепты салатов, салат, праздничные рецепты салатов, приготовление салатов";
} elseif(strtoupper($_REQUEST['s']) == "КУРИЦА"){ // Done
	$strTitle = "Рецепты курицы, рецепты приготовления блюд из курицы";
	$strDescription = "Фото рецепты приготовления курицы. Каждый рецепт сопровождается подробным описанием и пошаговыми фотографиями.";
	$strKeywords = "рецепты курицы, рецепты блюд из курицы, приготовление курицы";
} elseif(strtoupper($_REQUEST['s']) == "СУПЫ"){ // Done
	$strTitle = "Рецепты супов, фото рецепты приготовления супов";
	$strDescription = "Рецепты приготовления супов с пошаговыми фотографиями и подробным описанием для каждого этапа.";
	$strKeywords = "рецепт супов, суп, рецепт приготовления супа, рецепт горохового супа,  рецепт супа харчо, рецепт рассольника, рецепт борща";
} elseif(strtoupper($_REQUEST['s']) == "ПИРОГ"){ // Done
	$strTitle = "Рецепты пирогов, приготовление пирогов, рецепты с фото";
	$strDescription = "Рецепты приготовления пирогов, пирожков с пошаговыми фотографиями и подробными инструкциями.";
	$strKeywords = "рецепты пирогов, приготовление пирогов, рецепты пирожков";
} elseif(strtoupper($_REQUEST['s']) == "КОКТЕЙЛЬ"){ // Done
	$strTitle = "Рецепты коктейлей, алкогольные коктейли, молочные коктейли";
	$strDescription = "Молочные коктейли, фруктовые, алкогольные коктейли с подробными фотографиями процесса приготовления.";
	$strKeywords = "рецепты коктейлей, алкогольные коктейли, молочные коктейли, безалкогольные коктейли, мохито, фруктовый коктейль";
} elseif(strtoupper($_REQUEST['s']) == "РЫБА"){ // Done
	$strTitle = "Рецепты рыбы, блюда из рыбы. Фотографии рецептов";
	$strDescription = "Рецепты блюд из рыбы с пошаговыми фотографиями и подробным описанием приготовления.";
	$strKeywords = "рецепты рыбы, рыба, блюда из рыбы, приготовление рыбы";
} else {
	$strTitle = $_REQUEST['s'];
	$strDescription = "Рецепты блюд с фотографиями и пошаговыми инструкциями.";
	$strKeywords = "фото рецепты, рецепты с фотографиями, фото блюд";
}

$APPLICATION->SetPageProperty("title", $strTitle);
$APPLICATION->SetPageProperty("description", $strDescription);
$APPLICATION->SetPageProperty("keywords", $strKeywords);

CModule::IncludeModule("search");
CModule::IncludeModule("iblock");

$q = $_REQUEST['s'];

//Реклама
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }


$obSearch = new CSearch;
$obSearch->Search(array(
	"QUERY" => $q,
	"MODULE_ID" => "iblock",
));

if ($obSearch->errorno != 0):
	$bNull = true;
else:
	while($arResult = $obSearch->GetNext()){
		//echo "<pre>";print_r($arResult);echo "</pre>";
		if($arResult["PARAM2"] == 5){
			$arItems["recipe"][] = $arResult['ITEM_ID'];
		}elseif($arResult["PARAM2"] == 2){
			$arItems["kitchens"][] = $arResult['ITEM_ID'];
		}elseif($arResult["PARAM2"] == 1){
			$arItems["dish_type"][] = $arResult['ITEM_ID'];
		}elseif($arResult["PARAM2"] == 3){
			$arItems["ingredients"][] = $arResult['ITEM_ID'];
		}else{
			$arItems[ substr($arResult['URL_WO_PARAMS'], 1) ][] = $arResult['ITEM_ID']; 	
		}
	}
endif;
//echo "<pre>";print_r($arItems);echo "</pre>";

if(is_null($arItems)){
	$bNull == true;
}
$arRecipe = Array();
if(!is_null($arItems['kitchens']) || !is_null($arItems['dish_type'])){
	$arProperty["IBLOCK_ID"] = 5;
		
	if($arItems['kitchens'])$arProperty["PROPERTY_kitchen"] =  $arItems['kitchens'];//if
	if($arItems['dish_type'])$arProperty["PROPERTY_dish_type"] =  $arItems['dish_type'];//if
	//if($arItems['stages'])$arProperty["PROPERTY_recipt_steps"] =  $arItems['stages'];//if
	
	$rsSt = CIBlockElement::GetList(Array(), $arProperty, false, false);
	
	while($arSt = $rsSt->GetNext()){
		$arRecipe[] = $arSt['ID']; 
	}
	unset($arProperty);
	
}//if

if($arItems['ingredients']){
	$arProperty["IBLOCK_ID"] = 4;
	$arProperty["PROPERTY_ingredient"] =  $arItems['ingredients'];
	if($arRecipe)$arProperty["PROPERTY_parent"] =  $arRecipe;
	
	$rsSt = CIBlockElement::GetList(Array(), $arProperty, false, false, Array("ID","PROPERTY_parent"));
	while($arSt = $rsSt->GetNext()){
		$arRecipe[] = $arSt['PROPERTY_PARENT_VALUE'];
	}
}//if

if($arItems['stages']){
	$arProperty["IBLOCK_ID"] = 5;
	if($arItems['stages'])$arProperty["PROPERTY_recipt_steps"] =  $arItems['stages'];//if
	
	$rsSt = CIBlockElement::GetList(Array(), $arProperty, false, false);
	while($arSt = $rsSt->GetNext()){
		$arRecipe[] = $arSt['ID']; 
	}
}//if

if($arItems['recipe'])$arRecipe = array_merge($arRecipe, $arItems['recipe']);//if
$ID = array_unique($arRecipe);
unset($arRecipe, $arResult);

if(count($ID) == 0){
	$bNull = true;
} else {
	$Prop = Array("ID"=>$ID, "ACTIVE"=>"Y");
	// Заглушка
	if(1 == 1){
		$Prop['PROPERTY_lib'] = "Y";
	}
	//$arNavStartParams = Array("nPageSize" => 25, "iNumPage"=>IntVal($_REQUEST["PAGER_1"]));
	$rsRecipes = CIBlockElement::GetList(Array(), $Prop, false, /*$arNavStartParams*/false, Array("ID", "NAME", "PREVIEW_TEXT", "CREATED_BY", "PREVIEW_PICTURE", "PROPERTY_dish_type", "PROPERTY_kitchen", "PROPERTY_comment_count"));
	while($arRecipe = $rsRecipes->GetNext()){
		
		$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
		$arUser = $rsUser->Fetch();
		
		if(strlen($arUser["NAME"]) > 0 && strlen($arUser["LAST_NAME"]) > 0){
	     	$arUser['FULLNAME'] = $arUser["NAME"]." ".$arUser["LAST_NAME"];
	 	}else{
	 		if(strpos($arUser['EXTERNAL_AUTH_ID'], "OPENID") !== false){
				if(strpos($arUser['LOGIN'], "livejournal") !== false){
					$arUser['FULL_LOGIN'] = $arUser['LOGIN'];
					$arUser['LOGIN'] = substr($arUser['LOGIN'], 7, (strpos($arUser['LOGIN'], ".livejournal")-7));
					$arUser['LOGIN_TYPE'] = "lj";
				}
			}
	 		$arUser['FULLNAME'] = $arUser['LOGIN'];	 	
	 	}

		$arRecipe['USER'] = $arUser;
		
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
	
	if(count($arResult['ITEMS'])){
		$strRecipeHTML = '';
		$Html = '';
		$JSallRecipesResult = "var allRecipesResult = [";
		foreach($arResult['ITEMS'] as $key => $arRecipe){
			if($key < 24){
				$Html .= '<div class="item recipe_list_item">
							<div class="photo"><a href="/detail/'.$arRecipe['ID'].'/" class="'.$arRecipe['NAME'].'"><img src="'.$arRecipe['PREVIEW_PICTURE']['SRC'].'" width="'.$arRecipe['PREVIEW_PICTURE']['WIDTH'].'" height="'.$arRecipe['PREVIEW_PICTURE']['HEIGHT'].'" alt="'.$arRecipe['PREVIEW_PICTURE']['DESCRIPTION'].'"></a></div>
							<h5><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a></h5>
							<p class="author">От: '.$arRecipe['USER']['FULLNAME'].'</p>
							<p class="info">
								<span class="comments_icon"><noindex><a href="/detail/'.$arRecipe['ID'].'/#comments">'.intval($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a></noindex></span>
							</p>
						</div>';
			}else{
				$JSallRecipesResult .= $arRecipe["ID"];
				$JSallRecipesResult .= ", ";
			}
		}
		if(count($arResult['ITEMS']) >= 24){
			$JSallRecipesResult = substr($JSallRecipesResult, 0, -2);
		}
		$JSallRecipesResult .= "];";
		if(strlen($Html) > 0){
			$strRecipeHTML = '<div id="recipe_feed_block">'.$Html.'<div class="clear"></div></div>';
		}
	}
}

if($bNull || !isset($arResult['ITEMS'])){
	?><div id="content">
	    <div id="text_space">
		    <div class="body"><h1>Вы искали: <?=$strHtml?>. Но мы ничего не нашли.</h1></div>
		    <div class="search_switch">
                <div class="item"><a href="/posts_search/?q=<?=$strHtml?>">В записях</a></div>
                <div class="item act"><span>В рецептах</span></div>
                <div class="clear"></div>
            </div>
			<div id="fc_statistics"><div class="wrapper"><span class="item"><span class="num"><?=count($arResult['ITEMS'])?></span> <span class="word">рецептов</span></span></div></div>
        </div>
        <div id="banner_space">
            <?if(strlen($strBanner) > 0){?><div class="banner"><h5>Реклама</h5><?=$strBanner?></div><?}?>
			<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
		</div>
		<div class="clear"></div>
	</div>
	<?
} else {
	?>
	<div id="content">
	    <div id="text_space">		
		  <?if(strtoupper($strHtml) == "САЛАТ") {?><h1>Рецепты салатов</h1>
		  <?}elseif(strtoupper($strHtml) == "КУРИЦА") {?><h1>Рецепты курицы</h1>
		  <?}elseif(strtoupper($strHtml) == "СУПЫ") {?><h1>Рецепты супов</h1>
		  <?}elseif(strtoupper($strHtml) == "КОКТЕЙЛЬ") {?><h1>Рецепты коктейлей</h1>
		  <?}elseif(strtoupper($strHtml) == "РЫБА") {?><h1>Рецепты рыбы</h1>
		  <?}elseif(strtoupper($strHtml) == "ПИРОГ") {?><h1>Рецепты пирогов</h1>
		  <?}else{?><h1>Вы искали: <?=$strHtml?></h1><?} //if?>
            <div class="search_switch">
                <div class="item"><a href="/posts_search/?q=<?=$strHtml?>">В записях</a></div>
                <div class="item act"><span>В рецептах</span></div>
                <div class="clear"></div>
            </div>
			<div id="fc_statistics"><div class="wrapper"><span class="item"><span class="num"><?=count($arResult['ITEMS'])?></span> <span class="word">рецептов</span></span></div></div>
			<?if(strlen($JSallRecipesResult)):?>
				<script type="text/javascript">
					//В массиве перечислены id последних рецептов. Первые 9 из них должны быть уже выведены на странице при загрузке, остальные 9*n перечислены здесь для дальнейшей подгрузки через ajax
					<?=$JSallRecipesResult?>
				</script>
			<?endif;?>
		<?=$strRecipeHTML?>
		<?if(count($arResult["ITEMS"]) >= 24):?>
		<div id="get_more_recipes" class="">
			<a class="frame_bg" href="#"><span class="left"><span class="right"><span class="bg"><span>Ещё</span></span></span></span></a>
			<img height="20" width="160" alt="" src="/images/preloader.gif">
		</div>
		<?endif;?>
		</div>
        <div id="banner_space">
			<?
			$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
			if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
			<div id="do-you-know-that" class="b-facts">
				<div class="b-facts__heading">Знаете ли вы что:</div>
				<div class="b-facts__content">
					<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
						<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
					</div>
				</div>				
				<div class="b-facts__more">
					<a href="#" class="b-facts__more__link">Еще</a>
				</div>				
			</div>
			<?}?>
            <?if(strlen($strBanner) > 0){?><div class="banner"><h5>Реклама</h5><?=$strBanner?></div><?}?>
			<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
		</div>
		<div class="clear"></div>
	</div>
	<?
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
