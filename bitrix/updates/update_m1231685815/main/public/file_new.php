<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

//Functions
function BXCreateSection(&$fileContent, &$sectionFileContent, &$absoluteFilePath)
{
	//Check quota
	$quota = new CDiskQuota();
	if (!$quota->CheckDiskQuota(Array("FILE_SIZE" => strlen($fileContent) + strlen($sectionFileContent))))
	{
		$GLOBALS["APPLICATION"]->ThrowException($quota->LAST_ERROR, "BAD_QUOTA");
		return false;
	}

	//Create dir
	$success = @mkdir($absoluteFilePath, BX_DIR_PERMISSIONS);
	if (!$success)
	{
		$GLOBALS["APPLICATION"]->ThrowException(GetMessage("PAGE_NEW_FOLDER_CREATE_ERROR")."<br /> (".htmlspecialchars($absoluteFilePath).")", "DIR_NOT_CREATE");
		return false;
	}

	//Create .section.php
	if (!$GLOBALS["APPLICATION"]->SaveFileContent($absoluteFilePath."/.section.php", $sectionFileContent))
		return false;

	//Create index.php
	if (!$GLOBALS["APPLICATION"]->SaveFileContent($absoluteFilePath."/index.php", $fileContent))
		return false;

	return true;
}

//2 wizards: create page and create section
$createNewFolder = (isset($_REQUEST["newFolder"]) && $_REQUEST["newFolder"] == "Y");

if ($createNewFolder)
	IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/public/folder_new.php");
else
	IncludeModuleLangFile(__FILE__);

$popupWindow = new CJSPopup(GetMessage("PAGE_NEW_WINDOW_TITLE"));

//Check fileman permissions
$canEditNewPage = true;
if (IsModuleInstalled("fileman"))
{
	if ($createNewFolder && (!$USER->CanDoOperation("fileman_admin_folders") || !$USER->CanDoOperation("fileman_admin_files")) )
		$popupWindow->ShowError(GetMessage("PAGE_NEW_ACCESS_DENIED"));
	elseif (!$USER->CanDoOperation("fileman_admin_files"))
		$popupWindow->ShowError(GetMessage("PAGE_NEW_ACCESS_DENIED"));

	if (!$USER->CanDoOperation("fileman_edit_existent_files"))
		$canEditNewPage = false;
}

//Page path
$path = "/";
if (isset($_REQUEST["path"]) && strlen($_REQUEST["path"]) > 0)
{
	$path = $_REQUEST["path"];
	$path = Rel2Abs("/", $path);
}

$documentRoot = CSite::GetSiteDocRoot($site);

//Site ID
$site = SITE_ID;
if (isset($_REQUEST["site"]) && strlen($_REQUEST["site"]) > 0)
{
	$obSite = CSite::GetByID($_REQUEST["site"]);
	if ($arSite = $obSite->Fetch())
		$site = $_REQUEST["site"];
}

//Check permissions
if ($createNewFolder && (!$USER->CanDoFileOperation("fm_create_new_folder", Array($site, $path)) || !$USER->CanDoFileOperation("fm_create_new_file", Array($site, $path))) )
	$popupWindow->ShowError(GetMessage("PAGE_NEW_ACCESS_DENIED"));
elseif (!$USER->CanDoFileOperation("fm_create_new_file", Array($site, $path)))
	$popupWindow->ShowError(GetMessage("PAGE_NEW_ACCESS_DENIED"));
elseif (!is_dir($documentRoot.$path))
	$popupWindow->ShowError(GetMessage("PAGE_NEW_FOLDER_NOT_FOUND")." (".htmlspecialchars($path).")");

if (!$USER->CanDoFileOperation("fm_edit_existent_file", Array($site, $path)))
	$canEditNewPage = false;

//Lang
if (!isset($_REQUEST["lang"]) || strlen($_REQUEST["lang"]) <= 0)
	$lang = LANGUAGE_ID;

//BackUrl
$back_url = (isset($_REQUEST["back_url"]) ? $_REQUEST["back_url"] : "");

//Template ID
$templateID = false;
if (isset($_REQUEST["templateID"]) && strlen($_REQUEST["templateID"]) > 0)
{
	$obTemplate = CSiteTemplate::GetByID($_REQUEST["templateID"]);
	if ($arSiteTemplate = $obSite->Fetch())
		$templateID = $_REQUEST["templateID"];
}

if ($templateID === false)
{
	//Get default template if templateID not set
	$obTemplate = CSite::GetTemplateList($site);
	while ($arSiteTemplate = $obTemplate->Fetch())
	{
		if (strlen($arSiteTemplate["CONDITION"]) <=0 )
		{
			$templateID = $arSiteTemplate["TEMPLATE"];
			break;
		}
	}
}

