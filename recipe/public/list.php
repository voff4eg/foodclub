<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->AddHeadScript("/js/history/scripts/bundled/html4+html5/jquery.history.js");

$APPLICATION->SetPageProperty("title", "Фото рецепты, кулинарные рецепты с фото");
$APPLICATION->SetPageProperty("description", "Фото рецепты на сайте foodclub.ru с подробными описаниями каждого этапа приготовления и пошаговыми фотографиями.");
$APPLICATION->SetPageProperty("keywords", "фото рецепты, рецепты с фото, рецепты с пошаговыми фотографиями, рецепты с фотографиями, фотографии блюд");
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/factory.class.php");
$Factory = new CFactory;

if (CModule::IncludeModule("advertising")){ 
	$strBanner = CAdvBanner::Show("right_banner");
	$strBanner_middle = CAdvBanner::Show("middle_banner");
}
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"]."/classes/main.class.php");
$CFClub = new CFClub();

global $arKitchens;
global $arDishType;


//$arKitch = $CFClub->getKitchens();

$intCount = 27;	
//if(isset($_GET['k']) || isset($_GET['d']))$intCount = 0;

$cache_id = "all_recipes_".SITE_ID."_".(intval($_GET['d']) > 0 ? intval($_GET['d']) : 0 )."_".(intval($_GET['k']) > 0 ? intval($_GET['k']) : 0 );
$cache_dir = "/all_recipes/";

$obCache = new CPHPCache;
if($obCache->InitCache(36000, $cache_id, $cache_dir))
{
	$arRecipes = $obCache->GetVars();
}
elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache())
{
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	
	if(!isset($CFClub)){
		require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');
		$CFClub = CFClub::getInstance();		
	}
		
	$arRecipes = $CFClub->getList($intCount, $_GET);
	
	$CACHE_MANAGER->RegisterTag("all_recipes_".SITE_ID."_".(intval($_GET['d']) > 0 ? intval($_GET['d']) : 0 )."_".(intval($_GET['k']) > 0 ? intval($_GET['k']) : 0 ));
	$CACHE_MANAGER->EndTagCache();

	$obCache->EndDataCache($arRecipes);
}
else
{
     $arRecipes = array();
}

$cache_id = "all_id_recipes_".SITE_ID."_".(intval($_GET['d']) > 0 ? intval($_GET['d']) : 0 )."_".(intval($_GET['k']) > 0 ? intval($_GET['k']) : 0 );
$cache_dir = "/all_id_recipes/";
if($obCache->InitCache(36000, $cache_id, $cache_dir)){
	$arIds = $obCache->GetVars();
}elseif(CModule::IncludeModule("iblock") && $obCache->StartDataCache()){
	global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache($cache_dir);
	
	if(!isset($CFClub)){
		require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');
		$CFClub = CFClub::getInstance();		
	}
		
	$arIds = $CFClub->getIdList(0, $_GET);
	
	$CACHE_MANAGER->RegisterTag("all_id_recipes_".SITE_ID."_".(intval($_GET['d']) > 0 ? intval($_GET['d']) : 0 )."_".(intval($_GET['k']) > 0 ? intval($_GET['k']) : 0 ));
	$CACHE_MANAGER->EndTagCache();

	$obCache->EndDataCache($arIds);
}else{
    $arIds = array();
}
	
/*if(SITE_ID == "s1"){
    if($arRecipes = apc_fetch('allRecipes_all')){
        $arRecipes = unserialize($arRecipes);
    }else{
	$arRecipes = $CFClub->getList($intCount, $_GET);
        apc_add('allRecipes_all', serialize($arRecipes), 86400);
    }
}elseif(SITE_ID == "fr"){
    if($arRecipes = apc_fetch('allRecipes_all_fr')){
        $arRecipes = unserialize($arRecipes);
    }else{
	$arRecipes = $CFClub->getList($intCount, array_merge($_GET,array("site_id"=>"fr")));
        apc_add('allRecipes_all_fr', serialize($arRecipes), 86400);
    }
}*/

$site_dir = SITE_DIR;	

