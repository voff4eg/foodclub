<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="keywords" content="">
<meta name="description" content="">
<title><?$APPLICATION->ShowTitle()?></title>
<link rel="stylesheet" type="text/css" href="/css/admin/styles.css">
<script src="/js/jquery-1.2.6.min.js" type="text/javascript"></script>
<script src="/js/admin/jscript.js" type="text/javascript"></script>

</head>

<body>
<?
global $USER;
if(!$USER->IsAuthorized()) 
	$APPLICATION->AuthForm("Доступ закрыт.");
?>
<div id="body"><div class="padding">
<div id="head">
	<div id="logo"><a href="/"><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Food club"></a></div>
	<div class="clear"></div>
</div>	

<div id="topbar">
	<div class="menu no_submenu">
		<div class="item"><a href="/admin/recipe/edit/">Рецепты</a></div>
		<div class="item"><a href="/admin/recipe/add/">Добавление рецепта</a></div>
	</div>
</div>


<div id="content">
<?require($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');?>
