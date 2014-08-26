<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="content">
	<div id="text_space">
		<h1>Кулинарные клубы</h1>
<?
if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }
global $USER;
$rsBlogs = CSocNetGroup::GetList(Array("NAME"=>"ASC"), Array("!CLOSED"=>"Y","ACTIVE"=>"Y"), false, false);
$rsRel = CSocNetUserToGroup::GetList(Array(), Array("USER_ID"=>$USER->GetID()), false, false);
$Groups = array();
while($rs = $rsRel->GetNext()){
    $Groups[] = CSocNetGroup::GetByID($rs['GROUP_ID']);
}
$myGroups = array();
foreach($Groups as $mygroup){
    $myGroups[] = $mygroup["ID"];
}
/*
 * Заготовка для вывода клубов по буквам
while ($arBlog = $rsBlogs->GetNext()) {
	$strFirst = substr( $arBlog['NAME'], 0, 1-strlen( $arBlog['NAME'] ) );
	$arBlogs[ $strFirst ][] = $arBlog;
}
ksort($arBlogs, SORT_STRING); reset($arBlogs);
*/
$arBlogs = array();
while ($arBlog = $rsBlogs->GetNext()) {
	$arBlogs[] = $arBlog;
}
//$intCellCount = floor(count($arBlogs)/3);
//echo $intCellCount;
/*for($j = 1; $j <= count($arBlogs) - $intCellCount*3; $j++) $arExtCount[ $j ] = "Y";

$cell = 0;
for($i = 1; $i <= 3; $i++){
	$arRows[$i] = '<div class="three_column"><ul class="clubs">';
	for($j = 0; $j < $intCellCount; $j++){
		$arRows[$i] .= "<li><a href='".str_replace("#group_id#", $arBlogs[ $cell ]['ID'], $arResult['PATH_TO_GROUP_BLOG'])."'>".$arBlogs[ $cell ]['NAME']."</a></li>";
		$cell++;
	}
	if(isset($arExtCount[ $i ])) {
		$arRows[$i] .= "<li><a href='".str_replace("#group_id#", $arBlogs[ $cell ]['ID'], $arResult['PATH_TO_GROUP_BLOG'])."'>".$arBlogs[ $cell ]['NAME']."</a></li>";
		$cell++;
	}
	$arRows[$i] .= '</ul></div>';
}*/
//echo"<br/>";print_r($arBlogs);echo"<br/>";
?>
<div id="clubs_list">
    <table>
<?
foreach($arBlogs as $key => $arItem){
if($key % 3 == 0):?><tr><?endif;?>
<td><?if(intval($arItem['IMAGE_ID']) > 0){$fileSRC = CFile::GetByID($arItem['IMAGE_ID'])->Fetch();}?>
    <div class="image"><a href="<?=str_replace("#group_id#", $arBlogs[ $key ]['ID'], $arResult['PATH_TO_GROUP_BLOG'])?>"><?if(intval($arItem['IMAGE_ID']) > 0):?><img src="/upload/<?=$fileSRC['SUBDIR']?>/<?=$fileSRC['FILE_NAME']?>" width="207"><?endif;?></a></div>
    <h2><a href="<?=str_replace("#group_id#", $arBlogs[ $key ]['ID'], $arResult['PATH_TO_GROUP_BLOG'])?>"><?=$arItem['NAME']?></a></h2>
    <div class="text">
        <?=(strlen($arItem['DESCRIPTION']) > 100 ? substr($arItem['DESCRIPTION'],0,100)."..." : $arItem['DESCRIPTION'] )?>
    </div>
    <div class="bar"><span class="members" title="Количество участников"><?=$arItem['NUMBER_OF_MEMBERS']?></span> <?if(!in_array($arBlogs[$key]['ID'],$myGroups)):?><a href="<?=str_replace("#group_id#", $arBlogs[ $key ]['ID'], $arResult['PATH_TO_USER_REQUEST_GROUP'])?>">Вступить</a><?endif;?></div>
</td><?$key+=1;
if($key % 3 == 0):?></tr><?endif;?>
<?$key-=1;}?>
    </table>
</div>

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
