<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/photogallery.interface/templates/.default/script.js"></script>');
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/photogallery/templates/.default/script.js"></script>');
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/js/main/utils.js"></script>', true);
IncludeAJAX();
$GLOBALS['APPLICATION']->AddHeadString('<script src="/bitrix/components/bitrix/search.tags.input/templates/.default/script.js"></script>');
$GLOBALS['APPLICATION']->AddHeadString('<link href="/bitrix/components/bitrix/search.tags.input/templates/.default/style.css" type="text/css" rel="stylesheet" />');

/*************************************************************************
	Processing of received parameters
*************************************************************************/
$arParams["SHOW_TAGS"] = ($arParams["SHOW_TAGS"] == "Y" ? "Y" : "N");
$arResult["SHOW"]["TAGS"] = (($arResult["SHOW"]["TAGS"] == "Y" && $arParams["SHOW_TAGS"] == "Y") ? "Y" : "N");
$arParams["JPEG_QUALITY1"] = intVal($arParams["JPEG_QUALITY1"]) > 0 ? intVal($arParams["JPEG_QUALITY1"]) : 80;
$arParams["JPEG_QUALITY2"] = intVal($arParams["JPEG_QUALITY2"]) > 0 ? intVal($arParams["JPEG_QUALITY2"]) : 90;
$arParams["JPEG_QUALITY"] = intVal($arParams["JPEG_QUALITY"]) > 0 ? intVal($arParams["JPEG_QUALITY"]) : 90;
if (is_array($arParams["WATERMARK_COLORS"]) && !empty($arParams["WATERMARK_COLORS"]))
{
	$arr = $arParams["WATERMARK_COLORS"];
	$arParams["WATERMARK_COLORS"] = array();
	foreach ($arr as $key)
	{
		if (!empty($key))
			$arParams["WATERMARK_COLORS"][$key] = (strLen(GetMessage("P_COLOR_".strToUpper($key))) > 0 ? GetMessage("P_COLOR_".strToUpper($key)) : "#".strToUpper($key));
	}
}
else 
{
	$arParams["WATERMARK_COLORS"] = array(
			"FF0000" => GetMessage("P_COLOR_FF0000"), 
			"FFFF00" => GetMessage("P_COLOR_FFFF00"), 
			"FFFFFF" => GetMessage("P_COLOR_FFFFFF"),
			"000000" => GetMessage("P_COLOR_000000"));
}
//		GetMessage("P_COLOR_FFA500"), 
//		GetMessage("P_COLOR_008000"), 
//		GetMessage("P_COLOR_00FFFF"),
//		GetMessage("P_COLOR_800080"));
//		GetMessage("P_WATERMARK_SIZE_BIG"), 
//		GetMessage("P_WATERMARK_SIZE_MIDDLE"), 
//		GetMessage("P_WATERMARK_SIZE_SMALL"), 
//

/*************************************************************************
	/Processing of received parameters
*************************************************************************/



$watermark_colors = array_keys($arParams["WATERMARK_COLORS"]);
$arWaterMark = array(
	"tl" => GetMessage("P_WATERMARK_POSITION_TL"),
	"tc" => GetMessage("P_WATERMARK_POSITION_TC"),
	"tr" => GetMessage("P_WATERMARK_POSITION_TR"),
	"ml" => GetMessage("P_WATERMARK_POSITION_ML"),
	"mc" => GetMessage("P_WATERMARK_POSITION_MC"),
	"mr" => GetMessage("P_WATERMARK_POSITION_MR"),
	"bl" => GetMessage("P_WATERMARK_POSITION_BL"),
	"bc" => GetMessage("P_WATERMARK_POSITION_BC"),
	"br" => GetMessage("P_WATERMARK_POSITION_BR"));

$arUserSettings = array();
if ($GLOBALS["USER"]->IsAuthorized())
{
	require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/".strToLower($GLOBALS["DB"]->type)."/favorites.php");
	$arUserSettings = @unserialize(CUserOptions::GetOption("photogallery", "UploadViewMode", ""));
	$view_mode = ($arUserSettings["view_mode"] != "form" ? "applet" : "form");
	$watermark_copyright = $arUserSettings["copyright"];
	$watermark_color = $arUserSettings["color"];
	$watermark_size = $arUserSettings["size"];
	$watermark_position = $arUserSettings["position"];
	$watermark_text = $arUserSettings["text"];
	$watermark_resize = $arUserSettings["resize"];
}
else 
{
	$view_mode = ($_REQUEST["view_mode"] != "form" ? "applet" : "form");
	$watermark_copyright = strToLower($_REQUEST["watermark_copyright"]);
	$watermark_color = strToLower($_REQUEST["watermark_color"]);
	$watermark_size = strToLower($_REQUEST["watermark_size"]);
	$watermark_position = strToLower($_REQUEST["watermark_position"]);
	$watermark_text = $_REQUEST["watermark"];
	$watermark_resize = 1;
}

$str = strToLower($_SERVER['HTTP_USER_AGENT']);
$Browser["isOpera"] = (strpos($str, "opera") !== false);
$Browser["isIE"] = (!$Browser["isOpera"] && strpos($str, "msie") !== false);
$Browser["isWinIE"] = ($Browser["isIE"] && strpos($str, "win") !== false);
$view_mode = ($Browser["isOpera"] ? "form" : $view_mode);

$watermark_text = htmlspecialchars($watermark_text);
$watermark_color = htmlspecialchars(strToUpper($watermark_color));
$watermark_size = strToLower($watermark_size);
$watermark_position = strToLower($watermark_position);
$watermark_copyright = ($watermark_copyright == "hide"? "hide" : "show");

if (empty($watermark_color) || !in_array($watermark_color, $watermark_colors))
	$watermark_color = $watermark_colors[0];
$watermark_color = strToLower($watermark_color);

if (!in_array($watermark_size, array("big", "middle", "small")))
	$watermark_size = "middle";
	
if (!in_array($watermark_position, array_keys($arWaterMark)))
	$watermark_position = "br";

