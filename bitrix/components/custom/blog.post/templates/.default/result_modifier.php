<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if ($arResult["bSoNet"] && $arResult["BlogUser"]["AVATAR_file"] !== false)
{
	unset($arResult["BlogUser"]["AVATAR_img"]);
}
?>