$strWarning = "";

//Check post values
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_REQUEST["save"]))
{
	CUtil::JSPostUnescape();

	$fileName = (isset($_REQUEST["fileName"]) ? $_REQUEST["fileName"] : "");
	$pageTitle = (isset($_REQUEST["pageTitle"]) ? $_REQUEST["pageTitle"] : "");
	$editAfterSave = (isset($_REQUEST["editAfterSave"]) && $_REQUEST["editAfterSave"] == "Y");

	$addToMenu = (isset($_REQUEST["addToMenu"]) && $_REQUEST["addToMenu"] == "Y");
	$menuName = (isset($_REQUEST["menuName"]) ? $_REQUEST["menuName"] : "");
	$menuType = (isset($_REQUEST["menuType"]) ? $_REQUEST["menuType"] : false);

	$pageTags = (isset($_REQUEST["pageTags"]) ? $_REQUEST["pageTags"] : false);
	$menuItemPosition = (isset($_REQUEST["menuItemPosition"]) ? intval($_REQUEST["menuItemPosition"]) : 0);

	$absoluteFilePath = $documentRoot.rtrim($path, "/")."/".$fileName;

	//Check filename
	if (strlen($fileName) <= 0)
		$strWarning = GetMessage("PAGE_NEW_FILE_NAME_EMPTY");
	elseif (preg_match("/[^a-zA-Z0-9\s!\$&\(\)\[\]\{\}\-.;=@\^_\~]/is", $fileName))
		$strWarning = GetMessage("PAGE_NEW_FILE_NAME_VALID_SYMBOLS");
	elseif (!$createNewFolder && !preg_match("/\.php$/", $fileName))
		$strWarning = GetMessage("PAGE_NEW_FILE_NAME_PHP_EXT");
	elseif (!$createNewFolder && $fileName[0] == ".")
		$strWarning = GetMessage("PAGE_NEW_FILE_NAME_DOT_FIRST");
	elseif (file_exists($absoluteFilePath))
	{
		if (is_dir($absoluteFilePath))
			$strWarning = GetMessage("PAGE_NEW_FOLDER_EXISTS");
		else
			$strWarning = GetMessage("PAGE_NEW_FILE_EXISTS");
	}

	if (!check_bitrix_sessid())
		$strWarning = GetMessage("MAIN_SESSION_EXPIRED");
}
else
{
	//Default form values
	$currentFileDir = $documentRoot.rtrim($path, "/")."/";
	for ($fileNumber = ""; ; $fileNumber++)
	{
		$fileName = ($createNewFolder ? "folder".$fileNumber : "page".$fileNumber.".php");

		if (!file_exists($currentFileDir.$fileName))
			break;
	}

	$pageTitle = GetMessage("PAGE_NEW_DEFAULT_TITLE").($fileNumber != "" ? " (".$fileNumber.")": "");
	$editAfterSave = true;

	$addToMenu = true;
	$menuName = "";
	$menuType = false;
	$menuItemPosition = -1;

	$pageTags = "";
}

//Get Menu
$arMenu = Array();
if (IsModuleInstalled("fileman") && $USER->CanDoOperation("fileman_add_element_to_menu"))
{
	$arMenuTypes = GetMenuTypes($site);
	foreach ($arMenuTypes as $type => $name)
	{
		$menu = new CMenu($type);
		$menu->Init($path);

		$actualDir = $menu->MenuDir;
		$currentDir = rtrim($path, "/")."/";

		if (strlen($actualDir) > 0)
		{
			$actualMenuFile = $actualDir.".".$type.".menu.php";
			$fileOperation = (file_exists($documentRoot.$actualMenuFile) ? "fm_edit_existent_file" : "fm_create_new_file" );
			if (!$USER->CanDoFileOperation("fm_add_to_menu", Array($site, $actualMenuFile)) || !$USER->CanDoFileOperation($fileOperation, Array($site, $actualMenuFile)))
				$actualDir = false;
		}
		else
			$actualDir = false;

		$currentMenuFile = $currentDir.".".$type.".menu.php";
		$fileOperation = (file_exists($documentRoot.$currentMenuFile) ? "fm_edit_existent_file" : "fm_create_new_file" );
		if (!$USER->CanDoFileOperation('fm_add_to_menu', Array($site, $currentMenuFile)) || !$USER->CanDoFileOperation($fileOperation, Array($site, $currentMenuFile)))
			$currentDir = false;

		if ($currentDir === false && $actualDir === false)
			continue;

		$arMenuItems = Array();
		foreach ($menu->arMenu as $arItem)
		{
			if (isset($arItem[0]))
				$arMenuItems[] = $arItem[0];
		}

		$arMenu[$type] = Array(
			"NAME" => $name,
			"ACTUAL_FILE" => ($actualDir === false ? $currentMenuFile : $actualMenuFile),
			"CURRENT_FILE" => $currentMenuFile,
			"CAN_CREATE_NEW" => ($actualDir !== false && $currentDir !== false && $actualDir != $currentDir),
			"ITEMS" => $arMenuItems,
			"TEMPLATE" => $menu->template
		);
	}
}
//echo "<pre>";print_r($arMenu); echo "</pre>";

