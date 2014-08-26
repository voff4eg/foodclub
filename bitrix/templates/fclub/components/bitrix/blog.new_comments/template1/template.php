<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	require_once($_SERVER["DOCUMENT_ROOT"].'/classes/comment.class.php');
	CModule::IncludeModule("iblock");
	$obComment = CFClubComment::getInstance();
	if($arComments = $obComment->getLastReplies($arParams["COMMENT_COUNT"])){
	echo "<h3>Последние отзывы</h3>";
	foreach($arComments as $arComment){
?>
	
	<div class="item">	
		<div class="comment">
			<a href="/detail/<?=$arComment['PROPERTY_RECIPE_VALUE']?>/?ID=<?=$arComment['ID']?>#<?=$arComment['ID']?>"><?if(strlen($arComment['~PREVIEW_TEXT']) > 100){echo substr($arComment['~PREVIEW_TEXT'], 0 ,100)."...";}else{echo $arComment['~PREVIEW_TEXT'];}?></a>
		</div>
		<div class="info">
			<a href="/profile/<?=$arComment["USER"]["ID"]?>/"><?=$arComment["USER"]["LOGIN"]?></a>
			<span class="date"><?=$arComment['DATE_CREATE']?></span>
		</div>
	</div>			
<?
		}
	}
?>

