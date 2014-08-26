<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
?><script type="text/javascript">
if (typeof(phpVars) != "object")
	phpVars = {};
if (!phpVars.cookiePrefix)
	phpVars.cookiePrefix = '<?=CUtil::addslashes(COption::GetOptionString("main", "cookie_name", "BITRIX_SM"))?>';
if (!phpVars.titlePrefix)
	phpVars.titlePrefix = '<?=CUtil::addslashes(COption::GetOptionString("main", "site_name", $_SERVER["SERVER_NAME"]))?> - ';
if (!phpVars.messLoading)
	phpVars.messLoading = '<?=CUtil::addslashes(GetMessage("SONET_LOADING"))?>';
if (!phpVars.ADMIN_THEME_ID)
	phpVars.ADMIN_THEME_ID = '.default';
var photoVars = {'templatePath' : '/bitrix/components/bitrix/photogallery/templates/.default/'};
</script>