//Save page settings
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_REQUEST["save"]) && $strWarning == "")
{
	CUtil::JSPostUnescape();

	//Get page template content
	$fileContent = false;
	$arPageTemplates = GetFileTemplates($lang, Array($templateID));
	if (is_array($arPageTemplates) && isset($arPageTemplates[0]) && isset($arPageTemplates[0]["file"]))
		$fileContent = GetTemplateContent($arPageTemplates[0]["file"], $lang, Array($templateID));

	if (!$fileContent)
		$fileContent = '<'.'?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>Text here...<'.'?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>';

	//Title
	$strSectionName = "";
	if (strlen($pageTitle) > 0)
	{
		$fileContent = SetPrologTitle($fileContent, $pageTitle);
		if ($createNewFolder)
			$strSectionName = "\$sSectionName = \"".EscapePHPString($pageTitle)."\";\n";
	}

	//Properties
	$strDirProperties = "";
	if (isset($_POST["PROPERTY"]) && is_array($_POST["PROPERTY"]))
	{
		$bNeedComma = false;
		foreach ($_POST["PROPERTY"] as $arProperty)
		{
			$arProperty["CODE"] = (isset($arProperty["CODE"]) ? trim($arProperty["CODE"]) : "");
			$arProperty["VALUE"] = (isset($arProperty["VALUE"]) ? trim($arProperty["VALUE"]) : "");

			if (preg_match("/[a-zA-Z_-~]+/i", $arProperty["CODE"]))
			{
				if ($createNewFolder && strlen($arProperty["VALUE"]) > 0)
				{
					if($bNeedComma)
						$strDirProperties .= ",\n";

					$strDirProperties .= "   \"".EscapePHPString($arProperty["CODE"])."\" => \"".EscapePHPString($arProperty["VALUE"])."\"";
					$bNeedComma = true;
				}
				else
					$fileContent = SetPrologProperty($fileContent, $arProperty["CODE"], $arProperty["VALUE"]);
			}
		}
	}

	if ($createNewFolder)
	{
		$sectionFileContent = "<"."?\n".$strSectionName."\$arDirProperties = Array(\n".$strDirProperties."\n);\n"."?".">";
		$success = BXCreateSection($fileContent, $sectionFileContent, $absoluteFilePath);
	}
	else
	{
		//Tags only for pages
		if ($pageTags !== false && IsModuleInstalled("search"))
			$fileContent = SetPrologProperty($fileContent, COption::GetOptionString("search", "page_tag_property","tags"), $pageTags);

		$success = $APPLICATION->SaveFileContent($absoluteFilePath, $fileContent);
	}

	if ($success === false && ($exception = $APPLICATION->GetException()))
		$strWarning = $exception->msg;
	else
	{
		$relativePath = rtrim($path, "/")."/".$fileName.($createNewFolder ? "/" : "");

		//Save menu
		if ($addToMenu && array_key_exists($menuType, $arMenu) && CModule::IncludeModule("fileman"))
		{
			$menuFile = $arMenu[$menuType]["ACTUAL_FILE"];
			$arResult = CFileMan::GetMenuArray($documentRoot.$menuFile);
			$arMenuItems = $arResult["aMenuLinks"];
			$menuTemplate = $arResult["sMenuTemplate"];

			if ($menuItemPosition > count($arMenuItems))
				$menuItemPosition = 0;

			//move to the right all items greater current item
			for ($i = count($arMenuItems) - 1; $i >= $menuItemPosition; $i--)
				$arMenuItems[$i+1] = $arMenuItems[$i];

			$arMenuItems[$menuItemPosition] = Array($menuName, $relativePath, Array(), Array(), "");
			CFileMan::SaveMenu(Array($site, $menuFile), $arMenuItems, $menuTemplate);
		}

		$pathToEdit = $relativePath.($createNewFolder ? "index.php" : "");
		if (!$USER->CanDoFileOperation("fm_edit_existent_file", Array($site, $pathToEdit)))
			$canEditNewPage = false;

		//Open edit dialog
		if ($editAfterSave && $canEditNewPage): 
		?>
			<script>
				jsPopup.ShowDialog("/bitrix/admin/public_file_edit.php?lang=<?=$lang?>"+(jsUtils.IsEditor() ? "" : "&noeditor=Y")+"&path=<?=urlencode($pathToEdit)?>&site=<?=$site?>&back_url=<?=urlencode($relativePath)?>", {width: 780, height: 570, resize: false});
				jsUtils.addCustomEvent("OnBeforeCloseDialog_", function(){window.location = "<?=CUtil::JSEscape($relativePath)?>";});
			</script>
		<?
		else:
			$popupWindow->Close($bReload = true, $relativePath);
		endif;
		die();
	}
}