if ($arParams["BEHAVIOUR"] == "USER"):
?><div class="photo-user<?=($arResult["GALLERY"]["CREATED_BY"] == $GLOBALS["USER"]->GetId() ? " photo-user-my" : "")?>"><?
endif;

?><div class="photo-controls photo-action"><?
if (!empty($arResult["SECTION"]["ID"])):
	?><a href="<?=$arResult["SECTION_LINK"]?>" class="photo-action back-to-album"><?=GetMessage("P_GO_TO_SECTION")?></a><?
elseif (!empty($arResult["GALLERY"]["ID"])):
	?><a href="<?=$arResult["GALLERY_LINK"]?>" class="photo-action back-to-album"><?=GetMessage("P_GO_TO_GALLERY")?></a><?
else:
	?><a href="<?=$arResult["SECTIONS_TOP_LINK"]?>" title="<?=GetMessage("P_UP_TITLE")?>" class="photo-action back-to-album"><?=GetMessage("P_UP")?></a><?
endif;
if (!$Browser["isOpera"]):
	?><a href="<?=htmlspecialcharsEx($APPLICATION->GetCurPageParam(($_REQUEST["view_mode"] == "form" ? "" :  "view_mode=form"), array("view_mode")))?>" <?
		?>onclick="return PhotoClass.ChangeMode();" id="ControlsAppletForm" class="photo-action <?=($view_mode == "form" ? "photo-upload" : "")?>"><?
		?><?=($view_mode == "form" ? GetMessage("P_SHOW_APPLET") : GetMessage("P_SHOW_FORM"))?><?
	?></a><?
endif;
?></div><?
?><br /><?
?><script>
show_tags = '<?=($arResult["SHOW"]["TAGS"] == "Y" ? "Y" : "N")?>';
window.urlRedirectThis = '<?=$arResult["~SECTION_LINK"]?>';
window.urlRedirect = '<?=$arResult["~SECTION_LINK_EMTY"]?>';
window.PhotoUserID = <?=intVal($GLOBALS["USER"]->GetID())?>;
</script><?
?><div id="photo_error" class="photo-error"><?ShowError($arResult["ERROR_MESSAGE"]);?>
<noscript><?=GetMessage("P_JAVASCRIPT_DISABLED")?></noscript></div><?

if ($view_mode != "form"):
?><div id="waitwindow" class="waitwindow"><?=GetMessage("P_LOADING")?></div>
<?if (!$Browser["isWinIE"]):?>
<script>
if (!navigator.javaEnabled())
	document.getElementById('photo_error').innerHTML += "<?=CUtil::JSEscape(GetMessage("P_JAVA_DISABLED"))?><br />";
</script>
<?endif;?>

<input type='hidden' name='sessid' id='sessid' value='<?=bitrix_sessid()?>' />
<table border="0" cellpadding="0" cellspacing="0" width="100%" id="table_photo_object" class="photo_upload_table">
	<tr class="t"><td class="l"><div class="empty"></div></td><td class="r"><div class="empty"></div></td></tr>
	<tr valign="top"><td align="left">
		<div id="AddFolders" <?
			?>onclick="if ((jsUtils.IsIE() && getImageUploader('ImageUploader1')) || <?
				?>(!jsUtils.IsIE() && getImageUploader('ImageUploader1') && getImageUploader('ImageUploader1').AddFolders)) <?
				?>{getImageUploader('ImageUploader1').AddFolders();}">
		<table cellpadding="0" cellspacing="0" border="0" class="button" <?
			?>onmousedown="this.className='button-press';" <?
			?>onmouseout="this.className = 'button'">
			<tr>
				<td class="l"><div class="empty"></div></td>
				<td class="c"><?=GetMessage("AddFolders")?></td>
				<td class="r"><div class="empty"></div></td></tr>
		</table>
		</div>
		<div id="AddFiles" <?
			?>onclick="if ((jsUtils.IsIE() && getImageUploader('ImageUploader1')) || (!jsUtils.IsIE() && getImageUploader('ImageUploader1')<?
				?> && getImageUploader('ImageUploader1').AddFiles)) {getImageUploader('ImageUploader1').AddFiles();}">
		<table cellpadding="0" cellspacing="0" border="0" class="button" onmousedown="this.className += '-press';" onmouseout="this.className = 'button'">
			<tr>
				<td class="l"><div class="empty"></div></td>
				<td class="c"><?=GetMessage("AddFiles")?></td>
				<td class="r"><div class="empty"></div></td>
			</tr>
		</table>
		</div>
	</td><td>
		<div class="inner">
			<div id="RemoveAllFromUploadList">
				<a href="javascript:void(0);" onclick="if ((jsUtils.IsIE() && getImageUploader('ImageUploader1')) || (!jsUtils.IsIE() && <?
					?>getImageUploader('ImageUploader1') && getImageUploader('ImageUploader1').RemoveAllFromUploadList))  <?
					?>{getImageUploader('ImageUploader1').RemoveAllFromUploadList();}">
					<?=GetMessage("RemoveAllFromUploadList")?>
				</a>
			</div>
			<div class="photo-bold" id="photo_count_to_upload_div">
				<div id="photo_count_to_upload"><?=GetMessage("NoPhoto")?></div> 
				<?=GetMessage("Photo")?>
			</div>
		</div>
	</td></tr>
	<tr><td><div class="hr"></div></td><td></td></tr>
	<tr valign="top">
		<td id="object">
<script type="text/javascript">
if (typeof oText != "object")
	oText = {};
oText["Title"] = "<?=CUtil::addslashes(GetMessage("Title"))?>";
oText["Tags"] = "<?=CUtil::addslashes(GetMessage("Tags"))?>";
oText["Description"] = "<?=CUtil::addslashes(GetMessage("Description"))?>";
oText["NoPhoto"] = "<?=CUtil::addslashes(GetMessage("NoPhoto"))?>";
oText["Public"] = "<?=CUtil::addslashes(GetMessage("Public"))?>";


