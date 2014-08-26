<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<? 
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }
if(strlen(trim($arResult['REQUEST']['QUERY'])) > 0)
	$APPLICATION->SetTitle($arResult['REQUEST']['QUERY']);
else
	$APPLICATION->SetTitle("Поиск");
?>
<?if(count($arResult["SEARCH"])>0):
    CModule::IncludeModule("blog");
    CModule::IncludeModule("socialnetwork");
    
    foreach($arResult['SEARCH'] as $Item){
        if($Item['PARAM1'] == "POST"){
            $arPosts[] = substr($Item['ITEM_ID'],1);
			$BlogsId[] = $Item['PARAM2'];
        }
		elseif($Item['PARAM1'] == "COMMENT")
		{
			$arP = explode("|",$Item['PARAM2']);
			$BlogsId[] = $arP[0];		
		}
    };
	
	
    $Sort = Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC");
    $arFilter = Array(
	    "PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
	    "ID" => $arPosts,
        );
    $dbPosts = CBlogPost::GetList($Sort, $arFilter, false, Array("nPageSize"=>1000000));

    while ($arPost = $dbPosts->Fetch()){
        $Posts[ $arPost['ID'] ] = $arPost;
        $BlogsId[] = $arPost['BLOG_ID'];
    }
	$BlogsId = array_unique($BlogsId);
	sort($BlogsId);
    /*
     * Получение id групп в социальных сетях
     * $SocNetBlogs - массив, в котором - ключ:id блога, значение:id группы
     */
    $rsBlogs = CBlog::GetList(Array(), Array("ID"=>$BlogsId), false, false);
    while ($Blog = $rsBlogs->GetNext()) {
	    $SocNetBlogs[ $Blog['ID'] ] = $Blog['SOCNET_GROUP_ID'];
    }

	
    foreach($arResult['SEARCH'] as $Key=>$Item){

        if($Item['PARAM1'] == "POST"){
            $BlogId = $Posts[ substr($Item['ITEM_ID'],1) ]['BLOG_ID'];

            $SNId = $SocNetBlogs[ $BlogId ];
            $arResult['SEARCH'][ $Key ]['POST_URL'] = "/blogs/group/".$SNId."/blog/".substr($Item['ITEM_ID'],1)."/";
        }
        elseif($Item['PARAM1'] == "COMMENT")
        {
			$arP = explode("|",$Item['PARAM2']);
            $SNId = $SocNetBlogs[ $arP[0] ];
            $arResult['SEARCH'][ $Key ]['POST_URL'] = "/blogs/group/".$SNId."/blog/".$arP[1]."/#".substr($Item['ITEM_ID'],1);
        }
        elseif($Item['PARAM1'] == "BLOG")
        {
            $SNId = $SocNetBlogs[ substr($Item['ITEM_ID'],1) ];
            $arResult['SEARCH'][ $Key ]['POST_URL'] = "/blogs/group/".$SNId."/";
            $arResult['SEARCH'][ $Key ]['TITLE'] = substr($Item['TITLE'], 12);
        }
    }
    ?>
    <div id="content">
	    <div id="text_space">
            <h1>Вы искали: <?=$arResult['REQUEST']['QUERY']?></h1>
            <div class="search_switch">
	            <div class="item act"><span>В записях</span></div>
	            <div class="item"><a href="/search/<?=( strlen(trim($arResult['REQUEST']['QUERY'])) > 0 ? trim($arResult['REQUEST']['QUERY'])."/" : "" )?>">В рецептах</a></div>
	            <div class="clear"></div>
            </div>
    <?
    if( count($arResult['SEARCH']) > 0)
    {
        ?><div class="found_topics"><?
        foreach($arResult['SEARCH'] as $Item)
        {
            if( $Item['PARAM1'] == "POST"){?>
            <div class="item">
				<h2 class="h1"><a href="<?=$Item['POST_URL']?>"><?=$Item['TITLE']?></a></h2>
				<p><?=$Item['BODY_FORMATED']?></p>
			</div>
			<?} elseif($Item['PARAM1'] == "BLOG") {?>
            <div class="item blog">
				<h2>Клуб: <a href="<?=$Item['POST_URL']?>"><?=$Item['TITLE']?></a></h2>
			</div>

			<?} elseif($Item['PARAM1'] == "COMMENT") {?>
            <div class="item comment">
				<h2>Комментарий к записи <a href="<?=$Item['POST_URL']?>"><?=$Item['TITLE']?></a></h2>
				<p><?=$Item['BODY_FORMATED']?></p>
			</div>
            <?}?>
        <?
        }
        ?></div><?
    }
    else
    {
    
    }
    ?>
            <div class="clear"></div>
            <?=$arResult["NAV_STRING"];?>
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
            <?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
            <?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
        </div>
		<div class="clear"></div>
    </div>
    <?

else:?>
	<div id="content">
	    <div id="text_space">
	    	<?if(strlen(trim($arResult['REQUEST']['QUERY']))):?>            
            <h1>Вы искали: <?=trim($arResult['REQUEST']['QUERY'])?>. Но мы ничего не нашли</h1>
            <?else:?>
            <h1>Мы ничего не нашли</h1>
            <?endif;?>
            <div class="search_switch">
	            <div class="item act"><span>В записях</span></div>
	            <div class="item"><a href="/search/<?=( strlen(trim($arResult['REQUEST']['QUERY'])) > 0 ? trim($arResult['REQUEST']['QUERY'])."/" : "" )?>">В рецептах</a></div>
	            <div class="clear"></div>
            </div>
            <div class="clear"></div>
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
            <?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
            <?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
        </div>
		<div class="clear"></div>
    </div>
<?endif;?>

