<?
require_once(dirname(__FILE__)."/../include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/prolog.php");
require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/wizard.php");

IncludeModuleLangFile(__FILE__);

function _DumpPostVars($vname, $vvalue, $var_stack=array())
{
	if (is_array($vvalue))
	{
		foreach($vvalue as $key=>$value)
			_DumpPostVars($key, $value, array_merge($var_stack ,array($vname)));
	}
	else
	{
		if(count($var_stack)>0)
		{
			$var_name=$var_stack[0];
			for($i=1; $i<count($var_stack);$i++)
				$var_name.="[".$var_stack[$i]."]";
			$var_name.="[".$vname."]";
		}
		else
			$var_name=$vname;

		if ($var_name != "sessid")
		{
			?><input type="hidden" name="<?echo htmlspecialchars($var_name)?>" value="<?echo htmlspecialchars($vvalue)?>"><?
		}
	}
}

if(!$USER->CanDoOperation('edit_php')):
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"), false, false);
elseif (!check_bitrix_sessid()):
?>

	<span style="color:red"><?=GetMessage("MAIN_WIZARD_INSTALL_SESSION_EXPIRED")?></span>
	<form action="<?=$APPLICATION->GetCurPageParam(bitrix_sessid_get(), Array("sessid"))?>" method="post">

	<?
		foreach($_POST as $name => $value)
		{
			if ($name == "USER_LOGIN" || $name == "USER_PASSWORD")
				continue;
			_DumpPostVars($name, $value);
		}
	?><br>
		<input type="submit" value="<?=GetMessage("MAIN_WIZARD_INSTALL_RELOAD_PAGE")?>">
	</form>

<?
else:
	$installer = new CWizard($_REQUEST["wizardName"]);
	$installer->Install();
endif;
?>