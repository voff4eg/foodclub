<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");  

if (CModule::IncludeModule("advertising")){
	$strBanner_right = CAdvBanner::Show("right_banner");
	$strBanner_middle = CAdvBanner::Show("middle_banner");
}

$APPLICATION->SetPageProperty("title", "Кулинарные рецепты с фотографиями этапов приготовления. Foodclub.ru");
$APPLICATION->SetPageProperty("description", "Кулинарные рецепты со всего света. Удобный поиск, пошаговые фотографии кулинарных рецептов.");
$APPLICATION->SetPageProperty("keywords", "кулинарные рецепты, рецепт, кулинария, фото рецепты, рецепты с фотографиями, фото блюд");

CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/factory.class.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');
$CFClub = new CFClub;
$CFactory = new CFactory;

$obCache = new CPHPCache;
if($obCache->InitCache(3600, "RecipesCountMain".SITE_ID, "/RecipesCountMain".SITE_ID)){
	$count = $obCache->GetVars();
}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/RecipesCountMain");
	$count = $CFClub->getRecipesCount();
	$CACHE_MANAGER->RegisterTag("RecipesCountMainTag");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($count);
}else{
	$count = 0;
}
if($obCache->InitCache(900, "getOnlinecnt".SITE_ID, "/getOnlinecnt".SITE_ID)){
	$online = $obCache->GetVars();
}elseif($obCache->StartDataCache()){
	$online = $CFClub->getOnlineCount();
	$obCache->EndDataCache($online);
}else{
	$online = 0;
}
//Список записей блогов
if($obCache->InitCache(86400, "arBlogs_index".SITE_ID, "/blogs_index".SITE_ID)){
	$vars = $obCache->GetVars();
	$arBlogs = $vars["arBlogs"];
	$arPosts = $vars["arPosts"];
}elseif($obCache->StartDataCache()){
	$dbPosts = CBlogPost::GetList(  Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC"), 
									Array("PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH, /*"BLOG_ID" => array_keys($arBlogs),*/
									"<=DATE_PUBLISH"=>ConvertTimeStamp(date(), "FULL"),
									),
									false,
									Array('nPageSize'=>5)
								 );
								 
	while($arPost = $dbPosts->Fetch()){
		if(intval($arPost["BLOG_ID"]) > 0 && !isset($arBlogs[ $arPost["BLOG_ID"] ])){
			if($arBlog = CBlog::GetByID($arPost["BLOG_ID"])){
				$arBlogs[ $arBlog["ID"] ] = $arBlog;
			}
		}
		$dbCategory = CBlogPostCategory::GetList(Array("NAME" => "ASC"), Array("BLOG_ID" => $arPost["BLOG_ID"], "POST_ID" => $arPost["ID"]));
		while($arCategory = $dbCategory->GetNext()){
			$arCatTmp = $arCategory;
			$arCatTmp["urlToCategory"] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/?category=".$arCatTmp['CATEGORY_ID'];
			$arPost["CATEGORY"][] = $arCatTmp;
		}
									
		$arPost['urlToDelete'] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['ID']."/blog/?del_id=".$arPost['ID'];
		$res = CBlogImage::GetList(array("ID"=>"ASC"), array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arPost['BLOG_ID']));
		
		while ($arImage = $res->Fetch())
		    $arImages[$arImage['ID']] = $arImage['FILE_ID'];
		
		$parser = new blogTextParser;
		$arParserParams = Array(
			"imageWidth" => "465",
			"imageHeight" => "600",
		);
		
		$arAllow = array("HTML" => "N", "ANCHOR" => "Y", "BIU" => "Y", "IMG" => "Y", "QUOTE" => "Y", "CODE" => "Y", "FONT" => "Y", "LIST" => "Y", "SMILES" => "Y", "NL2BR" => "N", "VIDEO" => "Y");
		
		$arPost["TEXT_FORMATTED"] = $parser->convert($arPost['DETAIL_TEXT'], true, $arImages, $arAllow, $arParserParams);
		if (preg_match("/(\[CUT\])/i",$arPost['DETAIL_TEXT']) || preg_match("/(<CUT>)/i",$arPost['DETAIL_TEXT']))
			$arPost["CUT"] = "Y";
	    
		$rsUser = CUser::GetByID($arPost['AUTHOR_ID']);
		$arUser = $rsUser->Fetch();
		$arPost["arUser"] = $arUser;
		
		if(intval($arUser['PERSONAL_PHOTO']) > 0){
			$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
			$arAvatar = $rsAvatar->Fetch();			
			$arPost["arUser"]["avatar"] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
		} else {
			$arPost["arUser"]["avatar"] = "/images/avatar/avatar_small.jpg";
		}
		
		$arPost["DATE_FORMATTED"] = explode(" ", $arPost['DATE_PUBLISH']);
		
		$arPosts[] = $arPost;
	}
	$obCache->EndDataCache(array(
		"arBlogs" => $arBlogs,
		"arPosts" => $arPosts
	));
}else{
	$arBlogs = array();
	$arPosts = array();
}
unset($arPost);
?>
<div id="fc_statistics">
	<div class="wrapper">
		<span class="item">Добавлено <span class="num"><?=$count?></span> <a href="/recipes/"><?=$CFactory->plural_form($count, array("рецепт","рецепта","рецептов"));?></a></span><span class="sep"></span><span class="item"><span class="num"><?=$online?></span> <?=$CFactory->plural_form($online, array("кулинар","кулинара","кулинаров"));?> сейчас на сайте</span>
	</div>
