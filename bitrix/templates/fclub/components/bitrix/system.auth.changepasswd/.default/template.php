<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.header.php");

//ShowMessage($arParams["~AUTH_RESULT"]);
?>
<div id="form">
	<form action="<?=$arResult["AUTH_FORM"]?>" method="post" name="registration">
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="CHANGE_PWD">
	<?if (strlen($arResult["BACKURL"]) > 0) { ?><input type='hidden' name='backurl' value='<?=$arResult["BACKURL"]?>' /><? } ?>
		<h1>Смена пароля</h1>
		<div class="form_field">

			<h5>Логин <span>?</span></h5>
			<input type="text" class="text" name="USER_LOGIN" value="<?=$arResult["LAST_LOGIN"]?>">
		</div>
		<div class="form_field">
			<h5>Контрольная строка <span>?</span></h5>
			<input type="text" class="text" name="USER_CHECKWORD" value="<?=$arResult["USER_CHECKWORD"]?>">
		</div>

		<div class="two">
			<div class="form_field">
				<h5>Новый пароль <span>?</span></h5>
				<input type="password" class="text" name="USER_PASSWORD" value="<?=$arResult["USER_PASSWORD"]?>">
			</div>
			<div class="form_field">
				<h5>Подтверждение пароля <span>?</span></h5>
				<input type="password" class="text" name="USER_CONFIRM_PASSWORD" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>">
			</div>
			<div class="clear"></div>
		</div>
		<?if(strlen($arParams['~AUTH_RESULT']['MESSAGE']) > 0 || strlen($arResult['ERROR_MESSAGE']) > 0 ){?>
			<div class="error_message">
			<?if(strlen($arParams['~AUTH_RESULT']['MESSAGE']) > 0){echo "&mdash; ".$arParams['~AUTH_RESULT']['MESSAGE'];}?>
			<?if(strlen($arResult['ERROR_MESSAGE']) > 0){echo "&mdash; ".$arResult['ERROR_MESSAGE'];}?>
			</div>
		<?} //if?>
		<div class="submit">
			<button class="button" type="submit" style="width:270px; padding:0;">Сменить пароль</button>
		</div>
	</form>
</div>
<div id="bottom" class="enter">

<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.footer.php"); die;?>