/*if(SITE_ID == "s1"){
    if($arIds = apc_fetch('arIds_all')){
        $arIds = unserialize($arIds);
    }else{
	$arIds = $CFClub->getIdList(99999, $_GET);
        apc_add('arIds_all', serialize($arIds), 86400);
    }
}elseif(SITE_ID == "fr"){
    if($arIds = apc_fetch('arIds_all_fr')){
        $arIds = unserialize($arIds);
    }else{
	$arIds = $CFClub->getIdList(99999, array_merge($_GET,array("site_id"=>"fr")));
        apc_add('arIds_all_fr', serialize($arIds), 86400);
    }
}*/

$strRecipeHTML = '';
$JSallRecipesResult = "var allRecipesResult = [";

if(isset($_GET['k']) || isset($_GET['d'])){
	$strArt = (isset($_GET['k']) ? "DISH_TYPE" : "KITCHEN");
	
	$arNames['KITCHEN'] = $arKitch;
	$arNames['DISH_TYPE'] = $arDish;
	$notInclude = array();
	
	foreach($arRecipes['ITEMS'] as $arRecipe){
		$arResult[ $arRecipe['PROPERTY_'.$strArt.'_VALUE'] ][] = $arRecipe;
		$notInclude[] = $arRecipe["ID"];
	}
	foreach($arIds as $id){
		if(!in_array($id["ID"],$notInclude)){
			$JSallRecipesResult .= $id["ID"];
			$JSallRecipesResult .= ", ";
		}
	}
	$JSallRecipesResult = substr($JSallRecipesResult, 0, -2);
	$JSallRecipesResult .= "];";
	
	foreach($arResult as $strKey => $arItem){

		foreach($arItem as $arRecipe){
			$arRecipe['PROPERTY_COMMENT_COUNT_VALUE'] = IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']);
			if($arRecipe['CODE']){
			$strRecipeHTML .= 
<<<HTML
<div class="item recipe_list_item">
	<div class="photo">
		<a title="{$arRecipe['NAME']}" href="{$site_dir}detail/{$arRecipe['CODE']}/">
			<img width="170" alt="Фета, запеченная с помидорами и паприкой" src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" style="margin-top: -71px;">
		</a>
	</div>
	<h5><a href="{$site_dir}detail/{$arRecipe['CODE']}/">{$arRecipe['NAME']}</a></h5>
	<p class="author">От: {$arRecipe['USER']['LOGIN']}</p>
	<p class="info"><span title="Оставить отзыв" class="comments_icon"><noindex><a href="{$site_dir}detail/{$arRecipe['CODE']}/#comments">{$arRecipe['PROPERTY_COMMENT_COUNT_VALUE']}</a></noindex></span></p>
</div>
HTML;
}else{
	$strRecipeHTML .= 
<<<HTML
<div class="item recipe_list_item">
	<div class="photo">
		<a title="{$arRecipe['NAME']}" href="{$site_dir}detail/{$arRecipe['ID']}/">
			<img width="170" alt="Фета, запеченная с помидорами и паприкой" src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" style="margin-top: -71px;">
		</a>
	</div>
	<h5><a href="{$site_dir}detail/{$arRecipe['ID']}/">{$arRecipe['NAME']}</a></h5>
	<p class="author">От: {$arRecipe['USER']['LOGIN']}</p>
	<p class="info"><span title="Оставить отзыв" class="comments_icon"><noindex><a href="{$site_dir}detail/{$arRecipe['ID']}/#comments">{$arRecipe['PROPERTY_COMMENT_COUNT_VALUE']}</a></noindex></span></p>
</div>
HTML;
}
			}
		}
	} else {
		$notInclude = array();
		foreach($arRecipes['ITEMS'] as $arRecipe){
			if(strlen($arRecipe['USER']["NAME"]) > 0 && strlen($arRecipe['USER']["LAST_NAME"]) > 0){
		     	$name = $arRecipe['USER']["NAME"]." ".$arRecipe['USER']["LAST_NAME"];
		 	}else{
		 		$name = $arRecipe['USER']["LOGIN"];
		 	}			
			$arRecipe['PROPERTY_COMMENT_COUNT_VALUE'] = IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']);
			$notInclude[] = $arRecipe["ID"];
			if($arRecipe['CODE']){
			$strRecipeHTML .= 
<<<HTML
<div class="item recipe_list_item">
	<div class="photo">
		<a title="{$arRecipe['NAME']}" href="{$site_dir}detail/{$arRecipe['CODE']}/">
			<img width="170" alt="{$arRecipe['NAME']}" src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" style="margin-top: -71px;">
		</a>
	</div>
	<h5><a href="{$site_dir}detail/{$arRecipe['CODE']}/">{$arRecipe['NAME']}</a></h5>
	<p class="author">От: {$name}</p>
	<p class="info"><span title="Оставить отзыв" class="comments_icon"><noindex><a href="{$site_dir}detail/{$arRecipe['CODE']}/#comments">{$arRecipe['PROPERTY_COMMENT_COUNT_VALUE']}</a></noindex></span></p>
</div>
HTML;
}else{
	$strRecipeHTML .= 
<<<HTML
<div class="item recipe_list_item">
	<div class="photo">
		<a title="{$arRecipe['NAME']}" href="{$site_dir}detail/{$arRecipe['ID']}/">
			<img width="170" alt="{$arRecipe['NAME']}" src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" style="margin-top: -71px;">
		</a>
	</div>
	<h5><a href="{$site_dir}detail/{$arRecipe['ID']}/">{$arRecipe['NAME']}</a></h5>
	<p class="author">От: {$name}</p>
	<p class="info"><span title="Оставить отзыв" class="comments_icon"><noindex><a href="{$site_dir}detail/{$arRecipe['ID']}/#comments">{$arRecipe['PROPERTY_COMMENT_COUNT_VALUE']}</a></noindex></span></p>
</div>
HTML;
}
		}
		foreach($arIds as $id){
			if(!in_array($id["ID"],$notInclude)){
				$JSallRecipesResult .= $id["ID"];
				$JSallRecipesResult .= ", ";
			}
		}
		$JSallRecipesResult = substr($JSallRecipesResult, 0, -2);
		$JSallRecipesResult .= "];";
	}
	$c_arIds = count($arIds);
	
	$strTitle = "Последние 9 рецептов";
	if(IntVal($_GET['k']) > 0){$strTitle = $arKitch[IntVal($_GET['k'])]['NAME'];}elseif(IntVal($_GET['d']) > 0){$strTitle = $arDish[IntVal($_GET['d'])]['NAME'];}
	?>
	<div id="content" class="all_recipes">
		<h1>
			<span class="heading">Рецепты</span> <span class="choice"><noindex><a href="/profile/recipes/">Мои рецепты</a><a href="/profile/favorites/">Избранное</a></noindex><a class="add_recipe" href="/recipe/add/">Добавить рецепт</a></span>
			<div class="clear"></div>
		</h1>
		<div id="filter_recipes">
			<div class="item cuisine">
				<h5>Кухня</h5>
				<a class="frame_bg" href="#">
					<span class="left">
						<span class="right">
							<span class="bg"><span>Выберите</span></span>
						</span>
					</span>
					<span class="shutter"><span class="shutter_left"><span class="shutter_right"><span class="shutter_bg"></span></span></span></span>
				</a>
			</div>
			<div class="item dish">
				<h5>Тип блюда</h5>
				<a class="frame_bg" href="#">
					<span class="left">
						<span class="right">
							<span class="bg"><span>Выберите</span></span>
						</span>
					</span>
					<span class="shutter"><span class="shutter_left"><span class="shutter_right"><span class="shutter_bg"></span></span></span></span>
				</a>
			</div>
			<div class="item ingredient">
				<h5>Основной ингредиент</h5>
				<a class="frame_bg" href="#">
					<span class="left">
						<span class="right">
							<span class="bg"><span>Выберите</span></span>
						</span>
					</span>
					<span class="shutter"><span class="shutter_left"><span class="shutter_right"><span class="shutter_bg"></span></span></span></span>
				</a>
			</div>
			<script>
			var tagArray =[];
			<?PHP include($_SERVER["DOCUMENT_ROOT"]."/tags.php"); ?>
			</script>
			<div class="item tag">
				<h5>Метка</h5>
				<a class="frame_bg" href="#">
					<span class="left">
						<span class="right">
							<span class="bg"><span>Выберите</span></span>
						</span>
					</span>
					<span class="shutter"><span class="shutter_left"><span class="shutter_right"><span class="shutter_bg"></span></span></span></span>
				</a>
			</div>
			<div class="clear"></div>
			<div id="filter_lists">
				<div id="filter_list" style="display: none;" class=""></div>
			</div>
		</div>
		<div id="text_space">
			<div id="fc_statistics"><div class="wrapper"><span class="item"><span class="num"><?=$c_arIds?></span> <span class="word"><?=$Factory->plural_form(intval($c_arIds),array("рецепт","рецепта","рецептов"))?></span></span></div></div>
			<?if(strlen($JSallRecipesResult)):?>
				<script type="text/javascript">
					<?=$JSallRecipesResult?>
				</script>
			<?endif;?>
			<div id="recipe_feed_block">
				<?=$strRecipeHTML?>
				<div class="clear"></div>
			</div>
			<?if($c_arIds > 27):?>
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
			<div class="comments_feed">
				<?$APPLICATION->IncludeComponent("custom:blog.new_comments", ".default", array(
					"GROUP_ID" => "1",
					"BLOG_URL" => "",
					"COMMENT_COUNT" => "6",
					"MESSAGE_LENGTH" => "100",
					"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
					"PATH_TO_BLOG" => "/blogs/group/#blog#/blog/",
					"PATH_TO_POST" => "/blogs/group/#blog#/blog/#post_id#/",
					"PATH_TO_USER" => "/profile/#user_id#/",
					"PATH_TO_GROUP_BLOG_POST" => "",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "600",
					"PATH_TO_SMILE" => "",
					"BLOG_VAR" => "",
					"POST_VAR" => "",
					"USER_VAR" => "",
					"PAGE_VAR" => "",
					"SEO_USER" => "N"
					),
					false
				);?>
			</div>
		</div>
		<div class="clear"></div>
		<div style="margin: 45px 0;">
		<script charset="UTF-8" src="//www.travelpayouts.com/widgets/2e904aed9b11dc04a26ae5f66e4a9820.js?v=176"></script>
		</div>
		<?$APPLICATION->IncludeComponent("custom:foodshot.list", "foodshot", array(
	"IBLOCK_TYPE" => "foodshot",
	"IBLOCK_ID" => "25",
	"NEWS_COUNT" => "5",
	"SORT_BY1" => "timestamp_x",
	"SORT_ORDER1" => "DESC",
	"SORT_BY2" => "SORT",
	"SORT_ORDER2" => "ASC",
	"FILTER_NAME" => "",
	"FIELD_CODE" => array(
		0 => "CREATED_BY",
		1 => "",
	),
	"PROPERTY_CODE" => array(
		0 => "comments_count",
		1 => "likes_count",
		2 => "",
	),
	"CHECK_DATES" => "Y",
	"DETAIL_URL" => "",
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "N",
	"PREVIEW_TRUNCATE_LEN" => "",
	"ACTIVE_DATE_FORMAT" => "d.m.Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "N",
	"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
	"ADD_SECTIONS_CHAIN" => "N",
	"HIDE_LINK_WHEN_NO_DETAIL" => "N",
	"PARENT_SECTION" => "",
	"PARENT_SECTION_CODE" => "",
	"INCLUDE_SUBSECTIONS" => "Y",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "N",
	"PAGER_TITLE" => "Новости",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"DISPLAY_DATE" => "Y",
	"DISPLAY_NAME" => "Y",
	"DISPLAY_PICTURE" => "Y",
	"DISPLAY_PREVIEW_TEXT" => "Y",
	"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>

		<?if(strlen($strBanner_middle) > 0){?><div id="hor_banner"><?=$strBanner_middle?></div><?}?>
		<?$APPLICATION->IncludeComponent("custom:store.banner.horizontal", "", Array(),false);?>
		<div class="collection_block">
			<h2><span>Подборка</span></h2>
			<?
			if($obCache->InitCache(86400, "FooterBlock".SITE_ID, "/FooterBlock".SITE_ID)){
				$vars = $obCache->GetVars();
				$Themes = $vars["Themes"];
				$ResDump = $vars["ResDump"];
				$strBlockHTML = $vars["strBlockHTML"];
			}elseif($obCache->StartDataCache()){
				$rsThematicBlock = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
												Array("ACTIVE"=>"Y", "IBLOCK_CODE"=>"thematic_bloc", "PROPERTY_place_VALUE"=>"home"),
												false,
												false,
												Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_recipe", "PROPERTY_place")
											);
				$ResDump = Array();
				while($Bl = $rsThematicBlock->GetNext())
				{
					$ResDump[ $Bl['ID'] ][] = $Bl['PROPERTY_RECIPE_VALUE'];
					
					if(!isset( $Themes[ $Bl['ID'] ] ))
					{
						$Themes[ $Bl['ID'] ] = $Bl;
					}
				}
				$strBlockHTML = array();
				foreach($Themes as $Block){
					$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ID"=>$ResDump[ $Block['ID'] ]), false, false, Array("ID", "NAME", "CODE", "CREATED_BY", "PREVIEW_PICTURE", "PROPERTY_comment_count"));
					while($arRecipe = $rsRecipes->GetNext()){
						$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
						$arUser = $rsUser->Fetch();
						
						$arRecipe['USER'] = $arUser;
						
						//$strBlockHTML .= '<a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a>&nbsp;<span class="comments">(<a href="/detail/'.$arRecipe['ID'].'/#comments">'.IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a>)</span><span class="author">От: '.$arRecipe['USER']['LOGIN'].'</span>';
						$strBlockHTML[ $Block["ID"] ] .= '<div class="recipe_list_item">';
						if(intval($arRecipe["PREVIEW_PICTURE"]) > 0){
							$arFile = CFile::GetFileArray($arRecipe["PREVIEW_PICTURE"]);
							$strBlockHTML[ $Block["ID"] ] .= '<div class="photo"><a title="'.$arRecipe['NAME'].'" href="/detail/'.($arRecipe['CODE'] ? $arRecipe['CODE'] : $arRecipe['ID']).'/">';
							$strBlockHTML[ $Block["ID"] ] .= '<img width="150" alt="'.$arRecipe['NAME'].'" src="'.$arFile['SRC'].'"></a></div>';
						}
						$strBlockHTML[ $Block["ID"] ] .= '<h5><a href="/detail/'.($arRecipe['CODE'] ? $arRecipe['CODE'] : $arRecipe['ID']).'/">'.$arRecipe['NAME'].'</a></h5>';
						if(strlen($arRecipe['USER']["NAME"]) > 0 && strlen($arRecipe['USER']["LAST_NAME"]) > 0){
					     	$name = $arRecipe['USER']["NAME"]." ".$arRecipe['USER']["LAST_NAME"];
					 	}else{
					 		$name = $arRecipe['USER']["LOGIN"];
					 	}
						$strBlockHTML[ $Block["ID"] ] .= '<p class="author">От: '.$name.'</p>';
						$strBlockHTML[ $Block["ID"] ] .= '</div>';
					}
				}			
				$obCache->EndDataCache(array(
					"Themes" => $Themes,
					"ResDump" => $ResDump,
					"strBlockHTML" => $strBlockHTML
				));
			}else{
				$Themes = array();
				$ResDump = array();
				$strBlockHTML = array();
			}
			if($obCache->StartDataCache(86400, "FooterBlock_index_html".SITE_ID, "/footer_block".SITE_ID)){
				if(!empty($Themes) && !empty($strBlockHTML)){
					foreach($Themes as $Block){?>
						<div class="block">
							<h3><?=$Block['NAME']?></h3>
							<?=$strBlockHTML[ $Block["ID"] ]?>
							<div class="clear"></div>
						</div>
					<?}?>
				</div>
				<?}
				$obCache->EndDataCache();
			}?>
		</div>
	</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
