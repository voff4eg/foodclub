<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="auxiliary_block last_comments">
	<h3>Последние комментарии</h3>
<?
//echo "<pre>"; print_r($arResult); echo "</pre>";
foreach($arResult as $arComment)
{
	?><div class="item">
		<div class="comment">
			<a href="/blogs/group/<?=$arComment['BLOG_SOCNET_GROUP_ID']?>/blog/<?=$arComment['POST_ID']?>/<?=$arComment['urlToComment']?>"><?=$arComment['TEXT_FORMATED']?></a>
		</div>
		<div class="info">
			<a href="/profile/<?=$arComment['arUser']['ID']?>/"><?=$arComment['arUser']['LOGIN']?></a>
			<span class="date"><?=$arComment['DATE_CREATE_FORMATED']?></span>
		</div>
	</div><? 
}
?>	
</div>