</div>
<?if(strlen($strBanner_middle) > 0){?><div id="hor_banner"><?=$strBanner_middle?></div><?}?>
<div id="content">
	<div id="feed_space">
		<div class="choice">
			<noindex>
			<a href="/blogs/">Все клубы</a>
			<?if($USER->IsAuthorized()){?>
				<a href="/profile/lenta/">Моя лента</a>
			<?}?>
			</noindex>
		</div>
		<h2>Темы, обсуждаемые в клубах</h2>
<?if($obCache->StartDataCache(86400, "arBlogs_index_html", "/blogs_index".SITE_ID)){
	if(!empty($arPosts) && !empty($arBlogs)){
		foreach($arPosts as $i => $arPost):?>
		<?if($i < 2){?>
		<div class="topic_item">
			<div class="chain_path"><a href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']?>/blog/"><?=str_replace("Блог группы ","",$arBlogs[ $arPost['BLOG_ID'] ]['NAME'])?></a></div>			
			<h3><a class="heading" href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/".$arPost['ID']?>/"><?=$arPost['TITLE']?></a></h3>
			<div class="text"><?=$arPost["TEXT_FORMATTED"]?>
			<?if($arPost['CUT'] == "Y"){?>
				<div class="fcut"><a href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/".$arPost['ID']?>/#cut">Подробнее</a></div>
			<?}?>
			</div>
			<?if(isset($arPost['CATEGORY'])){?>
				<div class="tags">
					<h6>Метки</h6>
					<?foreach($arPost['CATEGORY'] as $Item){?>
						<a href="<?=$Item['urlToCategory']?>"><?=$Item['NAME']?></a>
					<?}?>
				</div>
			<?}?>
			<div class="bar">
				<span class="comments_icon" title="Комментировать"><noindex>
					<a href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/".$arPost['ID']?>/#00"><?=(intval($arPost['NUM_COMMENTS']) > 0 ? intval($arPost['NUM_COMMENTS']) : "" )?></a>
				</noindex></span>
				<span class="author">
					<noindex>
						<span class="photo">&nbsp;<a href="/profile/<?=$arPost["arUser"]['ID']?>/">
							<img src="<?=$arPost["arUser"]['avatar']?>" width="30" height="30" alt="<?=$arPost["arUser"]['LOGIN']?>" />
						</a></span>
						<?if(strlen($arPost["arUser"]["NAME"]) > 0 && strlen($arPost["arUser"]["LAST_NAME"]) > 0){
			             	$name = $arPost["arUser"]["NAME"]." ".$arPost["arUser"]["LAST_NAME"];
		             	}else{
		             		$name = $arPost["arUser"]["LOGIN"];
		             	}?>
						<a href="/profile/<?=$arPost["arUser"]['ID']?>/"><?=$name?></a>
					</noindex>
				</span>
				<span class="date"><?=CFactory::humanDate($arPost["DATE_FORMATTED"][0])?> <?=$arPost["DATE_FORMATTED"][1]?></span>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<?}else{?>
			<div class="topic_item_brief">
				<div class="bar">
					<span class="comments_icon" title="Комментировать"><noindex>
						<a href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/".$arPost['ID']?>/#00"><?=(intval($arPost['NUM_COMMENTS']) > 0 ? intval($arPost['NUM_COMMENTS']) : "" )?></a>
					</noindex></span>
				</div>
				<h3><a class="heading" href="/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/".$arPost['ID']?>/"><?=$arPost['TITLE']?></a></h3>
				<div class="clear"></div>
			</div>
		<?}		
		endforeach;?>
	<?}
	$obCache->EndDataCache();?>
<?}?>
	</div>
	<div id="cooks_space">
