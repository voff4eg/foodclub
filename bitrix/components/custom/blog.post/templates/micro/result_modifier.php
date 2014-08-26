<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if ($arResult["BlogUser"]["AVATAR_file"] !== false)
{
	$arResult["BlogUser"]["AVATAR_file"] = CFile::ResizeImageGet(
					$arResult["BlogUser"]["AVATAR"],
					array("width" => 30, "height" => 30),
					BX_RESIZE_IMAGE_EXACT,
					false
				);
	if ($arResult["BlogUser"]["AVATAR_file"] !== false)
		$arResult["BlogUser"]["AVATAR_img"] = CFile::ShowImage($arResult["BlogUser"]["AVATAR_file"]["src"], 30, 30, "border=0 align='right'");
}
?>