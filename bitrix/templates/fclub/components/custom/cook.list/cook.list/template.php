<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?echo"@@<pre>";print_r($arResult);echo"</pre>@@";?>
<?foreach($arResult["USERS"] as $arItem):?>
    <?foreach($arItem["FIELDS"] as $code=>$value):?>
            <?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?>
    <?endforeach;?>
    <?foreach($arItem["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>
            <?=$arProperty["NAME"]?>:&nbsp;
            <?if(is_array($arProperty["DISPLAY_VALUE"])):?>
                    <?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
            <?else:?>
                    <?=$arProperty["DISPLAY_VALUE"];?>
            <?endif?>
    <?endforeach;?>
<?endforeach;?>
