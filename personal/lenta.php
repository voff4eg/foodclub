<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?$APPLICATION->SetAdditionalCSS("/css/profile.css");
$APPLICATION->SetAdditionalCSS("/css/elem.css");?>
<?

if (CModule::IncludeModule("advertising")){ $strBanner = CAdvBanner::Show("right_banner"); }

$arUser = $APPLICATION->IncludeComponent("custom:profile", "", array());
$APPLICATION->SetPageProperty("title", $arUser['FULLNAME']." &mdash; лента пользователя на Foodclub");
?>
<div id="content">
	<div class="b-personal-page">
	<?$APPLICATION->IncludeFile("/personal/.profile_header.php", Array(
		"USER" => $arUser)
	);?>
	
	 <?$APPLICATION->IncludeComponent(
		"custom:profile_menu",
		"",
		Array(
			"ROOT_MENU_TYPE" => "profile",
			"MAX_LEVEL" => "1",
			"CHILD_MENU_TYPE" => "profile",
			"USE_EXT" => "N",
			"DELAY" => "N",
			"ALLOW_MULTI_SELECT" => "N",
			"MENU_CACHE_TYPE" => "N",
			"MENU_CACHE_TIME" => "3600",
			"MENU_CACHE_USE_GROUPS" => "Y",
			"MENU_CACHE_GET_VARS" => ""
		),
	false
	);?>

	<?
	$APPLICATION->IncludeComponent("custom:profile.lenta", "", array("USER"=>$arUser));
	?>
	</div>
	<div id="banner_space">
		<?$APPLICATION->IncludeComponent("custom:profile.badges", "", array("USER" => $arUser));?>
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
	<?
		$rsThematicBlock = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
											Array("ACTIVE"=>"Y", "IBLOCK_CODE"=>"thematic_bloc", "PROPERTY_place_VALUE"=>"home"),
											false,
											false,
											Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_recipe", "PROPERTY_place")
										);
		$ResDump = Array();
		while($Bl = $rsThematicBlock->GetNext())
		{
			$ResDump[ $Bl['ID'] ][] = $Bl['PROPERTY_RECIPE_VALUE'];
			
			if(!isset( $Themes[ $Bl['ID'] ] ))
			{
				$Themes[ $Bl['ID'] ] = $Bl;
			}
		}
		
		foreach($Themes as $Block){	
			$strBlockHTML = '';
			$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ID"=>$ResDump[ $Block['ID'] ]), false, false, Array("ID", "NAME", "CREATED_BY", "PROPERTY_comment_count"));
			
			while($arRecipe = $rsRecipes->GetNext()){
				$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
				$arUser = $rsUser->Fetch();
				
				$arRecipe['USER'] = $arUser;
				
				$strBlockHTML .= '<li><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a>&nbsp;<span class="comments">(<a href="/detail/'.$arRecipe['ID'].'/#comments">'.IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a>)</span><span class="author">От: '.$arRecipe['USER']['LOGIN'].'</span></li>';
			}?>
			<div class="thematic_block">
				<?
				if(IntVal($Block['PREVIEW_PICTURE']) > 0){
					$rsFile = CFile::GetByID(IntVal($Block['PREVIEW_PICTURE']));
					$arFile = $rsFile->Fetch();
					$arFile['SRC'] = "/upload/".$arFile['SUBDIR']."/".$arFile['FILE_NAME'];					
				?>
				<img src="<?=$arFile['SRC']?>" width="<?=$arFile['WIDTH']?>" height="<?=$arFile['HEIGHT']?>" alt="<?=$arFile['DESCRIPTION']?>" class="thematic_pic">
				<?}?>
				<h2><a href="http://foodclub.ru/search/<?=$Block['NAME']?>/"><?=$Block['NAME']?></a></h2>
				<ul class="recipes">
				<?=$strBlockHTML?>
				</ul>
			</div>
		<?}?>
	</div>
	<div class="clear"></div>
</div>
<?//	$obCache->EndDataCache();
//endif;
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
