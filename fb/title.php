<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?> 
<meta property="og:image" content="http://www.foodclub.ru/upload/iblock/05f/_end.jpg" />

<div class="b-footer-social">

<div class="b-footer-social__fb">
<fb:like href="http://www.foodclub.ru" send="false" width="450" show_faces="true" action="recommend"></fb:like>
</div>

<div class="b-footer-social__vk">
<div id="vk_like_fc"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like_fc", {

type: "full",
pageTitle: "Foodclub.ru — Кулинарные рецепты с пошаговыми фотографиями",
pageDescription: "Более 1000 кулинарных рецептов",
pageUrl: "www.foodclub.ru",
pageImage: "/images/foodclub_logo.gif",
text: ""
});
</script>
</div>

<div class="i-clearfix"></div>

</div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>