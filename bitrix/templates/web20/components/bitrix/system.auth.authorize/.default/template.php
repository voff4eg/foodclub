<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->RestartBuffer();
include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.header.php");
?>
<?
//ShowMessage($arParams["~AUTH_RESULT"]);
//ShowMessage($arResult['ERROR_MESSAGE']);
?>
			<div id="form">
				<div class="open_id act">
					<h2><a href="#" class="no_link">LiveJournal OpenID</a><span>LiveJournal OpenID</span></h2>
					
					<div class="fields">
						<div class="form_field">
							<h5>Имя пользователя <span>?</span></h5>
							<input type="text" class="text" value="">
						</div>
					</div>
					<form action="/auth/index.php" method="post" name="open_id">
					<input type="hidden" name="OPENID_IDENTITY" value="">
					</form>
				</div>
				<div class="authorization">
					<h2><a href="#" class="no_link">Авторизация</a><span>Авторизация</span></h2>
					<form action="/auth/index.php" method="post" name="authorization">
						<input type="hidden" name="AUTH_FORM" value="Y" />
						<input type="hidden" name="TYPE" value="AUTH" />
						<input type='hidden' name='backurl' value='<?$_SERVER['HTTP_REFERER']?>' />
						<div class="fields">
	
							<div class="form_field">
								<h5>Логин <span>?</span></h5>
								<input type="text" class="text" name="USER_LOGIN">
							</div>
							<div class="form_field">
								<h5>Пароль <span>?</span></h5>
								<input type="password" class="text" name="USER_PASSWORD">
	
							</div>
							<div class="clear"></div>
						</div>
					</form>
				</div>
				<?if(strlen(strlen($arParams['~AUTH_RESULT'])) > 0 || strlen(strlen($arResult['ERROR_MESSAGE'])) > 0 ){?>
					<div class="error_message">
					<?if(strlen($arParams['~AUTH_RESULT']) > 0){echo "&mdash; ".$arParams['~AUTH_RESULT'];}?>
					<?if(strlen($arResult['ERROR_MESSAGE']) > 0){echo "&mdash; ".$arResult['ERROR_MESSAGE'];}?>
					</div>
				<?} //if?>
				<div class="button_authorization">Войти</div>
			</div>
			
	<?include($_SERVER['DOCUMENT_ROOT']."/bitrix/templates/fclub/service.footer.php"); die;?>
