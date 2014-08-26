<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	
CModule::IncludeModule("iblock");
if( $USER->IsAdmin() || in_array(5, $USER->GetParam("GROUPS")) ){
$APPLICATION->SetPageProperty("title", "Foodclub");
$APPLICATION->SetTitle("Foodclub");

$CFClub = CFClub::getInstance();

$arRecipesTree = $CFClub->getRecipesTree();
$arKitchens = $CFClub->getKitchens();

//echo "<pre>"; print_r($arKitchens); echo "</pre>";

$strKitchenHTML = '';
$strRecipeHTML = '';

foreach($arKitchens as $strKey => $arKitchen){
	
	$strKitchenHTML .= '<li>';
	if(!is_null($arKitchen['DISH']) > 0){
		$strKitchenHTML .= '<a href="#k'.$strKey.'">'.$arKitchen['NAME'].'</a>';
	} else {
		$strKitchenHTML .= $arKitchen['NAME'];
	}
	$strKitchenHTML .= '<span>'.$arKitchen['NAME'].'</span></li>';
	
	$strRecipeHTML .= '<div id="k'.$strKey.'" class="dishes_list act"><h2>'.$arKitchen['NAME'].'</h2><div class="left_column">';
	foreach($arKitchen['DISH'] as $arDish){
		if( count($arRecipesTree[ $arKitchen['ID'] ][ $arDish['ID'] ]) > 0){
			$strRecipeHTML .= '<ul><h3 class="h5">'.$arDish['NAME'].'</h3>';
			foreach($arRecipesTree[ $arKitchen['ID'] ][ $arDish['ID'] ] as $arRecipe){
				$strRecipeHTML .= '<li><a href="/admin/recipe/edit/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a></li>';
			}
			$strRecipeHTML .= '</ul>';
		}
	}
	$strRecipeHTML .= '</div><div class="clear"></div></div>';
};

?>
<div class="body">
	<div id="cooking_list">
		<h2>Рецепты</h2>
		<ul>
			<h3 class="h5">Кухни</h3>
			<?=$strKitchenHTML?>
		</ul>
	</div>
	<?=$strRecipeHTML?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
<?} else {
	LocalRedirect("/auth/?backurl=/admin/edit/");
}
?>
