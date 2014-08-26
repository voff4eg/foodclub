<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.header.php");

//ShowMessage($arParams["~AUTH_RESULT"]);

?>
<div id="form">
	<form name="forget" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
		<h1>Забыли пароль?</h1>
		<?if (strlen($arResult["BACKURL"]) > 0) { ?><input type='hidden' name='backurl' value='<?=$arResult["BACKURL"]?>' /><? } ?>
	
		<input type="hidden" name="AUTH_FORM" value="Y">
		<input type="hidden" name="TYPE" value="SEND_PWD">
		<div class="form_field">
			<h5>Логин <span>?</span></h5>
			<input type="text" name="USER_LOGIN" class="text" value="<?=$arResult["LAST_LOGIN"]?>" />
		</div>
		<div class="form_field">
			<h5>Или e-mail <span>?</span></h5>
			<input type="text" name="USER_EMAIL" class="text" />
		</div>
		<?if(strlen($arParams['~AUTH_RESULT']['MESSAGE']) > 0 || strlen($arResult['ERROR_MESSAGE']) > 0 ){?>
			<div class="result_message">
			<?if(strlen($arParams['~AUTH_RESULT']['MESSAGE']) > 0){echo "&mdash; ".$arParams['~AUTH_RESULT']['MESSAGE'];}?>
			<?if(strlen($arResult['ERROR_MESSAGE']) > 0){echo "&mdash; ".$arResult['ERROR_MESSAGE'];}?>
			</div>
		<?} //if?>
		<div class="submit">
			<button class="button" type="submit" style="width:270px; padding:0;">Отправить</button>
		</div>
	</form>
</div>
<div id="bottom" class="enter">

<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.footer.php"); die;?>