//Properties from fileman settings
$arFilemanProperties = Array();
if (CModule::IncludeModule("fileman") && is_callable(Array("CFileMan", "GetPropstypes")))
	$arFilemanProperties = CFileMan::GetPropstypes($site);

//Properties from page
$arDirProperties = Array();
if ($strWarning != "" && isset($_POST["PROPERTY"]) && is_array($_POST["PROPERTY"]))
{
	//Restore post values if error occured
	foreach ($_POST["PROPERTY"] as $arProperty)
	{
		if (isset($arProperty["VALUE"]) && strlen($arProperty["VALUE"]) > 0)
			$arDirProperties[$arProperty["CODE"]] = $arProperty["VALUE"];
	}
}
else
{
	$arPageSlice = ParseFileContent($fileContent);
	$arDirProperties = $arPageSlice["PROPERTIES"];
}

//All file properties. Includes properties from root folders
$arInheritProperties = $APPLICATION->GetDirPropertyList(Array($site, $path)); 
if ($arInheritProperties === false)
	$arInheritProperties = Array();

//Tags
if (!$createNewFolder && IsModuleInstalled("search"))
{
	$tagPropertyCode = COption::GetOptionString("search", "page_tag_property","tags");
	unset($arFilemanProperties[$tagPropertyCode]);
	unset($arDirProperties[$tagPropertyCode]);
	unset($arInheritProperties[strtoupper($tagPropertyCode)]);
}

$bSearchExists = (isset($tagPropertyCode) && CModule::IncludeModule("search"));

//Delete equal properties
$arGlobalProperties = Array();
foreach ($arFilemanProperties as $propertyCode => $propertyDesc)
{
	if (array_key_exists($propertyCode, $arDirProperties))
		$arGlobalProperties[$propertyCode] = $arDirProperties[$propertyCode];
	else
		$arGlobalProperties[$propertyCode] = "";

	unset($arDirProperties[$propertyCode]);
	unset($arInheritProperties[strtoupper($propertyCode)]);
}

foreach ($arDirProperties as $propertyCode => $propertyValue)
	unset($arInheritProperties[strtoupper($propertyCode)]);

$bPropertyExists = (!empty($arGlobalProperties) || !empty($arDirProperties) || !empty($arInheritProperties));


//HTML Output
$popupWindow->ShowTitlebar(GetMessage("PAGE_NEW_WINDOW_TITLE"));
$popupWindow->StartDescription($createNewFolder ? "bx-create-new-folder" : "bx-create-new-page");

if (isset($strWarning) && $strWarning != "")
	$popupWindow->ShowValidationError($strWarning);
?>

<p><?=GetMessage("PAGE_NEW_SUB_TITLE")?> <b><?=htmlspecialchars($path)?></b></p>

<?if (IsModuleInstalled("fileman")):?>
	<?if ($createNewFolder):?>
		<p><a href="/bitrix/admin/fileman_newfolder.php??lang=<?=urlencode($lang)?>&site=<?=urlencode($site)?>&path=<?=urlencode($path)?>&back_url=<?=urlencode($back_url)?>"><?=GetMessage("PAGE_NEW_EDIT_IN_ADMIN")?></a></p>
	<?else:?>
		<p><a href="/bitrix/admin/fileman_html_edit.php?lang=<?=urlencode($lang)?>&site=<?=urlencode($site)?>&path=<?=urlencode($path)?>&new=Y&back_url=<?=urlencode($back_url)?>"><?=GetMessage("PAGE_NEW_EDIT_IN_ADMIN")?></a></p>
	<?endif?>
<?endif?>

<?
$popupWindow->EndDescription();
$popupWindow->StartContent();
?>

