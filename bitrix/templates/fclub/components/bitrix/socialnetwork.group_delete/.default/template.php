<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="system_message">
	<div class="pointer"></div>
	<div class="padding">
<?
if ($arResult["NEED_AUTH"] == "Y")
{
	LocalRedirect('/auth/');;
}
elseif (strlen($arResult["FatalError"])>0)
{
	?>
	<h2><?=$arResult["FatalError"]?></h2>
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<h2><?=$arResult["ErrorMessage"]?></h2>
		<?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?>
		<h2><?= GetMessage("SONET_C9_SUBTITLE") ?></h2>
		</div>
	</div>
		<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arResult["Group"]["ID"] ?>">
			<?=bitrix_sessid_post()?>
			<input type="submit" name="save" value="<?= GetMessage("SONET_C9_DO_DEL") ?>">
			<input type="reset" name="cancel" value="<?= GetMessage("SONET_C9_DO_CANCEL") ?>" OnClick="window.location='<?= $arResult["Urls"]["Group"] ?>'">
		</form>
		<?
	}
	else
	{
		?>
		<h2><?= GetMessage("SONET_C9_SUCCESS") ?></h2>
		<p><a href="/blogs/">Спасибо, я понял</a></p>
		<?
	}
}
?>
</div>