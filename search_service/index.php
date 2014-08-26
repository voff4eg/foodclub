<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");

if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

$intID = intval($_REQUEST['id']);
//$intID = 1175;

$rsIng = CIBlockElement::GetById($intID);
$Ing = $rsIng->GetNext();

$APPLICATION->SetTitle($Ing['NAME']);


$rowStages = CIBlockElement::GetList(
    array(
        "SORT"=>"ASC"
    ),
    array(
        "IBLOCK_ID" => 4,
        "ACTIVE" => "Y",
        "PROPERTY_ingredient" => $intID
    ),
    false,
    false,
    array(
        "ID",
    )
    
);

while($Stage = $rowStages->GetNext())
{
    $arStages[] = $Stage['ID'];
}

if( count($arStages) > 0 )
{

    $rowRecipes = CIBlockElement::GetList(
        array(
            "NAME"=>"ASC"
        ),
        array(
            "IBLOCK_ID" => 5,
            "ACTIVE" => "Y",
            "PROPERTY_recipt_steps" => $arStages
        ),
        false,
        Array("nPageSize" => 100000),
        Array("ID", "NAME", "PREVIEW_TEXT", "PREVIEW_PICTURE", "CREATED_BY", "PROPERTY_dish_type", "PROPERTY_kitchen", "PROPERTY_comment_count")
    );

    $cell = "l";
    while($arRecipe = $rowRecipes->GetNext())
    {
        $rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
	    $arUser = $rsUser->Fetch();
	
	    $arRecipe['USER'] = $arUser;
			
	    if(IntVal($arRecipe['PREVIEW_PICTURE']) > 0){
		    $rsFile = CFile::GetByID(IntVal($arRecipe['PREVIEW_PICTURE']));
		    $arFile = $rsFile->Fetch();
		    $arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];
		    $arRecipe["PREVIEW_PICTURE"] = $arFile;
	    }
	
	    $arRecipe['PROPERTY_COMMENT_COUNT_VALUE'] = IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']);
	    $arRecipeHTML[ $cell ][] = 
<<<HTML
<div class="item">
	<div class="photo">
		<div class="big_photo">
			<div>
				<table class="frame">
					<tr><td class="tl"><img src="/images/spacer.gif" width="11" height="11" alt=""></td><td class="top"><img src="/images/spacer.gif" width="1" height="11" alt=""></td><td class="tr"><img src="/images/spacer.gif" width="14" height="11" alt=""></td></tr><tr><td class="left"><img src="/images/spacer.gif" width="11" height="1" alt=""></td>
						<td class="middle"><a href="/detail/{$arRecipe['ID']}/"><img src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" width="{$arRecipe['PREVIEW_PICTURE']['WIDTH']}" height="{$arRecipe['PREVIEW_PICTURE']['HEIGHT']}" alt=""></a></td>
						<td class="right"><img src="/images/spacer.gif" width="14" height="1" alt=""></td></tr><tr><td class="bl"><img src="/images/spacer.gif" width="11" height="14" alt=""></td><td class="bottom"><img src="/images/spacer.gif" width="1" height="14" alt=""></td><td class="br"><img src="/images/spacer.gif" width="14" height="14" alt=""></td>
					</tr>
				</table>
			</div>
		</div>
		<img src="{$arRecipe['PREVIEW_PICTURE']['SRC']}" width="50" height="33" alt="">
	</div>
	<div class="link"><a href="/detail/{$arRecipe['ID']}/">{$arRecipe['NAME']}</a>&nbsp;<span class="comments">(<a href="/detail/{$arRecipe['ID']}/#comments">{$arRecipe['PROPERTY_COMMENT_COUNT_VALUE']}</a>)</span><span class="author">От: {$arRecipe['USER']['LOGIN']}</span></div>
	<div class="clear"></div>
</div>
HTML;
        
        if($cell == "r"){$cell = "l";}else{$cell = "r";}
    }

    $NavString = "";
    if($rowRecipes->IsNavPrint()){
	    $NavString = $rowRecipes->GetPageNavStringEx($navComponentObject, "Рецепты", "service_search", "N");
    }
    ?>
    <div id="content">
	    <div id="text_space">
            <h1>Рецепты, которые содержат: <?=$Ing['NAME']?></h1>
            <div class="two_column pages_recipes">
                <?foreach( $arRecipeHTML['l'] as $Item ){ echo $Item;}?>
            </div>
            <div class="two_column pages_recipes">
                <?foreach( $arRecipeHTML['r'] as $Item ){ echo $Item;}?>
            </div>
            <div class="clear"></div>
            <?=$NavString;?>
        </div>
        <div id="banner_space">
            <?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		</div>
		<div class="clear"></div>
    </div>

<?
} else {
    ?>
    <div id="content">
	    <div id="text_space">
            <h1>Рецепты, которые содержат: <?=$Ing['NAME']?>. Но мы ничего не нашли.</h1>
            <div class="clear"></div>
        </div>
        <div id="banner_space">
            <?if(strlen($strBanner) > 0){?><div class="banner"><?=$strBanner?></div><?}?>
		</div>
		<div class="clear"></div>
    </div>
    <?
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