<table class="bx-width100" id="bx_new_page_common">
	<tr>
		<td class="bx-popup-label bx-width30"><?=GetMessage("PAGE_NEW_FILENAME")?>:</td>
		<td><input type="text" style="width:90%;" id="bx_new_page_name" name="fileName" value="<?=htmlspecialcharsEx($fileName)?>" onblur="BXCheckFileName(this, <?=($createNewFolder ? "true" : "false")?>);" onkeypress="BXCheckFileName(this, <?=($createNewFolder ? "true" : "false")?>);" onkeyup="BXCheckFileName(this, <?=($createNewFolder ? "true" : "false")?>);"><font id="bx_error_text"></font></td>
	</tr>
	<tr>
		<td class="bx-popup-label bx-width30"><?=GetMessage("PAGE_NEW_PAGE_NAME")?>:</td>
		<td><input type="text" style="width:90%;" id="bx_page_title" name="pageTitle" value="<?=htmlspecialcharsEx($pageTitle)?>"></td>
	</tr>

	<?if (IsModuleInstalled("fileman") && $canEditNewPage):?>
	<tr>
		<td class="bx-popup-label bx-width30"></td>
		<td><input type="checkbox" id="bx_edit_after_save" name="editAfterSave" value="Y" <?=($editAfterSave ? "checked": "")?>> <label for="bx_edit_after_save"><?=GetMessage("PAGE_NEW_EDIT_PAGE")?></label></td>
	</tr>
	<?endif?>

	<?if (!empty($arMenu)):?>
	<tr>
		<td class="bx-popup-label bx-width30"></td>
		<td><input type="checkbox" id="bx_add_to_menu" name="addToMenu" value="Y" onclick="BXAddMenuStep(this.checked)" <?=($addToMenu ? "checked" : "")?>> <label for="bx_add_to_menu"><?=GetMessage("PAGE_NEW_ADD_MENU")?></label>
		</td>
	</tr>
	<?endif?>
</table>

<?if (!empty($arMenu)):?>
<table id="bx_new_page_menu" class="bx-width100" style="display:none;">
	<tr>
		<td class="bx-popup-label bx-width30"><?=GetMessage("PAGE_NEW_MENU_NAME")?>:</td>
		<td><input type="text" style="width:90%;" id="bx_menu_name" name="menuName" value="<?=htmlspecialcharsEx($menuName);?>"></td>
	</tr>
	<tr>
		<td class="bx-popup-label bx-width30"><?=GetMessage("PAGE_NEW_MENU_TYPE")?>:</td>
		<td>
			<select id="bx_menu_type" id="menuType" name="menuType" style="width:50%" onchange="BXChangeMenuType(this.options[this.selectedIndex].value, true)">
			<?foreach ($arMenu as $type => $arMenuProp):?>
				<option value="<?=htmlspecialchars($type)?>" <?=($menuType == $type ? "selected" : "")?>><?=htmlspecialcharsEx($arMenuProp["NAME"])?></option>
			<?endforeach?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="bx-popup-label bx-width30" valign="top"><?=GetMessage("PAGE_NEW_MENU_POSITION")?>:</td>
		<td>
				<select name="menuItemPosition" id="bx_menu_items" style="width:50%"></select><input type="hidden" id="bx_menu_position" value="<?=$menuItemPosition?>" />
		</td>
	</tr>
</table>
<?endif?>

<?
$jsInheritPropIds = "var jsInheritProps = [";

if ($bPropertyExists || $bSearchExists):?>

<table class="bx-width100" id="bx_new_page_prop" style="display:none;">
<?if ($bPropertyExists):?>
	<tr class="section">
		<td colspan="2">
			<table cellspacing="0">
				<tr>
					<td><?=GetMessage("PAGE_NEW_PROPERTIES")?></td>
					<td id="bx_page_prop_name">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
<?endif?>

<?
$propertyIndex = 0;

foreach ($arGlobalProperties as $propertyCode => $propertyValue):?>

	<tr style="height:30px;">
		<td class="bx-popup-label bx-width30"><?=(
			strlen($arFilemanProperties[$propertyCode]) > 0 ? 
				htmlspecialcharsEx($arFilemanProperties[$propertyCode]) : 
				htmlspecialcharsEx($propertyCode))
		?>:</td>
		<td>

		<?$inheritValue = $APPLICATION->GetDirProperty($propertyCode, Array($site, $path));?>

		<?if (strlen($inheritValue) > 0 && strlen($propertyValue) <= 0):
			$jsInheritPropIds .= ",".$propertyIndex;
		?>

			<input type="hidden" name="PROPERTY[<?=$propertyIndex?>][CODE]" value="<?=htmlspecialcharsEx($propertyCode)?>" /> 

			<div id="bx_view_property_<?=$propertyIndex?>" style="overflow:hidden;padding:2px 12px 2px 2px; border:1px solid white; width:90%; cursor:text; box-sizing:border-box; -moz-box-sizing:border-box;background-color:transparent; background-position:right; background-repeat:no-repeat;" onclick="BXEditProperty(<?=$propertyIndex?>)" onmouseover="this.style.borderColor = '#434B50 #ADC0CF #ADC0CF #434B50';" onmouseout="this.style.borderColor = 'white'" class="edit-field"><?=htmlspecialcharsEx($inheritValue)?></div>

			<div id="bx_edit_property_<?=$propertyIndex?>" style="display:none;"></div>

		<?else:?>

			<input type="text" name="PROPERTY[<?=$propertyIndex?>][VALUE]" value="<?=htmlspecialcharsEx($propertyValue)?>" style="width:90%;"><input type="hidden" name="PROPERTY[<?=$propertyIndex?>][CODE]" value="<?=htmlspecialcharsEx($propertyCode)?>" />

		<?endif?>
		</td>
	</tr>

