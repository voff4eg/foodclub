<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if (!$this->__component->__parent || empty($this->__component->__parent->__name) || $this->__component->__parent->__name != "bitrix:blog"):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/blog/templates/.default/themes/blue/style.css');
endif;
?>
<div class="blog-post-edit blog-post-edit-micro">
<?
if(strlen($arResult["MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["ERROR_MESSAGE"])>0)
{
	?>
	<div class="blog-errors blog-note-box blog-note-error">
		<div class="blog-error-text">
			<?=$arResult["ERROR_MESSAGE"]?>
		</div>
	</div>
	<?
}
if(strlen($arResult["FATAL_MESSAGE"])>0)
{
}
elseif(strlen($arResult["UTIL_MESSAGE"])>0)
{
	?>
	<div class="blog-textinfo blog-note-box">
		<div class="blog-textinfo-text">
			<?=$arResult["UTIL_MESSAGE"]?>
		</div>
	</div>
	<?
}
else
{
	// Frame with file input to ajax uploading in WYSIWYG editor dialog
	if($arResult["imageUploadFrame"] == "Y")
	{
		?>
		<script>
			<?if(!empty($arResult["Image"])):?>
				top.bxBlogImageId = top.arImagesId.push('<?=$arResult["Image"]["ID"]?>');
				top.arImages.push('<?=CUtil::JSEscape($arResult["Image"]["PARAMS"]["SRC"])?>');
				top.bxBlogImageIdWidth = '<?=CUtil::JSEscape($arResult["Image"]["PARAMS"]["WIDTH"])?>';
			<?elseif(strlen($arResult["ERROR_MESSAGE"]) > 0):?>
				top.bxBlogImageError = '<?=CUtil::JSEscape($arResult["ERROR_MESSAGE"])?>';
			<?endif;?>
		</script>
		<?
		die();
	}
	else
	{
		?>
		<form action="<?=POST_FORM_ACTION_URI?>" name="REPLIER" method="post" enctype="multipart/form-data" target="_self">
		<input type="hidden" name="microblog" value="Y">
		<input type="hidden" id="DATE_PUBLISH_DEF" name="DATE_PUBLISH_DEF" value="<?=$arResult["PostToShow"]["DATE_PUBLISH"];?>">
		<?=bitrix_sessid_post();?>
		<div id="blog-post-edit-micro-form" class="blog-edit-form blog-edit-post-form blog-post-edit-form" style="display:none;">
			<div class="blog-post-message blog-edit-editor-area blog-edit-field-text">
				<div class="blog-comment-field blog-comment-field-bbcode">
					<?
					include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/lhe.php");
					?>
				</div>
				<div class="blog-post-buttons blog-edit-buttons">
					<input type="hidden" name="save" value="Y">
					<input tabindex="4" type="submit" id="add-microblog" name="save" value="<?=GetMessage("BLOG_SEND_MICRO")?>">
				</div>
			</div>
		</div>
		</form>
		<?
	}
}
?>
</div>