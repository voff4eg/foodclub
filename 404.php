<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Foodclub 404 ошибка");
?>
<div id="body" class="error">
	<div class="logo"><a href="/"><img src="/images/foodclub_logo.gif" width="143" height="102" alt="Foodclub"></a></div>
	<div class="content">
		<h1>404 ошибка</h1>
		<p>Лучше <a href="/">вернуться на главную</a> и повторить попытку.</p>
	</div>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>