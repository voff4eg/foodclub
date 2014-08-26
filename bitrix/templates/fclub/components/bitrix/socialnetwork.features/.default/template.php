<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="club_body">
<?
if ($arResult["NEED_AUTH"] == "Y")
{
	$APPLICATION->AuthForm("");
}
elseif (strlen($arResult["FatalError"]) > 0)
{
	?>
	<div class="system_message">
	<div class="pointer"></div>
	<div class="padding"><h2><?
	echo $arResult["FatalError"];
	?></h2></div>
	</div>
	<?
}
else
{
	if(strlen($arResult["ErrorMessage"])>0)
	{
		?>
		<div class="system_message">
		<div class="pointer"></div>
		<div class="padding"><h2><?
		echo $arResult["ErrorMessage"];
		?></h2></div>
		</div>
		<?
	}

	if ($arResult["ShowForm"] == "Input")
	{
		?>
		<h2>Настройка</h2>
		<form id="setup_form" method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
		<?foreach ($arResult["Features"] as $feature => $arFeature):?>
			<input type="hidden" id="<?= $feature ?>_active_id" name="<?= $feature ?>_active" value="Y">
						
			<?foreach ($arFeature["Operations"] as $operation => $perm):?>
				<div class="form_field">
					<h5><?= GetMessage("SONET_FEATURES_".$feature."_".$operation) ?>:</h5>
					<select style="width:300px" name="<?= $feature ?>_<?= $operation ?>_perm">
						<?foreach ($arResult["PermsVar"] as $key => $value):?>
							<option value="<?= $key ?>"<?= ($key == $perm) ? " selected" : "" ?>><?= $value ?></option>
						<?endforeach;?>
					</select>
				</div>
			<?endforeach;?>
					
		<?endforeach;?>
			<input type="hidden" name="SONET_USER_ID" value="<?= $arParams["USER_ID"] ?>">
			<input type="hidden" name="SONET_GROUP_ID" value="<?= $arParams["GROUP_ID"] ?>">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="save" value="<?= GetMessage("SONET_C4_SUBMIT") ?>">
			<div class="button">Сохранить</div>
		</form>
		<?
	}
	else
	{
		?>
		<?if ($arParams["PAGE_ID"] == "group_features"):?>
			<div class="system_message">
			<div class="pointer"></div>
			<div class="padding"><h2><?= GetMessage("SONET_C4_GR_SUCCESS") ?></h2>
			<a href="<?= $arResult["Urls"]["Group"] ?>">Спасибо, понятно</a></div>
			</div>
		<?else:?>
			<div class="system_message">
			<div class="pointer"></div>
			<div class="padding"><h2><?= GetMessage("SONET_C4_US_SUCCESS") ?></h2>
			<a href="<?= $arResult["Urls"]["User"] ?>"><?= $arResult["User"]["NAME"]." ".$arResult["User"]["LAST_NAME"]; ?></a></div>
			</div>
		<?endif;?>
		<?
	}
}
?>
</div>
