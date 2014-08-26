<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "Кулинарная книга, повавренная книга");
$APPLICATION->SetPageProperty("description", "Книжная лавка Foodclub — продажа кулинарных и поваренных книг.");
$APPLICATION->SetTitle("Кулинарные и поваренные книги в книжной лавке на Foodclub.ru");
?>
<div id="content">
	<div class="book_store">
		<h1>Книжная лавка</h1>
		<table class="books">
		<?$APPLICATION->IncludeFile("/book_store/text.inc.php", Array(), Array("MODE"=>"html"))?>
		<?if (CModule::IncludeModule("iblock")){
			$rsBooks = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ACTIVE"=>"Y", "IBLOCK_CODE"=>"books"), false, false, Array("NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "PROPERTY_cost", "PROPERTY_link"));
			$bFirst = true;

			while ($arBook = $rsBooks->GetNext()) {
			    if($bFirst){ echo "<tr>";}
				    if(IntVal($arBook['PREVIEW_PICTURE']) > 0){
					    $rsFile = CFile::GetByID($arBook['PREVIEW_PICTURE']);
					    $arFile = $rsFile->Fetch();
				    }?>
				    <td>
					    <?if(isset($arFile)){?><div class="image"><img src="/upload/<?=$arFile['SUBDIR']?>/<?=$arFile['FILE_NAME']?>" alt="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" title="<?=(strlen($arFile['DESCRIPTION']) > 0 ? $arFile['DESCRIPTION'] : $arFields['NAME'])?>" width="<?=$arFile['WIDTH']?>" height="<?=$arFile['HEIGHT']?>"></div><?}?>
					    <div class="description">

						    <div class="padding">
							    <h2><?=$arBook['NAME']?></h2>
							    <?if(StrLen($arBook['PREVIEW_TEXT']) > 0){?><p><?=$arBook['~PREVIEW_TEXT']?></p><?}?>
							    <div class="price num">
							        <div><?=$arBook['PROPERTY_COST_VALUE']?> <span>руб.</div>
							        <a href="<?=$arBook['PROPERTY_LINK_VALUE']?>" target="_blank"></a>
							     </div>
						    </div>
					    </div>
					    <div class="clear"></div>
				    </td>
				    <?
				if(!$bFirst){echo "</tr>"; $bFirst = true;} else {$bFirst = false;}
		    }
            if(!$bFirst){echo "</tr>";}
	    }?>
		</table>
		<div class="clear"></div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