if (typeof oParams != "object")
	oParams = {};
oParams["min_size_picture"] = <?=htmlspecialcharsEx($arParams["WATERMARK_MIN_PICTURE_SIZE"]);?>;

var oAppletInfo = <?=CUtil::PhpToJSObject($arParams["PICTURES"])?>;
	
iu = null;
t = null;
bInitPhotoUploader = false;
function to_init()
{
	is_loaded = false;
	try
	{
		if (PUtilsIsLoaded == true)
			is_loaded = true;
	}
	catch(e){}
	
	if (is_loaded)
	{
		if (!bInitPhotoUploader && InitPhotoUploader)
		{
			InitPhotoUploader();
			bInitPhotoUploader = true;
		}
	}
	if (!bInitPhotoUploader)
		setTimeout(to_init, 100);
	return;
}
to_init();

function InitPhotoUploader()
{
	iu = new ImageUploaderWriter("ImageUploader1", "100%", 315);
	
	iu.addEventListener("SelectionChange", "ChangeSelectionLink");
	iu.addEventListener("UploadFileCountChange", "ChangeFileCountLink");
	iu.addEventListener("AfterUpload", "AfterUploadLink");
	iu.addEventListener("BeforeUpload", "BeforeUploadLink");
	iu.addEventListener("PackageBeforeUpload", "PackageBeforeUploadLink");
	
	iu.fullPageLoadListenerName = "InitLink";
	
	iu.addParam("ShowDescriptions", "false");
	iu.addParam("AllowRotate", "true");
	iu.addParam("PaneLayout", "OnePane");
	iu.addParam("UseSystemColors", "false");
	iu.addParam("BackgroundColor", "#ededed");
	iu.addParam("UploadPaneBackgroundColor", "#ededed");
	iu.addParam("UploadPaneBorderStyle", "none");
	iu.addParam("PreviewThumbnailBorderColor", "#afafaf");
	iu.addParam("PreviewThumbnailBorderHoverColor", "#91a7d3");
	iu.addParam("PreviewThumbnailActiveSelectionColor", "#ff8307");
	iu.addParam("PreviewThumbnailInactiveSelectionColor", "#ff8307");
	
	iu.addParam("ShowUploadListButtons", "false");
	iu.addParam("ShowButtons", "false");
	iu.addParam("FolderView", "Thumbnails");

	iu.addParam("UploadThumbnail1FitMode", "Fit");
	iu.addParam("UploadThumbnail1Width", "<?=$arParams["THUMBS_SIZE"]["SIZE"]?>");
	iu.addParam("UploadThumbnail1Height", "<?=$arParams["THUMBS_SIZE"]["SIZE"]?>");
	iu.addParam("UploadThumbnail1JpegQuality", "<?=$arParams["JPEG_QUALITY1"]?>");
	
	iu.addParam("UploadThumbnail2FitMode", "Fit");
	iu.addParam("UploadThumbnail2Width", "<?=$arParams["PREVIEW_SIZE"]["SIZE"]?>");
	iu.addParam("UploadThumbnail2Height", "<?=$arParams["PREVIEW_SIZE"]["SIZE"]?>");
	iu.addParam("UploadThumbnail2JpegQuality", "<?=$arParams["JPEG_QUALITY2"]?>");
	
	iu.addParam("UploadThumbnail3FitMode", "ActualSize");
	iu.addParam("UploadThumbnail3JpegQuality", "<?=$arParams["JPEG_QUALITY"]?>");
	
	//Configure upload settings.
	iu.addParam("UploadSourceFile", "false");
	iu.addParam("ExtractExif", "ExifDateTime;ExifOrientation;ExifModel");
	iu.addParam("FilesPerOnePackageCount", "<?=$arParams["UPLOAD_MAX_FILE"]?>");
	
	//Configure URL files are uploaded to.
	sAction = window.location.protocol + "//<?=str_replace("//", "/", $_SERVER["HTTP_HOST"]."/".POST_FORM_ACTION_URI)?>";
	iu.addParam("Action", sAction);
	//Configure URL where to redirect after upload.
//	iu.addParam("RedirectUrl", "");
	//For ActiveX control full path to CAB file (including file name) should be specified.
	iu.activeXControlCodeBase = "/bitrix/image_uploader/ImageUploader.cab";
	iu.activeXClassId = "<?=$arParams["IMAGE_UPLOADER_ACTIVEX_CLSID"]?>";
	iu.activeXControlVersion = "<?=$arParams["IMAGE_UPLOADER_ACTIVEX_CONTROL_VERSION"]?>";
	//For Java applet only path to directory with JAR files should be specified (without file name).
	iu.javaAppletCodeBase = "/bitrix/image_uploader";
	iu.javaAppletClassName="com.bitrixsoft.imageuploader.ImageUploader.class"; 
	iu.javaAppletJarFileName="ImageUploader.jar"; 
	iu.javaAppletCached = true;
	iu.javaAppletVersion = "<?=$arParams["IMAGE_UPLOADER_JAVAAPPLET_VERSION"]?>";
	
	iu.showNonemptyResponse = "off";
	
	//Configure appearance.
	//Set and configure advanced details view.
	iu.addParam("ButtonAddToUploadListText", "<?=CUtil::addslashes(GetMessage("P_BUTTON_ADD_TO_UPLOAD_LIST_TEXT"))?>");
	iu.addParam("ButtonAddAllToUploadListText", "<?=CUtil::addslashes(GetMessage("P_BUTTON_ADD_ALL_TO_UPLOAD_LIST_TEXT"))?>");
	iu.addParam("ButtonRemoveFromUploadListText", "");
	iu.addParam("ButtonRemoveAllFromUploadListText", "");
	
	iu.addParam("MenuThumbnailsText", "<?=CUtil::addslashes(GetMessage("P_MENU_THUMBNAILS_TEXT"))?>");
	iu.addParam("MenuDetailsText", "<?=CUtil::addslashes(GetMessage("P_MENU_DETAILS_TEXT"))?>");
	iu.addParam("MenuListText", "<?=CUtil::addslashes(GetMessage("P_MENU_LIST_TEXT"))?>");
	iu.addParam("MenuIconsText", "<?=CUtil::addslashes(GetMessage("P_MENU_ICONS_TEXT"))?>");
	iu.addParam("MenuArrangeByText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_TEXT"))?>");
	iu.addParam("MenuArrangeByUnsortedText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_UNSORTED"))?>");
	iu.addParam("MenuArrangeByNameText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_NAME_TEXT"))?>");
	iu.addParam("MenuArrangeBySizeText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_SIZE_TEXT"))?>");
	iu.addParam("MenuArrangeByTypeText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_TYPE_TEXT"))?>");
	iu.addParam("MenuArrangeByModifiedText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_MODIFIED_TEXT"))?>");
	iu.addParam("MenuArrangeByPathText", "<?=CUtil::addslashes(GetMessage("P_MENU_ARRANGE_BY_PATH_TEXT"))?>");
	iu.addParam("MenuSelectAllText", "<?=CUtil::addslashes(GetMessage("P_MENU_SELECT_ALL_TEXT"))?>");
	iu.addParam("MenuDeselectAllText", "<?=CUtil::addslashes(GetMessage("P_MENU_DESELECT_ALL_TEXT"))?>");
	iu.addParam("MenuInvertSelectionText", "<?=CUtil::addslashes(GetMessage("P_MENU_INVERT_SELECTION_TEXT"))?>");
	iu.addParam("MenuRemoveFromUploadListText", "<?=CUtil::addslashes(GetMessage("P_MENU_REMOVE_FROM_UPLOAD_LIST_TEXT"))?>");
	iu.addParam("MenuRemoveAllFromUploadListText", "<?=CUtil::addslashes(GetMessage("P_MENU_REMOVE_ALL_FROM_UPLOAD_LIST_TEXT"))?>");
	iu.addParam("MenuRefreshText", "<?=CUtil::addslashes(GetMessage("P_MENU_REFRESH_TEXT"))?>");
	
	iu.instructionsCommon = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_COMMON"))?>';
	iu.instructionsNotWinXPSP2 = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_NOT_WINXPSP2"))?>';
	iu.instructionsWinXPSP2 = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_WINXPSP2"))?>';
	
	//ImageUploader properties
	iu.addParam("AuthenticationRequestBasicText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestBasicText"))?>");
	iu.addParam("AuthenticationRequestButtonCancelText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestButtonCancelText"))?>");
	iu.addParam("AuthenticationRequestDomainText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestDomainText"))?>");
	iu.addParam("AuthenticationRequestLoginText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestLoginText"))?>");
	iu.addParam("AuthenticationRequestNtlmText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestNtlmText"))?>");
	iu.addParam("AuthenticationRequestPasswordText", "<?=CUtil::addslashes(GetMessage("P_AuthenticationRequestPasswordText"))?>");
	iu.addParam("ButtonAddFilesText", "<?=CUtil::addslashes(GetMessage("P_ButtonAddFilesText"))?>");
	iu.addParam("ButtonAdvancedDetailsCancelText", "<?=CUtil::addslashes(GetMessage("P_ButtonAdvancedDetailsCancelText"))?>");
	iu.addParam("ButtonDeselectAllText", "<?=CUtil::addslashes(GetMessage("P_ButtonDeselectAllText"))?>");
	iu.addParam("ButtonRemoveAllFromUploadListText", "<?=CUtil::addslashes(GetMessage("P_ButtonRemoveAllFromUploadListText"))?>");
	iu.addParam("ButtonRemoveFromUploadListText", "<?=CUtil::addslashes(GetMessage("P_ButtonRemoveFromUploadListText"))?>");
	iu.addParam("RotateIconClockwiseTooltipText", "<?=CUtil::addslashes(GetMessage("P_RotateIconClockwiseTooltipText"))?>");
	iu.addParam("RotateIconCounterclockwiseTooltipText", "<?=CUtil::addslashes(GetMessage("P_RotateIconCounterclockwiseTooltipText"))?>");
	
	iu.addParam("ButtonSelectAllText", "<?=CUtil::addslashes(GetMessage("P_ButtonSelectAllText"))?>");
	iu.addParam("ButtonSendText", "<?=CUtil::addslashes(GetMessage("P_ButtonSendText"))?>");
	iu.addParam("DescriptionEditorButtonCancelText", "<?=CUtil::addslashes(GetMessage("P_DescriptionEditorButtonCancelText"))?>");
	iu.addParam("FileIsTooLargeText", "<?=CUtil::addslashes(GetMessage("P_FileIsTooLargeText"))?>");
	iu.addParam("HoursText", "<?=CUtil::addslashes(GetMessage("P_HoursText"))?>");
	iu.addParam("KilobytesText", "<?=CUtil::addslashes(GetMessage("P_KilobytesText"))?>");
	iu.addParam("LoadingFilesText", "<?=CUtil::addslashes(GetMessage("P_LoadingFilesText"))?>");
	iu.addParam("MegabytesText", "<?=CUtil::addslashes(GetMessage("P_MegabytesText"))?>");
	iu.addParam("MenuAddAllToUploadListText", "<?=CUtil::addslashes(GetMessage("P_MenuAddAllToUploadListText"))?>");
	iu.addParam("MenuAddToUploadListText", "<?=CUtil::addslashes(GetMessage("P_MenuAddToUploadListText"))?>");
	iu.addParam("MessageBoxTitleText", "<?=CUtil::addslashes(GetMessage("P_MessageBoxTitleText"))?>");
	iu.addParam("MessageCannotConnectToInternetText", "<?=CUtil::addslashes(GetMessage("P_MessageCannotConnectToInternetText"))?>");
	iu.addParam("MessageMaxFileCountExceededText", "<?=CUtil::addslashes(GetMessage("P_MessageMaxFileCountExceededText"))?>");
	iu.addParam("MessageMaxTotalFileSizeExceededText", "<?=CUtil::addslashes(GetMessage("P_MessageMaxTotalFileSizeExceededText"))?>");
	iu.addParam("MessageNoResponseFromServerText", "<?=CUtil::addslashes(GetMessage("P_MessageNoResponseFromServerText"))?>");
	iu.addParam("MessageServerNotFoundText", "<?=CUtil::addslashes(GetMessage("P_MessageServerNotFoundText"))?>");
	iu.addParam("MessageUnexpectedErrorText", "<?=CUtil::addslashes(GetMessage("P_MessageUnexpectedErrorText"))?>");
	iu.addParam("MessageUploadCancelledText", "<?=CUtil::addslashes(GetMessage("P_MessageUploadCancelledText"))?>");
	iu.addParam("MessageUploadCompleteText", "<?=CUtil::addslashes(GetMessage("P_MessageUploadCompleteText"))?>");
	iu.addParam("MessageUploadFailedText", "<?=CUtil::addslashes(GetMessage("P_MessageUploadFailedText"))?>");
	iu.addParam("MessageUserSpecifiedTimeoutHasExpiredText", "<?=CUtil::addslashes(GetMessage("P_MessageUserSpecifiedTimeoutHasExpiredText"))?>");
	iu.addParam("MinutesText", "<?=CUtil::addslashes(GetMessage("P_MinutesText"))?>");
	iu.addParam("ProgressDialogCancelButtonText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogCancelButtonText"))?>");
	iu.addParam("ProgressDialogCloseButtonText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogCloseButtonText"))?>");
	iu.addParam("ProgressDialogCloseWhenUploadCompletesText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogCloseWhenUploadCompletesText"))?>");
	iu.addParam("ProgressDialogEstimatedTimeText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogEstimatedTimeText"))?>");
	iu.addParam("ProgressDialogPreparingDataText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogPreparingDataText"))?>");
	iu.addParam("ProgressDialogSentText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogSentText"))?>");
	iu.addParam("ProgressDialogTitleText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogTitleText"))?>");
	iu.addParam("ProgressDialogWaitingForResponseFromServerText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogWaitingForResponseFromServerText"))?>");
	iu.addParam("ProgressDialogWaitingForRetryText", "<?=CUtil::addslashes(GetMessage("P_ProgressDialogWaitingForRetryText"))?>");
	iu.addParam("RemoveIconTooltipText", "<?=CUtil::addslashes(GetMessage("P_RemoveIconTooltipText"))?>");
	iu.addParam("SecondsText", "<?=CUtil::addslashes(GetMessage("P_SecondsText"))?>");
	iu.addParam("DropFilesHereText", "<?=CUtil::addslashes(GetMessage("P_DropFilesHereText"))?>");
	if (!__browser.isOpera)
	{
		iu.addParam("MessageRetryOpenFolderText", '<?=CUtil::addslashes(GetMessage("P_MessageRetryOpenFolderText"))?>');
		iu.addParam("MessageRedirectText", "<?=CUtil::addslashes(GetMessage("P_MessageRedirectText"))?>");
		iu.addParam("MessageSwitchAnotherFolderWarningText", "<?=CUtil::addslashes(GetMessage("P_MessageSwitchAnotherFolderWarningText"))?>");
		iu.addParam("MessageDimensionsAreTooLargeText", "<?=CUtil::addslashes(GetMessage("P_MessageDimensionsAreTooLargeText"))?>");
		iu.addParam("MessageNoInternetSessionWasEstablishedText", "<?=CUtil::addslashes(GetMessage("P_MessageNoInternetSessionWasEstablishedText"))?>");
		iu.addParam("UnixFileSystemRootText", "<?=CUtil::addslashes(GetMessage("P_UnixFileSystemRootText"))?>");
		iu.addParam("UnixHomeDirectoryText", "<?=CUtil::addslashes(GetMessage("P_UnixHomeDirectoryText"))?>");
	}
	iu.writeHtml();
}
</script>
</td>
<td id="description">
	<div class="inner">
		<div class="photo_desription_field" id="photo_desription_field">
			<div id="photo_desription_field_image">
