<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
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
	<h2><?=$arResult["FatalError"]?></h2>
	<p><a href="<?=$arResult['Urls']['Group']?>blog/">Вернуться</a></p>
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<h2><?=$arResult["ErrorMessage"]?></h2>
		<p><a href="<?=$arResult['Urls']['Group']?>blog/">Вернуться</a></p>
		<?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?>
		<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
			<table class="sonet-message-form data-table" cellspacing="0" cellpadding="0">
				<tr>
					<th colspan="2"><?= GetMessage("SONET_C39_T_PROMT") ?></th>
				</tr>
				<tr>
					<td valign="top" width="10%" nowrap><?= GetMessage("SONET_C39_T_GROUP") ?>:</td>
					<td valign="top">
						<b><?
						echo "<a href=\"".$arResult["Urls"]["Group"]."\">";
						echo $arResult["Group"]["NAME"];
						echo "</a>";
						?></b>
					</td>
				</tr>
				<tr>
					<td valign="top" nowrap><span class="required-field">*</span> <?= GetMessage("SONET_C39_T_MESSAGE") ?>:</td>
					<td valign="top"><textarea name="MESSAGE" style="width:98%" rows="5"><?= htmlspecialcharsex($_POST["MESSAGE"]); ?></textarea></td>
				</tr>
			</table>
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arResult["Group"]["ID"] ?>">
			<?=bitrix_sessid_post()?>
			<br />
			<input type="submit" name="save" value="<?= GetMessage("SONET_C39_T_SEND") ?>">
		</form>
		<?
	}
	else
	{
		?>
		<?if ($arResult["Group"]["OPENED"] == "Y"):?>
			<h2><?= GetMessage("SONET_C39_T_SUCCESS_ALT") ?></h2>
		<?else:?>
			<h2><?= GetMessage("SONET_C39_T_SUCCESS") ?></h2>
		<?endif;?>
		<p><a href="<?=$arResult['Urls']['Group']?>blog/">Спасибо, понятно</a></p>
		<?
	}
}
?>
</div>