<?$propertyIndex++; endforeach;?>

<?foreach ($arInheritProperties as $propertyCode => $propertyValue): $jsInheritPropIds .= ",".$propertyIndex;?>

	<tr style="height:30px;">
		<td class="bx-popup-label bx-width30"><?=htmlspecialcharsEx($propertyCode)?>:</td>
		<td>

			<input type="hidden" name="PROPERTY[<?=$propertyIndex?>][CODE]" value="<?=htmlspecialcharsEx($propertyCode)?>" /> 

			<div id="bx_view_property_<?=$propertyIndex?>" style="overflow:hidden;padding:2px 12px 2px 2px; border:1px solid white; width:90%; cursor:text; box-sizing:border-box; -moz-box-sizing:border-box;background-color:transparent; background-position:right; background-repeat:no-repeat;" onclick="BXEditProperty(<?=$propertyIndex?>)" onmouseover="this.style.borderColor = '#434B50 #ADC0CF #ADC0CF #434B50'" onmouseout="this.style.borderColor = 'white'" class="edit-field"><?=htmlspecialcharsEx($propertyValue)?></div>

			<div id="bx_edit_property_<?=$propertyIndex?>" style="display:none;"></div>

		</td>
	</tr>

<?$propertyIndex++; endforeach; ?>

<?foreach ($arDirProperties as $propertyCode => $propertyValue):?>

		<tr id="bx_user_property_<?=$propertyIndex?>">
			<td class="bx-popup-label bx-width30"><?=htmlspecialcharsEx(ToUpper($propertyCode))?><input type="hidden" name="PROPERTY[<?=$propertyIndex?>][CODE]" value="<?=htmlspecialcharsEx(ToUpper($propertyCode))?>" />:</td>
			<td><input type="text" name="PROPERTY[<?=$propertyIndex?>][VALUE]" value="<?=htmlspecialcharsEx($propertyValue)?>" style="width:90%;"></td>
		</tr>

<?
$propertyIndex++; 
endforeach;
?>

<?if ($bSearchExists):?>
	<tr class="empty">
		<td colspan="2"><div class="empty"></div></td>
	</tr>
	<tr class="section">
		<td colspan="2">
			<table cellspacing="0">
				<tr>
					<td><?=GetMessage("PAGE_NEW_TAGS_NAME")?></td>
					<td id="bx_page_tags">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="bx-popup-label bx-width30"><?=GetMessage("PAGE_NEW_TAGS")?>:</td>
		<td><?=InputTags("pageTags", $pageTags, array($site), 'style="width:90%;"');?></td>
	</tr> 
<?endif?>

</table>

<?
endif;//$bPropertyExists || $bSearchExists
$jsInheritPropIds .= "];";
?>
<input type="hidden" name="save" value="Y" />
<?
$popupWindow->EndContent();
$popupWindow->StartButtons();
?>
<input name="btn_popup_prev" id="btn_popup_prev" type="button" value="<?=GetMessage("PAGE_NEW_BUTTON_PREV")?>" title="<?=GetMessage("PAGE_NEW_BUTTON_PREV")?>" />
<input name="btn_popup_next" id="btn_popup_next" type="button" value="<?=GetMessage("PAGE_NEW_BUTTON_NEXT")?>" title="<?=GetMessage("PAGE_NEW_BUTTON_NEXT")?>" />
<input name="btn_popup_finish" id="btn_popup_finish" type="button" value="<?=GetMessage("PAGE_NEW_BUTTON_FINISH")?>" title="<?=GetMessage("PAGE_NEW_BUTTON_FINISH")?>" />
&nbsp;&nbsp;&nbsp;&nbsp;<input name="btn_popup_close" type="button" value="<?=GetMessage("PAGE_NEW_BUTTON_CANCEL")?>" onclick="jsPopup.CloseDialog()" title="<?=GetMessage("PAGE_NEW_BUTTON_CANCEL")?>" />