<script type="text/javascript">
bInitThumbnailWriter = false;
function to_init_thumb()
{
	is_loaded = false;
	try
	{
		if (PUtilsIsLoaded == true)
			is_loaded = true;
	}
	catch(e){}
	
	if (is_loaded)
	{
		if (!bInitThumbnailWriter && InitThumbnailWriter)
		{
			InitThumbnailWriter();
			bInitThumbnailWriter = true;
		}
	}
	if (!bInitThumbnailWriter)
		setTimeout(to_init_thumb, 100);
	return;
}
to_init_thumb();
function InitThumbnailWriter()
{
	t = new ThumbnailWriter("Thumbnail1", 120, 120);
	t.addParam("BackgroundColor", "#d8d8d8");
	//For ActiveX control full path to CAB file (including file name) should be specified.
	t.activeXControlCodeBase = "/bitrix/image_uploader/ImageUploader.cab";
	t.activeXClassId = "<?=$arParams["THUMBNAIL_ACTIVEX_CLSID"]?>";
	t.activeXControlVersion = "<?=$arParams["THUMBNAIL_ACTIVEX_CONTROL_VERSION"]?>";
	//For Java applet only path to directory with JAR files should be specified (without file name).
	t.javaAppletCodeBase = "/bitrix/image_uploader";
	t.javaAppletJarFileName="ImageUploader.jar"; 
	t.javaAppletCached = true;
	t.javaAppletVersion = "<?=$arParams["THUMBNAIL_JAVAAPPLET_VERSION"]?>";
	
	t.instructionsCommon = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_COMMON"))?>';
	t.instructionsNotWinXPSP2 = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_NOT_WINXPSP2"))?>';
	t.instructionsWinXPSP2 = '<?=CUtil::addslashes(GetMessage("P_INSTRUCTIONS_WINXPSP2"))?>';
	t.addParam("ParentControlName", "ImageUploader1");
	t.writeHtml();
}
</script>
			</div>
			<?if ($arParams["BEHAVIOUR"] == "USER"):?>
			<input name="Public" id="PhotoPublic" class="PhotoPublic" type="checkbox" value="Y" disabled="disabled" />
				<label for="PhotoPublic"><?=GetMessage("Public")?></label><br />
			<?endif;?>
			<?=GetMessage("Title")?>:<br /><input name="Title" id="PhotoTitle" class="Title" type="text" /><br />
			<?if ($arParams["SHOW_TAGS"] == "Y" && IsModuleInstalled("search")):?>
				<?=GetMessage("Tags")?>:<br />
					<input name="Tag" id="PhotoTag" class="Tag" type="text" onfocus="PhotoClass.SendTags(this);" /><br />
			<?elseif ($arParams["SHOW_TAGS"] == "Y"):?>
				<?=GetMessage("Tags")?>:<br />
					<input name="Tag" id="PhotoTag" class="Tag" type="text" /><br />
			<?endif;?>
			<?=GetMessage("Description")?>:<br />
			<textarea name="Description" id="PhotoDescription" class="Description"></textarea>
		</div>
	</div>