<?$APPLICATION->IncludeComponent("custom:foodshot.list", "one_foodshot", array(
	"IBLOCK_TYPE" => "foodshot",
	"IBLOCK_ID" => "25",
	"NEWS_COUNT" => "1",
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
	"CACHE_TIME" => "360000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
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
		<div id="cooks_feed">
			<?$APPLICATION->IncludeComponent("custom:topcook.list", "topcook.index.new", Array(
				"DISPLAY_DATE" => "Y",	// Выводить дату элемента
				"DISPLAY_NAME" => "Y",	// Выводить название элемента
				"DISPLAY_PICTURE" => "Y",	// Выводить изображение для анонса
				"DISPLAY_PREVIEW_TEXT" => "Y",	// Выводить текст анонса
				"AJAX_MODE" => "N",	// Включить режим AJAX
				"IBLOCK_TYPE" => "news",	// Тип информационного блока (используется только для проверки)
				"IBLOCK_ID" => "",	// Код информационного блока
				"NEWS_COUNT" => "20",	// Количество новостей на странице
				"SORT_BY1" => "ACTIVE_FROM",	// Поле для первой сортировки новостей
				"SORT_ORDER1" => "DESC",	// Направление для первой сортировки новостей
				"SORT_BY2" => "SORT",	// Поле для второй сортировки новостей
				"SORT_ORDER2" => "ASC",	// Направление для второй сортировки новостей
				"FILTER_NAME" => "",	// Фильтр
				"FIELD_CODE" => "",	// Поля
				"PROPERTY_CODE" => "",	// Свойства
				"CHECK_DATES" => "Y",	// Показывать только активные на данный момент элементы
				"DETAIL_URL" => "",	// URL страницы детального просмотра (по умолчанию - из настроек инфоблока)
				"PREVIEW_TRUNCATE_LEN" => "",	// Максимальная длина анонса для вывода (только для типа текст)
				"ACTIVE_DATE_FORMAT" => "d.m.Y",	// Формат показа даты
				"SET_TITLE" => "Y",	// Устанавливать заголовок страницы
				"SET_STATUS_404" => "N",	// Устанавливать статус 404, если не найдены элемент или раздел
				"INCLUDE_IBLOCK_INTO_CHAIN" => "Y",	// Включать инфоблок в цепочку навигации
				"ADD_SECTIONS_CHAIN" => "Y",	// Включать раздел в цепочку навигации
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",	// Скрывать ссылку, если нет детального описания
				"PARENT_SECTION" => "",	// ID раздела
				"PARENT_SECTION_CODE" => "",	// Код раздела
				"CACHE_TYPE" => "A",	// Тип кеширования
				"CACHE_TIME" => "36000000",	// Время кеширования (сек.)
				"CACHE_NOTES" => "",
				"CACHE_FILTER" => "N",	// Кэшировать при установленном фильтре
				"CACHE_GROUPS" => "Y",	// Учитывать права доступа
				"DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
				"DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
				"PAGER_TITLE" => "Новости",	// Название категорий
				"PAGER_SHOW_ALWAYS" => "Y",	// Выводить всегда
				"PAGER_TEMPLATE" => "",	// Название шаблона
				"PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
				"PAGER_SHOW_ALL" => "Y",	// Показывать ссылку "Все"
				"AJAX_OPTION_SHADOW" => "Y",	// Включить затенение
				"AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
				"AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
				"AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
				"AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
				),
				false
			);?>
		</div>
	</div>
	<div id="banner_space" class="index">
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
		<?if(strlen($strBanner_right) > 0){?><div class="banner"><h5>Реклама</h5><?=$strBanner_right?></div><?}?>
		<div class="comments_feed">
			<?$APPLICATION->IncludeComponent("custom:blog.new_comments", ".default", array(
	"GROUP_ID" => "1",
	"BLOG_URL" => "",
	"COMMENT_COUNT" => "10",
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
				$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ID"=>$ResDump[ $Block['ID'] ]), false, false, Array("ID", "NAME", "CREATED_BY", "PREVIEW_PICTURE", "PROPERTY_comment_count"));
				while($arRecipe = $rsRecipes->GetNext()){
					$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
					$arUser = $rsUser->Fetch();
					
					$arRecipe['USER'] = $arUser;
					
					//$strBlockHTML .= '<a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a>&nbsp;<span class="comments">(<a href="/detail/'.$arRecipe['ID'].'/#comments">'.IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a>)</span><span class="author">От: '.$arRecipe['USER']['LOGIN'].'</span>';
					$strBlockHTML[ $Block["ID"] ] .= '<div class="recipe_list_item">';
					if(intval($arRecipe["PREVIEW_PICTURE"]) > 0){
						$arFile = CFile::GetFileArray($arRecipe["PREVIEW_PICTURE"]);
						$strBlockHTML[ $Block["ID"] ] .= '<div class="photo"><a title="'.$arRecipe['NAME'].'" href="/detail/'.$arRecipe['ID'].'/">';
						$strBlockHTML[ $Block["ID"] ] .= '<img width="150" alt="'.$arRecipe['NAME'].'" src="'.$arFile['SRC'].'"></a></div>';
					}
					$strBlockHTML[ $Block["ID"] ] .= '<h5><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a></h5>';
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
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
