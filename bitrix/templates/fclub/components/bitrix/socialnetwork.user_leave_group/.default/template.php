<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="system_message">
	<div class="pointer"></div>
	<div class="padding">
<?
if ($arResult["NEED_AUTH"] == "Y")
{
	$APPLICATION->AuthForm("");
}
elseif (strlen($arResult["FatalError"])>0)
{
	?>
	<?=$arResult["FatalError"]?>
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<?=$arResult["ErrorMessage"]?>
		<?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?>		
		<h2><?=GetMessage("SONET_C37_T_PROMT") ?></h2>
		</div>
	</div>
		<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arResult["Group"]["ID"] ?>">
			<?=bitrix_sessid_post()?>
			<input type="submit" class="button" name="save" value="<?= GetMessage("SONET_C37_T_SAVE") ?>">
			<input type="reset" class="button" name="cancel" value="<?= GetMessage("SONET_C37_T_CANCEL") ?>" OnClick="window.location='<?= $arResult["Urls"]["Group"] ?>'">
		</form>
		<?
	}
	else
	{
		LocalRedirect($arResult["Urls"]["Group"]);
	}
}
?>
</div>