</td></tr>
<tr><td><div class="hr"></div></td><td></td></tr>


<tr><td><?
		?><div id="photo_albums_to_move"><?=GetMessage("P_TO_ALBUM")?>: <?
			?><select id="photo_album_id" name="photo_album_id"><?
			?><option value="new" <?=(intVal($arParams["SECTION_ID"]) == 0 ? "selected" : "")?>><?
				?><?=GetMessage("P_IN_NEW_ALBUM")?></option><?
			
		if (is_array($arResult["SECTION_LIST"]))
		{
			?><optgroup><?
			foreach ($arResult["SECTION_LIST"] as $key => $val):
				?><option value="<?=$key?>" <?
					?> <?=($arParams["SECTION_ID"] == $key ? "selected" : "")?>><?=$val?></option><?
			endforeach;
			?></optgroup><?
		}
			?></select></div><?
		?><div id="photo_resize_div"><?=GetMessage("P_RESIZE")?>: <?
			?><select name="photo_resize_size" id="photo_resize_size" onchange="WaterMark.ChangeText(this)">
					<option value="0" <?=($watermark_resize == 0 ? "selected" : "")?>><?=GetMessage("P_ORIGINAL")?></option>
					<option value="1" <?=($watermark_resize == 1 ? "selected" : "")?>>1024x768</option>
					<option value="2" <?=($watermark_resize == 2 ? "selected" : "")?>>800x600</option>
					<option value="3" <?=($watermark_resize == 3 ? "selected" : "")?>>640x480</option>
				</select></div><?
		
		if ($arParams["WATERMARK"] == "Y"):
		?><div id="photo_watermark"><?
			?><table border="0" cellpadding="0" cellspacing="0"><tr><?
				?><td><?=GetMessage("P_WATERMARK")?>: </td><?
				?><td><input type="text" id="watermark" name="watermark" value="<?=$watermark_text?>" size="15" onblur="WaterMark.ChangeText(this)" /></td><?
				?><td><input type="hidden" id="watermark_copyright" name="watermark_copyright" value="<?=$watermark_copyright?>" /><?
				
				?><div id="watermark_copyright_main" title="<?=GetMessage("P_WATERMARK_COPYRIGHT")?>"><?
					?><div id="watermark_copyright_switcher" class="<?=$watermark_copyright?>" <?
						?>onclick="WaterMark.ShowMenu('copyright');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';"><?
					?></div><?
					
					?><div id="watermark_copyright_container"><?
					foreach (array("show", "hide") as $value):
						?><div id="copyright_<?=$value?>" class="string<?=($value == $watermark_copyright ? ' active' : '')?>" <?
							?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
							?>onmouseout="this.className = this.className.replace(' over', '');" <?
							?>onmouseover="this.className += ' over';" title="<?=GetMessage("P_WATERMARK_COPYRIGHT_".strToUpper($value))?>"><?
							?><?=GetMessage("P_WATERMARK_COPYRIGHT_".strToUpper($value))?><?
							?><div class="<?=$value?>"></div><?
						?></div><?
					endforeach;
					?></div><?
				?></div><?
				
				?></td><?

			if (is_array($arParams["WATERMARK_COLORS"]))
			{
				?><td><input type="hidden" id="watermark_color" name="watermark_color" value="<?=$watermark_color?>" /><?

				?><div id="watermark_color_main" title="<?=GetMessage("P_WATERMARK_COLOR_TITLE")?>"><?
					?><div id="watermark_color_switcher" style="background-color:#<?=$watermark_color?>;"<?
						?>onclick="WaterMark.ShowMenu('color');" <?
						?>onmouseout="this.className = '';" <?
						?>onmouseover="this.className = 'over';"><?
					?></div><?
					
					?><div id="watermark_color_container"><?
					foreach ($arParams["WATERMARK_COLORS"] as $value => $title):
						$value = htmlspecialChars(strToLower($value));
						?><div id="color_<?=$value?>" <?
							?>class="string <?=($value == $watermark_color ? "active" : "1")?>" <?
							?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
							?>onmouseout="this.className = this.className.replace(' over', '');" <?
							?>onmouseover="this.className += ' over';" title="<?=$title?>">
						<div class="color_icon" style="background-color:#<?=$value?>;"></div>
						<?=$title?>
						</div><?
					endforeach;
					?></div><?
				?></div><?
				?></td><?
			}
			
			?><td><?
			?><input type="hidden" id="watermark_size" name="watermark_size" value="<?=$watermark_size?>" /><?
			
			?><div id="watermark_size_main" title="<?=GetMessage("P_WATERMARK_SIZE_TITLE")?>"><?
				?><div id="watermark_size_switcher" class="<?=$watermark_size?>" <?
					?>onclick="WaterMark.ShowMenu('size');" <?
					?>onmouseout="this.className = this.className.replace(' over', '');" <?
					?>onmouseover="this.className += ' over';"><?
				?></div><?
				?><div id="watermark_size_container"><?
				foreach (array("big", "middle", "small") as $value):
					?><div id="size_<?=$value?>" class="string<?=($value == $watermark_size ? ' active' : '')?>" <?
						?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';" title="<?=GetMessage("P_WATERMARK_SIZE_".strToUpper($value))?>"><?
						?><?=GetMessage("P_WATERMARK_SIZE_".strToUpper($value))?><?
					?><div class="<?=$value?>"></div><?
					?></div><?
				endforeach;
				?></div><?
			?></div><?
			?></td><?
			
			?><td><?
			?><input type="hidden" id="watermark_position" name="watermark_position" value="<?=$watermark_position?>" /><?
			?><div id="watermark_position_main" title="<?=GetMessage("P_WATERMARK_POSITION_TITLE")?>"><?
				?><div id="watermark_position_switcher" class="<?=$watermark_position?>" <?
					?>onclick="WaterMark.ShowMenu('position');" <?
					?>onmouseout="this.className = this.className.replace(' over', '');" <?
					?>onmouseover="this.className += ' over';"><?
				?></div><?
				?><div id="watermark_position_container"><?
				foreach ($arWaterMark as $value => $name):
					?><table border="0" cellpadding="0" cellspacing="0" class="outer"><tr><td><?
					?><div id="position_<?=$value?>" class="<?=$value?><?=($value == $watermark_position ? ' active' : '')?>" <?
						?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';" title="<?=$name?>"></div><?
					?></td></tr></table><?
				endforeach;
				?></div><?
			?></div></td></tr></table><?
		?></div><?
		endif;
		
		
	?><div id="Send">
		<table cellpadding="0" cellspacing="0" border="0" class="button"  onmousedown="this.className += '-press';" onmouseout="this.className = 'button'">
			<tr>
				<td class="l"><div class="empty"></div></td>
				<td class="c" id="SendColor"><?=GetMessage("Send")?></td>
				<td class="r"><div class="empty"></div></td>
			</tr>
		</table>
	</div>
