<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Кулинарная книга, повавренная книга");
$APPLICATION->SetPageProperty("description", "Книжная лавка Foodclub — продажа кулинарных и поваренных книг.");
$APPLICATION->SetTitle("Кулинарные и поваренные книги в книжной лавке на Foodclub.ru");

if (CModule::IncludeModule("advertising")){
    $strBanner = CAdvBanner::Show("right_banner");
}

if (!$USER->IsAuthorized()){
    LocalRedirect("/auth/?backurl=/discounts/");
}
?>
<div id="content">
	<div id="text_space">
		<h1>Скидки</h1>
		<?$APPLICATION->IncludeFile("/discounts/text.inc.php", Array(), Array("MODE"=>"html"))?>
        <table class="discounts">
		<?if (CModule::IncludeModule("iblock")){
			$rsBooks = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ACTIVE"=>"Y", "IBLOCK_CODE"=>"discount"), false, false, Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "PREVIEW_PICTURE", "PROPERTY_cost", "PROPERTY_link"));
			$bFirst = true;

            $cell = 0;
			while ($arBook = $rsBooks->GetNext()) {
			    if($cell == 0){ echo "<tr>";}

				    if(IntVal($arBook['PREVIEW_PICTURE']) > 0){
					    $rsFile = CFile::GetByID($arBook['PREVIEW_PICTURE']);
					    $arFile = $rsFile->Fetch();
				    }?>
				    <td>
				    <? if(StrLen($arBook['DETAIL_TEXT']) > 0){ ?>
				        <h2><a href="./detail/?p=<?=$arBook['ID']?>"><?=$arBook['NAME']?></a></h2>
				    <?} else {?>
				        <h2><span><?=$arBook['NAME']?></span></h2>
				    <?}?>
				        <?if(isset($arFile)){?>
				            <div class="image">
				            <? if(StrLen($arBook['DETAIL_TEXT']) > 0){ ?><a href="./detail/?p=<?=$arBook['ID']?>"><?}?>
				            <img src="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" alt="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" title="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" width="<?=$arFile['WIDTH']?>" height="<?=$arFile['HEIGHT']?>"></div>
				            <? if(StrLen($arBook['DETAIL_TEXT']) > 0){ ?></a><?}?>

				        <?}?>
						<?if(StrLen($arBook['PREVIEW_TEXT']) > 0){?>
						    <div class="description"><?=$arBook['~PREVIEW_TEXT']?></div>
				        <?}?>
				    </td>
				    <?
				$cell++;
				if($cell == 3){echo "</tr>"; $cell = 0;}
		    }
            if($cell == 1){echo "<td></td><td></td></tr>";}
            if($cell == 2){echo "<td></td></tr>";}
	    }?>
		</table>
	</div>
	<div id="banner_space">
		<?
		$rsRandomDoUKnow = CIBlockElement::GetList(array("rand"=>"asc"),array("IBLOCK_CODE"=>"do_u_know"),false,false,array("ID","PREVIEW_TEXT"));
		if($arRandomDoUKnow = $rsRandomDoUKnow->Fetch()){?>
		<div id="do-you-know-that" class="b-facts">
			<div class="b-facts__heading">Знаете ли вы что:</div>
			<div class="b-facts__content">
				<div class="b-facts__item" data-id="<?=$arRandomDoUKnow["ID"]?>">
					<?=$arRandomDoUKnow["PREVIEW_TEXT"]?>
				</div>
			</div>
			<div class="b-facts__more">
				<a href="#" class="b-facts__more__link">Еще</a>
			</div>
		</div>
		<?}?>
	    <?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		<?require($_SERVER["DOCUMENT_ROOT"]."/facebook_box.html");?>
    </div>
    <div class="clear"></div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

