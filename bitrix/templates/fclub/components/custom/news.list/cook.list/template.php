<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div id="new_cooks" class="user_list">
    <h5>Встречайте новичков</h5>
    <table>
        <?$i = 0;?>
        <?foreach($arResult["USERS"] as $key => $arItem):?>
            <?if(strlen($arItem["LOGIN"]) > 0):?>
                <?if($key % 3 == 0):?><tr><?endif;?><?$i++;?>
                <td>
                    <div class="author"><?if(strlen($arItem["PERSONAL_PHOTO"]) > 0):?><div class="photo"><div class="big_photo"><div><a href="/profile/<?=$arItem['ID']?>/" class="nickname"><img src="<?=$arItem['photo']['SRC']?>" width="100" height="100" alt="<?=$arItem["LOGIN"]?>"></a></div></div><img src="<?=$arItem['photo']['SRC']?>" width="30" height="30" alt="<?=$arItem["LOGIN"]?>"></div><?else:?><div class="photo"><div class="big_photo"><div><a href="/profile/<?=$arItem['ID']?>/" class="nickname"><img src="/images/avatar/avatar.jpg" width="100" height="100" alt="<?=$arItem["LOGIN"]?>"></a></div></div><img src="/images/avatar/avatar_small.jpg" width="30" height="30" alt="<?=$arItem["LOGIN"]?>"></div><?endif;?><a href="/profile/<?=$arItem['ID']?>/" class="nickname"><?if(strlen($arItem["NAME"]) > 0 && strlen($arItem["LAST_NAME"]) > 0):?><?=$arItem["NAME"]?> <?=$arItem["LAST_NAME"]?><?else:?><?=$arItem["LOGIN"]?><?endif;?></a></div>
                    <div class="intro"><?if(strlen($arItem["UF_ABOUT_SELF"]) > 100){echo substr($arItem["UF_ABOUT_SELF"],0,100)."...";}else{echo $arItem["UF_ABOUT_SELF"];}?></div>
                </td>
                <?if($i % 3 == 0 || $i == $arResult["USERS"]["USERS_COUNT"]):?></tr><?endif;?>
            <?endif;?>
        <?endforeach;?>
    </table>
</div>