</td>
<td>
</td></tr>
<tr class="b"><td class="l"><div class="empty"></div></td><td class="r"><div class="empty"></div></td></tr>
</table>
<?
else:

?><form id='form_upload' action='<?=POST_FORM_ACTION_URI?>' method='post' enctype='multipart/form-data'>
<table id="photo_form_table">
<tr><td>
		<input type='hidden' name='redirect' value='Y' />
		<input type='hidden' name='FileCount' value='<?=$arParams["UPLOAD_MAX_FILE"]?>' />
		<input type='hidden' name='save_upload' id='save_upload' value='Y' />
		<input type='hidden' name='sessid' id='sessid' value='<?=bitrix_sessid()?>' />
		<input type='hidden' name='redirect' value='Y' />
		<input type='hidden' name='PackageGuid' value='<?=time()?>' />
<p><?=GetMessage("P_SELECT_PHOTO_FOR_UPLOAD")?>:</p>
		<?
		for ($ii = 1; $ii <= $arParams["UPLOAD_MAX_FILE"]; $ii++):
		?><div class="photo-element"><?
			?><div class="file"><input type='file' id='SourceFile_<?=$ii?>' name='SourceFile_<?=$ii?>' /></div>
			<?=GetMessage("Title")?>:<br /><input name="Title_<?=$ii?>" id="Title_<?=$ii?>" class="Title" type="text" /><br /><?
			if ($arParams["BEHAVIOUR"] == "USER"):?>
			<input name="Public" id="PhotoPublic_<?=$ii?>" class="PhotoPublic" type="checkbox" value="Y" />
				<label for="PhotoPublic_<?=$ii?>"><?=GetMessage("Public")?></label><br />
			<?endif;
			if ($arResult["SHOW"]["TAGS"] == "Y"):
			?><?=GetMessage("Tags")?>:<br /><input name="Tags_<?=$ii?>" id="Tags_<?=$ii?>" class="Tag" type="text" onfocus="PhotoClass.SendTags(this);" /><br /><?
			endif;
		?><?=GetMessage("Description")?>: <br />
		<textarea name="Description_<?=$ii?>" id="Description_<?=$ii?>" class="Description"></textarea><br />
		</div><?
		endfor;