<?$popupWindow->EndButtons();?>

<script>

var bxMenuType = <?=CUtil::PhpToJSObject($arMenu)?>;

window.BXChangeMenuType = function(menuType, onChange)
{
	if (!bxMenuType[menuType])
		return;

	var menuItems = document.getElementById("bx_menu_items");
	if (!menuItems)
		return;

	menuItems.options.length = 0;

	//Create options list
	var selectDocument = menuItems.ownerDocument;
	if (!selectDocument)
		selectDocument = menuItems.document;

	for (var itemPosition = 0, length = bxMenuType[menuType]["ITEMS"].length; itemPosition < length; itemPosition++)
	{
		var option = selectDocument.createElement("OPTION");
		option.text = bxMenuType[menuType]["ITEMS"][itemPosition];
		option.value = itemPosition;
		menuItems.options.add(option);
	}

	var option = selectDocument.createElement("OPTION");
	option.text = "<?=GetMessage("PAGE_NEW_NEW_ITEM")?>";
	option.value = itemPosition;
	menuItems.options.add(option);

	var menuItemPosition = document.getElementById("bx_menu_position");
	if ( (onChange && onChange == true) || (menuItemPosition && menuItemPosition.value < 0) )
		menuItems.selectedIndex = menuItems.options.length - 1;
	else if (menuItemPosition)
		menuItems.selectedIndex = menuItemPosition.value;
}

window.BXFirstStepNext = function(wizard)
{
	var addToMenu = document.getElementById("bx_add_to_menu");

	if (!addToMenu || !addToMenu.checked)
	{
		wizard.SetCurrentStep("bx_new_page_prop");
		return;
	}

	//Set item name equal title
	var menuName = document.getElementById("bx_menu_name");
	var pageTitle = document.getElementById("bx_page_title");

	if (!menuName || !pageTitle)
		return;

	if (menuName.value == "" || menuName.disabled)
		menuName.value = pageTitle.value;
}

window.BXFirstStepShow = function(wizard)
{
	var addToMenu = document.getElementById("bx_add_to_menu");

	if (addToMenu && addToMenu.checked)
	{
		wizard.SetButtonDisabled("next", false);
		wizard.SetButtonDisabled("finish", true);
	}
	else
	{
		if (!wizard.IsStepExists("bx_new_page_prop") && wizard.IsStepExists("bx_new_page_menu"))
			wizard.SetButtonDisabled("next", true);
	}
}

window.BXAddMenuStep = function(addStep)
{
	if (addStep)
	{
		var menuType = document.getElementById("bx_menu_type");
		if (menuType)
			BXChangeMenuType(menuType.options[menuType.selectedIndex].value);

		if (window.bxNewPageWizard)
		{
			window.bxNewPageWizard.SetButtonDisabled("finish", true);
			window.bxNewPageWizard.SetButtonDisabled("next", false);
		}
	}
	else
	{
		if (window.bxNewPageWizard)
		{
			window.bxNewPageWizard.SetButtonDisabled("finish", false);

			if (!window.bxNewPageWizard.IsStepExists("bx_new_page_prop") && window.bxNewPageWizard.IsStepExists("bx_new_page_menu"))
				window.bxNewPageWizard.SetButtonDisabled("next", true);
		}
	}
}

BXAddMenuStep(<?=($addToMenu ? "true" : "false")?>);

window.BXThirdStepPrev = function(wizard)
{
	var addToMenu = document.getElementById("bx_add_to_menu");

	if (!addToMenu || !addToMenu.checked)
	{
		wizard.SetCurrentStep("bx_new_page_menu");
		return;
	}
}

