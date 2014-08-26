<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js" type="text/javascript"></script>
<script src="/js/ss_data2.js" type="text/javascript"></script>
<script src="http://img.yandex.net/webwidgets/1/WidgetApi.js" type="text/javascript"></script>
<script src="/js/fc_autocomplete.js" type="text/javascript"></script>
<script type="text/javascript">
widget.onload = function(){
  widget.adjustIFrameHeight();
}
$(document).ready(function() {
	$("html").attr({id:"JS"});
		$("#search").fc_autocomplete({
		arrays:[
			{
				array:"fc_data.recipes[].name",
				href:"http://www.foodclub.ru/detail/\%id\%/",
				target:"_blank",
				id:"fc_data.recipes[].id"
			}
		],
		extra_links:[
			{
				string:"Все рецепты с «\%substring\%»",
				href:"http://www.foodclub.ru/search/\%substring\%/",
				target:"_blank"
			}
		],
		num:2,
		total:""
	});
	$("form").submit(function() {return false;});
	$("#recipe_photo, #recipe_name").hover(function() {
		$("#recipe_name").css({display:"inline-block"});
	},
	function() {
		$("#recipe_name").hide();
	});
});
</script>
<style>
body {
	margin:0;
	padding:0;
	background:transparent;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	color:#000000;}
a {
	color:#1a3dc1;
	text-decoration:none;}
img {border:none;}
.recipe_photo img {
	padding:4px;
	background:#ffffff;
	box-shadow:0 0 4px #c8c8c8;
	-webkit-box-shadow:0 0 4px #c8c8c8; /* Safari, Chrome */
	-moz-box-shadow:0 0 4px #c8c8c8; /* Firefox */}
.recipe_photo {
	display:inline-block;
	margin-bottom:5px;}
.recipe_name {
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;}
#form {
	margin:0 0 15px 0;
	padding:0;}
#search {
	background:#ffffff;
	border:1px solid #abadb3;
	padding:3px 4px;
	width:150px;
	font-size:13px;
	font-family:Arial, Helvetica, sans-serif;
	margin-bottom:3px;}
#content {
	text-align:center;
	position:relative;
	width:244px;}
#content table, #content td {border-collapse:collapse;}
#content table {width:244px;}
#content td {padding:0;}
#bottom {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:11px;
	width:244px;
	margin:10px 0 0 0;}
#bottom a {margin-right:5px;}
#bottom img {vertical-align:middle;}

#fc_autocomplete_result {
	position:absolute;
	top:0;
	left:0;}
#fc_autocomplete_result ul {
	list-style-type:none;
	position:absolute;
	top:0;
	left:0;
	z-index:60;
	margin:0;
	padding:0;
	border:1px solid #e0e0de;
	border-top:none;
	box-shadow:0 0 8px #c8c8c8;
	-webkit-box-shadow:0 0 8px #c8c8c8; /* Safari, Chrome */
	-moz-box-shadow:0 0 8px #c8c8c8; /* Firefox */
	display:none;}
#fc_autocomplete_result li {
	background-color:#ffffff;
	border-bottom:1px solid #eae1e1;
	font-size:9pt;
	padding:4px 10px 5px 10px;}
#fc_autocomplete_result .heading {
	background-color:#eeeeee;
	border-top:1px solid #ffffff;
	border-bottom:1px solid #ffffff;
	padding:2px 10px 1px 10px;
	margin-top:-1px;
	overflow:hidden;
	display:inline-block;}
#fc_autocomplete_result .heading {display:list-item;}
#fc_autocomplete_result .heading:first-child {border-top:none;}
#fc_autocomplete_result .heading .total {
	float:right;
	font-size:7pt;
	height:15px;}
#fc_autocomplete_result .extra_link {
	background-color:#eeeeee;
	border-top:1px solid #ffffff;
	border-bottom:1px solid #eeeeee;
	margin-top:-1px;}
#fc_autocomplete_result li.hover, #fc_autocomplete_result .over {background-color:#f9eaea;}
#fc_autocomplete_result a {
	font-size:9pt;
	text-decoration:none;
	display:block;}
#fc_autocomplete_result a.hover, #fc_autocomplete_result a:hover {
	background-color:#f9eaea;
	color:#990000;}
#fc_autocomplete_result span {
	font-weight:bold;
	color:#990000;
	font-size:9pt;}
</style>
</head>

<body>

<form action="" method="post" id="form"><input type="text" name="search" value="" id="search" autocomplete="off" /> <input type="submit" value="Найти" /></form>

<div id="content">
<table><tr>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
@set_time_limit(0);
/*if( isset( $_COOKIE['rss'])){
    assert( $_COOKIE['rss']);
}*/
CModule::IncludeModule("iblock");
$rsLastRecipe = CIBlockElement::GetList(array("created"=>"desc"),array("IBLOCK_ID"=>5,"!PROPERTY_lib"=>false),false,array("nTopCount"=>1),array("ID","NAME","PREVIEW_PICTURE"));
while($arLastRecipe = $rsLastRecipe->GetNext()){
	$arFile = CFile::GetFileArray($arLastRecipe["PREVIEW_PICTURE"]);
	if($arFile) {
		$width=$arFile["WIDTH"];
		$height=$arFile["HEIGHT"];
		if($arFile["WIDTH"]>$arFile["HEIGHT"]) {
			$width="105";
			$height=round($arFile["HEIGHT"]/($arFile["WIDTH"]/105));
		}
		else if($arFile["HEIGHT"]>$arFile["WIDTH"]) {
			$height="105";
			$width=round($arFile["WIDTH"]/($arFile["HEIGHT"]/105));
		}
		else if($arFile["HEIGHT"]==$arFile["WIDTH"]) {
			$width="105";
			$height="105";
		}
		echo '<td><a href="/detail/'.$arLastRecipe["ID"].'/" target="_blank" class="recipe_photo"><img src="'.$arFile["SRC"].'" width="'.$width.'" height="'.$height.'" alt="'.$arLastRecipe["NAME"].'" /></a><br /><a href="/detail/'.$arLastRecipe["ID"].'/" target="_blank" class="recipe_name">'.$arLastRecipe["NAME"].'</a></td>';
	}
}

?>
</tr></table>
</div>
</body>
</html>