?></td></tr>

<tr><td>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" id="table_photo_form" class="photo_upload_table">
		<tr class="t"><td class="l"><div class="empty"></div></td><td class="r"><div class="empty"></div></td></tr>
		<tr valign="top"><td align="left" id="photo_data">
		<div id="photo_albums_to_move"><?=GetMessage("P_TO_ALBUM")?>: <?
			?><select id="photo_album_id" name="photo_album_id"><?
			?><option value="new" <?=(intVal($arParams["SECTION_ID"]) == 0 ? "selected" : "")?>><?
				?><?=GetMessage("P_IN_NEW_ALBUM")?></option><?
			
		if (is_array($arResult["SECTION_LIST"]))
		{
			?><optgroup><?
			foreach ($arResult["SECTION_LIST"] as $key => $val):
				?><option value="<?=$key?>" <?
					?> <?=($arParams["SECTION_ID"] == $key ? "selected" : "")?>><?=$val?></option><?
			endforeach;
			?></optgroup><?
		}
			?></select></div><?
		?><div id="photo_resize_div"><?=GetMessage("P_RESIZE")?>: <?
			?><select name="photo_resize_size" id="photo_resize_size" onchange="WaterMark.ChangeText(this)">
					<option value="0" <?=($watermark_resize == 0 ? "selected" : "")?>><?=GetMessage("P_ORIGINAL")?></option>
					<option value="1" <?=($watermark_resize == 1 ? "selected" : "")?>>1024x768</option>
					<option value="2" <?=($watermark_resize == 2 ? "selected" : "")?>>800x600</option>
					<option value="3" <?=($watermark_resize == 3 ? "selected" : "")?>>640x480</option>
				</select></div><?
		
		if ($arParams["WATERMARK"] == "Y" && !empty($arParams["PATH_TO_FONT"])):
		?><div id="photo_watermark"><?
			?><table border="0" cellpadding="0" cellspacing="0"><tr><?
				?><td><?=GetMessage("P_WATERMARK")?>: </td><?
				?><td><input type="text" id="watermark" name="watermark" value="<?=$watermark_text?>" size="15" onblur="WaterMark.ChangeText(this)" /></td><?
				
				?><td><input type="hidden" id="watermark_copyright" name="watermark_copyright" value="<?=$watermark_copyright?>" /><?
				
				?><div id="watermark_copyright_main" title="<?=GetMessage("P_WATERMARK_COPYRIGHT")?>"><?
					?><div id="watermark_copyright_switcher" class="<?=$watermark_copyright?>" <?
						?>onclick="WaterMark.ShowMenu('copyright');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';"><?
					?></div><?
					
					?><div id="watermark_copyright_container"><?
					foreach (array("show", "hide") as $value):
						?><div id="copyright_<?=$value?>" class="string<?=($value == $watermark_copyright ? ' active' : '')?>" <?
							?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
							?>onmouseout="this.className = this.className.replace(' over', '');" <?
							?>onmouseover="this.className += ' over';" title="<?=GetMessage("P_WATERMARK_COPYRIGHT_".strToUpper($value))?>"><?
							?><?=GetMessage("P_WATERMARK_COPYRIGHT_".strToUpper($value))?><?
							?><div class="<?=$value?>"></div><?
						?></div><?
					endforeach;
					?></div><?
				?></div><?
				?></td><?

			if (is_array($arParams["WATERMARK_COLORS"]))
			{
				?><td><input type="hidden" id="watermark_color" name="watermark_color" value="<?=$watermark_color?>" /><?

				?><div id="watermark_color_main" title="<?=GetMessage("P_WATERMARK_COLOR_TITLE")?>"><?
					?><div id="watermark_color_switcher" style="background-color:#<?=$watermark_color?>;"<?
						?>onclick="WaterMark.ShowMenu('color');" <?
						?>onmouseout="this.className = '';" <?
						?>onmouseover="this.className = 'over';"><?
					?></div><?
					
					?><div id="watermark_color_container"><?
					foreach ($arParams["WATERMARK_COLORS"] as $value => $title):
						$value = htmlspecialChars(strToLower($value));
						?><div id="color_<?=$value?>" <?
							?>class="string <?=($value == $watermark_color ? "active" : "1")?>" <?
							?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
							?>onmouseout="this.className = this.className.replace(' over', '');" <?
							?>onmouseover="this.className += ' over';" title="<?=$title?>">
						<div class="color_icon" style="background-color:#<?=$value?>;"></div>
						<?=$title?>
						</div><?
					endforeach;
					?></div><?
				?></div><?
				?></td><?
			}
			
			?><td><?
			?><input type="hidden" id="watermark_size" name="watermark_size" value="<?=$watermark_size?>" /><?
			
			?><div id="watermark_size_main" title="<?=GetMessage("P_WATERMARK_SIZE_TITLE")?>"><?
				?><div id="watermark_size_switcher" class="<?=$watermark_size?>" <?
					?>onclick="WaterMark.ShowMenu('size');" <?
					?>onmouseout="this.className = this.className.replace(' over', '');" <?
					?>onmouseover="this.className += ' over';"><?
				?></div><?
				?><div id="watermark_size_container"><?
				foreach (array("big", "middle", "small") as $value):
					?><div id="size_<?=$value?>" class="string<?=($value == $watermark_size ? ' active' : '')?>" <?
						?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';" title="<?=GetMessage("P_WATERMARK_SIZE_".strToUpper($value))?>"><?
						?><?=GetMessage("P_WATERMARK_SIZE_".strToUpper($value))?><?
					?><div class="<?=$value?>"></div><?
					?></div><?
				endforeach;
				?></div><?
			?></div><?
			?></td><?
			
			?><td><?
			?><input type="hidden" id="watermark_position" name="watermark_position" value="<?=$watermark_position?>" /><?
			?><div id="watermark_position_main" title="<?=GetMessage("P_WATERMARK_POSITION_TITLE")?>"><?
				?><div id="watermark_position_switcher" class="<?=$watermark_position?>" <?
					?>onclick="WaterMark.ShowMenu('position');" <?
					?>onmouseout="this.className = this.className.replace(' over', '');" <?
					?>onmouseover="this.className += ' over';"><?
				?></div><?
				?><div id="watermark_position_container"><?
				foreach ($arWaterMark as $value => $name):
					?><table border="0" cellpadding="0" cellspacing="0" class="outer"><tr><td><?
					?><div id="position_<?=$value?>" class="<?=$value?><?=($value == $watermark_position ? ' active' : '')?>" <?
						?>onclick="WaterMark.ChangeData('<?=$value?>');" <?
						?>onmouseout="this.className = this.className.replace(' over', '');" <?
						?>onmouseover="this.className += ' over';" title="<?=$name?>"></div><?
					?></td></tr></table><?
				endforeach;
				?></div><?
			?></div></td></tr></table><?
		?></div><?
		endif;
			
			
			?><div id="Send_form" onclick="document.getElementById('form_upload').submit();">
				<table cellpadding="0" cellspacing="0" border="0" class="button"  onmousedown="this.className += '-press';" onmouseout="this.className = 'button'">
					<tr>
						<td class="l"><div class="empty"></div></td>
						<td class="c" id="SendColor"><?=GetMessage("Send")?></td>
						<td class="r"><div class="empty"></div></td>
					</tr>
				</table>
			</div>
		</td>
		<td>
		</td></tr>
		<tr class="b"><td class="l"><div class="empty"></div></td><td class="r"><div class="empty"></div></td></tr>
	</table>
</td></tr></table>
</form><?
endif;

if ($arParams["BEHAVIOUR"] == "USER"):
?></div><?
endif;
?>