window.BXCheckFileName = function(input, createNewFolder)
{
	var onSaveCheck = false;
	if (!input)
	{
		var input = document.getElementById("bx_new_page_name");
		onSaveCheck = true;
	}

	if (!input)
		return false;

	fileName = input.value;
	var errorBox = document.getElementById("bx_error_text");
	var validSymbols = /[^a-zA-Z0-9\s\!\$\&\(\)\[\]\{\}\-\.\;\=\@\^_\~]/;
	var phpExtension = /\.php$/;

	function SetError(errorText)
	{
		input.style.color = "red";
		if (errorBox)
		{
			errorBox.style.display = "block";
			errorBox.innerHTML = errorText;
			//errorBox.style.color = "red";
			errorBox.className = "errortext";
		}
	}

	if (onSaveCheck && fileName == "")
	{
		SetError("<?=GetMessage("PAGE_NEW_FILE_NAME_EMPTY")?>");
		return false;
	}
	else if (!createNewFolder && onSaveCheck && !phpExtension.test(fileName))
	{
		SetError("<?=GetMessage("PAGE_NEW_FILE_NAME_PHP_EXT")?>");
		return false;
	}
	else if (!createNewFolder && fileName != "" && fileName.substr(0,1) == ".")
	{
		SetError("<?=GetMessage("PAGE_NEW_FILE_NAME_DOT_FIRST")?>");
		return false;
	}
	else if (fileName != "" && validSymbols.test(fileName))
	{
		SetError("<?=GetMessage("PAGE_NEW_FILE_NAME_VALID_SYMBOLS")?>");
		return false;
	}
	else
	{
		input.style.color = "";
		if (errorBox)
		{
			errorBox.style.display = "none";
			errorBox.innerHTML = "";
			errorBox.style.color = "";
		}
		return true;
	}
}

//Save
window.BXNewPageSave = function(wizard)
{
	if (BXCheckFileName(null, <?=($createNewFolder ? "true" : "false")?>))
		jsPopup.PostParameters();
	else
		wizard.SetCurrentStep("bx_new_page_common");
}

window.BXFileNameSelect = function()
{
	var input = document.getElementById("bx_new_page_name");
	if (input)
	{
		input.focus();
		input.select();
	}
}

BXFileNameSelect();

//Edit properties
window.BXBlurProperty = function(element, propertyIndex)
{
	var viewProperty = document.getElementById("bx_view_property_" + propertyIndex);

	if (element.value == "" || element.value == viewProperty.innerHTML)
	{
		var editProperty = document.getElementById("bx_edit_property_" + propertyIndex);

		viewProperty.style.display = "block";
		editProperty.style.display = "none";

		while (editProperty.firstChild)
			editProperty.removeChild(editProperty.firstChild);
	}
}

window.BXEditProperty = function(propertyIndex)
{
	if (document.getElementById("bx_property_input_" + propertyIndex))
		return;

	var editProperty = document.getElementById("bx_edit_property_" + propertyIndex);
	var viewProperty = document.getElementById("bx_view_property_" + propertyIndex);

	viewProperty.style.display = "none";
	editProperty.style.display = "block";

	var input = document.createElement("INPUT");

	input.type = "text";
	input.name = "PROPERTY["+propertyIndex+"][VALUE]";
	input.style.width = "90%";
	input.style.padding = "2px";
	input.id = "bx_property_input_" + propertyIndex;
	input.onblur = function () {BXBlurProperty(input,propertyIndex)};
	input.value = viewProperty.innerHTML;

	editProperty.appendChild(input);
	input.focus();
	input.select();
}

//Create hints
window.BXFolderEditHint = function()
{
	var td = document.getElementById("bx_page_prop_name");
	if (td)
	{
		oBXHint = new BXHint("<?=GetMessage("PAGE_NEW_DESCRIPTION")?>");
		td.appendChild(oBXHint.oIcon);
	}

	var td = document.getElementById("bx_page_tags");
	if (td)
	{
		oBXHint = new BXHint("<?=GetMessage("PAGE_NEW_TAGS_DESCIPTION")?>");
		td.appendChild(oBXHint.oIcon);
	}

	<?=$jsInheritPropIds?>
	
	for (var index = 0; index < jsInheritProps.length; index++)
		oBXHint = new BXHint("<?=GetMessage("PAGE_NEW_INHERIT_TITLE")?>", document.getElementById("bx_view_property_"+ jsInheritProps[index]), {"width":200});
}
window.BXFolderEditHint();

//Wizard
window.bxNewPageWizard = new jsWizard;
window.bxNewPageWizard.AddStep("bx_new_page_common", {"next":"<?=(!empty($arMenu) ? "bx_new_page_menu" : "bx_new_page_prop")?>", "finish":"bx_new_page_common", "onfinish" : BXNewPageSave, "onnext" : BXFirstStepNext, "onshow" : BXFirstStepShow});
window.bxNewPageWizard.AddStep("bx_new_page_menu", {"prev":"bx_new_page_common", "next":"bx_new_page_prop", "finish":"bx_new_page_menu", "onfinish" : BXNewPageSave});
window.bxNewPageWizard.AddStep("bx_new_page_prop", {"prev": "<?=(!empty($arMenu) ? "bx_new_page_menu" : "bx_new_page_common")?>", "finish":"bx_new_page_prop", "onfinish" : BXNewPageSave, "onprev" : BXThirdStepPrev});
window.bxNewPageWizard.Display();
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>