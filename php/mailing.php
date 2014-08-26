<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $SUBSCRIBE_TEMPLATE_RUBRIC;
$SUBSCRIBE_TEMPLATE_RUBRIC=$arRubric;
global $APPLICATION;


CModule::IncludeModule("iblock");
CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/factory.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/main.class.php');
$CFClub = new CFClub();

$rowDateFrom = strtotime(date("d.m.Y")) - 604800;
//$rowDateFrom = strtotime(date("d.m.Y")) - 4*604800;
$DateFrom = date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT")), $rowDateFrom);

$arFilter = array(
	"dates" => array(
		"from"=>$DateFrom,
	)
);
$arRecipes = $CFClub->GetList(0, $arFilter);

$dbPosts = CBlogPost::GetList(  Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC"),
								Array("PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH,
								">=DATE_PUBLISH"=>$DateFrom,
								),
								false,
								Array('nPageSize'=>0)
							 );

while ($arPost = $dbPosts->Fetch()){
	$arPosts[] = $arPost;
}

$rsGroup = CSocNetGroup::GetList(Array(), Array(), false, false);

/**
 * TODO Необходимо собрать уникальные ID блогов и запрашивать информацию только по ним вне цикла.
 */
while ($arGroup = $rsGroup->GetNext()) {
	$arBlog = CBlog::GetBySocNetGroupID($arGroup['ID']);
	$arBlogs[ $arBlog['ID'] ] = $arGroup;
}


//echo "<pre>"; print_r($arBlogs); echo "</pre>";
$parser = new blogTextParser;

?>

<table style="border-collapse: collapse; width: 640px; margin: 0 auto;">
	<tr>
		<td style="border-collapse: collapse; vertical-align: top; background-color: #fff; padding: 0;">
		
			<div style="padding: 18px 0 19px 0; border-bottom: 1px solid #ececec; margin: 0 0 16px 0;">
				<table style="border-collapse: collapse; width: 640px; padding: 0; margin: 0;">
					<tr>
						<td style="border-collapse: collapse; vertical-align: top; padding: 0 0 0 33px;">
							<a href="http://<?=$_SERVER['HTTP_HOST']?>/?subscribe" target="_blank" style="color: #990000; text-decoration: none; padding: 0; margin: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/logo.gif" width="102" height="72" alt="Foodclub.ru" style="border: 0; vertical-align: bottom; cursor: pointer; padding: 0; margin: 0;"></a>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; padding: 24px 0 0 0; text-align: center; width: 160px;">
							<div style="text-transform: uppercase; color: #333; font-weight: bold; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">Рецепты</div>
							<div style="font-style: italic; color: #999; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">с пошаговыми<br>фотографиями</div>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; padding: 24px 0 0 0; text-align: center; width: 160px;">
							<div style="text-transform: uppercase; color: #333; font-weight: bold; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">Истории</div>
							<div style="font-style: italic; color: #999; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">пользователей</div>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; padding: 24px 0 0 0; text-align: center; width: 160px;">
							<div style="text-transform: uppercase; color: #333; font-weight: bold; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">Конкурсы</div>
							<div style="font-style: italic; color: #999; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px; line-height: 14px; padding: 0; margin: 0;">для кулинаров</div>
						</td>

					</tr>
				</table>
			</div>
			<?
			if( count($arRecipes['ITEMS']) > 0 ){
			?>
			<div style="padding: 0 32px; border-bottom: 1px solid #ececec; margin: 0 0 16px 0;">
				<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-weight: bold; font-size: 18px; color: #000; text-align: center; margin: 0 0 18px 0; padding: 0;">Новые рецепты</div>
				
				<div style="padding: 0; margin: 0;">
					<?foreach($arRecipes['ITEMS'] as $Item){?>
					<div style="display: inline; float: left; height: 210px; margin: 0 4px 8px 0; padding: 0; width: 188px;">
						<?
						if(!empty($Item['PREVIEW_PICTURE']) && $Item['PREVIEW_PICTURE']['WIDTH'] > 0 && $Item['PREVIEW_PICTURE']['HEIGHT'] > 0){
							$mt = (-1)*((170 / $Item['PREVIEW_PICTURE']['WIDTH'])*$Item['PREVIEW_PICTURE']['HEIGHT']-113)/2;
							if($mt > 0){
								$mt = 0;//$mt*(-1);
							}
						?>
						<div style="background: #fff; border: 1px solid #e3e3e1; box-shadow: 0 0 3px #c8c8c8; -webkit-box-shadow: 0 0 3px #c8c8c8; -moz-box-shadow: 0 0 3px #c8c8c8; -khtml-box-shadow: 0 0 3px #c8c8c8; display: block; margin: 0 0 8px 0; padding: 9px; text-align: center;"><a title="<?=$Item['NAME']?>" href="http://<?=$_SERVER['HTTP_HOST']?>/detail/<?=$Item['ID']?>/?subscribe&amp;new-recipe" target="_blank" style="color: #990000; text-decoration: none; display: inline-block; height: 113px; overflow: hidden; width: 170px; padding: 0; margin: 0;"><img width="170" alt="<?=$Item['NAME']?>" src="http://<?=$_SERVER['HTTP_HOST']?><?=str_replace(" ", "%20", $Item['PREVIEW_PICTURE']['SRC'])?>" style=" padding: 0; margin: 0; margin-top: <?=$mt?>px; border: 0; vertical-align: bottom; cursor: pointer;"></a></div>
						<?}?>
						<div style="margin: 0 10px 2px 10px; padding: 0;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/detail/<?=$Item['ID']?>/?subscribe&amp;new-recipe" class="b-recipes__list__item__heading" target="_blank" style="color: #990000; text-decoration: none; font-size: 14px; font-family: Georgia, 'Times New Roman', Times, serif; padding: 0; margin: 0;"><?=$Item['NAME']?></a></div>
						<div style="color: #999999; font-size: 10px; margin: 0 0 9px 10px; padding: 0; font-family: Arial, Helvetica, sans-serif;">От: <?=$Item['USER']['LOGIN']?></div>
					</div>
					<?
					}
					?>										
					<div style="clear: both; height: 0; overflow: hidden; width: 1px;"></div>
				</div>
			</div>
			<?
			}
			?>			
			<?
			if( count($arPosts) > 0 ){
			?>
			<div style="padding: 0 20px 11px; border-bottom: 1px solid #ececec; margin: 0 0 18px 0;">
				<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-weight: bold; font-size: 18px; color: #000; text-align: center; margin: 0 0 7px 0;">Новые записи</div>
				<div style="margin: 0; padding: 0;">
				<?
				foreach($arPosts as $arPost){
				?>
			<?
			$res = CBlogImage::GetList(array("ID"=>"ASC"), array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arPost['BLOG_ID']));

			while ($arImage = $res->Fetch())
				$arImages[$arImage['ID']] = $arImage['FILE_ID'];

			$text = $parser->convert($arPost['DETAIL_TEXT'], true, $arImages);
			$text = substr(strip_tags($text), 0,200)."...";
			//$text = str_replace('src="/','border="0" src="http://'.$_SERVER['HTTP_HOST'].'/',$text);
			$text = str_replace('<a ','<a style="color:#990000;"',$text);



			$rsUser = CUser::GetByID($arPost['AUTHOR_ID']);
			$arUser = $rsUser->Fetch();

			$arDate = explode(" ", $arPost['DATE_PUBLISH']);
			?>
				<div style="margin: 0 0 6px 0; padding: 0;">
					<div style="text-align: center; margin: 0 0 7px 0; padding: 0;"><img src="http://www.foodclub.ru/images/mailing/subscribe/bullet.gif" width="7" height="7" alt="" style="border: 0; vertical-align: bottom; margin: 0; padding: 0;"></div>
					<div style="text-align: center; margin: 0 0 1px 0; padding: 0; font-size: 11px; font-family: Georgia, 'Times New Roman', Times, serif; color: #000;"><?=$arBlogs[ $arPost['BLOG_ID'] ]['NAME']?></div>
					<div style="text-align: center; margin: 0; padding: 0;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/blogs/group/<?=$arBlogs[ $arPost['BLOG_ID'] ]['ID']."/blog/".$arPost['ID']?>/?subscribe&amp;new-entry" target="_blank" style="color: #990000; text-decoration: none; font-size: 14px; font-family: Georgia, 'Times New Roman', Times, serif; margin: 0; padding: 0;"><?=$arPost['TITLE']?></a></div>
				</div>
				<?
				}
				?>
				</div>
			</div>
			<?
			}
			?>
			<?
			$strBlockHTML = '';
			$rsThematicBlock = CIBlockElement::GetList( Array("SORT"=>"ASC"), 
											Array("ACTIVE"=>"N", "IBLOCK_CODE"=>"thematic_bloc", "CODE"=>"mailing"),
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
				$rsRecipes = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("ID"=>$ResDump[ $Block['ID'] ]), false, false, Array("ID", "NAME", "CREATED_BY", "PREVIEW_PICTURE", "PROPERTY_comment_count"));
				while($arRecipe = $rsRecipes->GetNext()){
					$rsUser = CUser::GetByID($arRecipe['CREATED_BY']);
					$arUser = $rsUser->Fetch();
					
					$arRecipe['USER'] = $arUser;
					
					//$strBlockHTML .= '<a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a>&nbsp;<span class="comments">(<a href="/detail/'.$arRecipe['ID'].'/#comments">'.IntVal($arRecipe['PROPERTY_COMMENT_COUNT_VALUE']).'</a>)</span><span class="author">От: '.$arRecipe['USER']['LOGIN'].'</span>';
					$strBlockHTML .= '<div style="width: 150px; display: inline; float: left; margin: 0 1px 0 0; padding: 0;">'.PHP_EOL;
					if(intval($arRecipe["PREVIEW_PICTURE"]) > 0){
						$arFile = CFile::GetFileArray($arRecipe["PREVIEW_PICTURE"]);
						$mt = (-1)*((150/$arFile['WIDTH'] )*$arFile['HEIGHT'] - 99)/2;
						if($mt > 0){
							$mt = 0;//$mt*(-1);
						}
						//echo $arFile["SRC"]." - ".str_replace(" ","%20",$arFile["SRC"])."<br>";
						$strBlockHTML .= '<div style="margin: 0 0 9px 0; padding: 0; text-align: center;">'.PHP_EOL;
						$strBlockHTML .= '<a title="'.$arRecipe['NAME'].'" href="http://'.$_SERVER[HTTP_HOST].'/detail/'.$arRecipe['ID'].'/?subscribe&amp;block-recipe" target="_blank" style="color: #990000; text-decoration: none; display: inline-block; height: 99px; overflow: hidden; width: 150px; margin: 0; padding: 0;"><img width="150" alt="'.$arRecipe['NAME'].'" src="http://'.$_SERVER['HTTP_HOST'].str_replace(" ","%20",$arFile['SRC']).'" style="margin: 0; padding: 0; margin-top: '.$mt.'px; border: 0; vertical-align: bottom; cursor: pointer;"></a>'.PHP_EOL;
						$strBlockHTML .= '</div>'.PHP_EOL;
						//$strBlockHTML .= '<div class="photo"><a title="'.$arRecipe['NAME'].'" href="/detail/'.$arRecipe['ID'].'/">';
						//$strBlockHTML .= '<img width="150" alt="'.$arRecipe['NAME'].'" src="'.$arFile['SRC'].'"></a></div>';
					}
					$strBlockHTML .= '<div style="margin: 0 10px 2px 10px; padding: 0;"><a href="http://'.$_SERVER['HTTP_HOST'].'/detail/'.$arRecipe['ID'].'/?subscribe&amp;block-recipe" target="_blank" style="color: #990000; text-decoration: none; font-size: 14px; font-family: Georgia, \'Times New Roman\', Times, serif; margin: 0; padding: 0;">'.$arRecipe['NAME'].'</a></div>'.PHP_EOL;
					$strBlockHTML .= '<div style="color: #999999; font-size: 10px; margin: 0 0 9px 10px; padding: 0; font-family: Arial, Helvetica, sans-serif;">От: '.$arRecipe['USER']['LOGIN'].'</div>'.PHP_EOL;
					//$strBlockHTML .= '<h5><a href="/detail/'.$arRecipe['ID'].'/">'.$arRecipe['NAME'].'</a></h5>';
					//$strBlockHTML .= '<p class="author">От: '.$arRecipe['USER']['LOGIN'].'</p>';
					$strBlockHTML .= '</div>'.PHP_EOL;
				}
			}
			//die;
			?>
			<?if(strlen($strBlockHTML) > 0):?>
			<div style="padding: 2px 18px 8px; border-bottom: 1px solid #ececec; margin: 0 0 16px 0;">
				<div style="margin: 0; padding: 0;">
					<?=$strBlockHTML?>
					<div style="clear: both; height: 0; overflow: hidden; width: 1px;"></div>
				</div>
			</div>
			<?endif;?>
			<div style="padding: 0 0 16px 0; border-bottom: 1px solid #ececec; margin: 0 0 16px 0;">
				<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-weight: bold; font-size: 18px; color: #000; text-align: center; margin: 0 0 12px 0; padding: 0;">Рецепт месяца</div>
				<table style="border-collapse: collapse; width: 640px; margin: 0; padding: 0; color: #000;">
					<tr>
						<td style="border-collapse: collapse; vertical-align: top; width: 170px; text-align: right; padding: 7px 0 0 0; margin: 0; color: #000;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/recipe-of-month/?subscribe&amp;recipe-om" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/recipe-om-ill.jpg" width="100" height="99" alt="Конкурс Рецепт месяца" title="Конкурс Рецепт месяца" style="border: 0; vertical-align: bottom; cursor: pointer; margin: 0; padding: 0;"></a></td>
						<td style="border-collapse: collapse; vertical-align: top; text-align: center; padding: 0 30px; margin: 0; color: #000; font-family: Georgia, 'Times New Roman', Times, serif; font-size: 12px;">
							<div style="margin: 0 0 13px 0; padding: 0; color: #000;">Каждый месяц приглашенный эксперт будет выбирать лучший из рецептов, опубликованных на нашем сайте.</div>
							<div style="margin: 0 0 13px 0; padding: 0; color: #000;">Для участия в <a href="http://<?=$_SERVER['HTTP_HOST']?>/recipe-of-month/?subscribe&amp;recipe-om" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;">конкурсе</a> надо опубликовать свой пошаговый рецепт на сайте, а там уж эксперты выберут достойнейший и мы вручим приз победителю.</div>
							<div style="margin: 0; padding: 0;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/recipe/add/?subscribe&amp;recipe-om&amp;add-recipe" target="_blank" class="b-recipes-om__add__link" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/add.gif" width="138" height="31" alt="Добавить рецепт" style="border: 0; vertical-align: bottom; cursor: pointer; margin: 0; padding: 0;"></a></div>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; width: 170px; text-align: left; padding: 7px 0 0 0; color: #000;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/recipe-of-month/?subscribe&amp;recipe-om" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/recipe-om-quest.gif" width="98" height="98" alt="Конкурс Рецепт месяца" title="Конкурс Рецепт месяца" style="border: 0; vertical-align: bottom; cursor: pointer; margin: 0; padding: 0;"></a></td>
					</tr>
				</table>
			</div>
			
			<div style="margin: 0 0 55px 0;">
				<table style="border-collapse: collapse; margin: 0; width: 640px;">
					<tr>
						<td style="border-collapse: collapse; vertical-align: top; padding: 0 30px 0 17px;">
							<a href="http://<?=$_SERVER['HTTP_HOST']?>/profile/2/?subscribe" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/author-photo.jpg" width="94" height="94" alt="Мария Савельева 
Главный редактор Foodclub.ru" style="border: 0; vertical-align: bottom; cursor: pointer; margin: 0; padding: 0;"></a>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; width: 499px; padding: 18px 0 0 0;">
							<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #000; margin: 0 0 22px 0; padding: 0;">
							Друзья, спасибо за то, что вы с нами!<br />Мы очень стараемся, чтобы вам было интересно и вкусно с <a href="http://<?=$_SERVER['HTTP_HOST']?>/?subscribe&amp;add-recipe" target="_blank" style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #990000; text-decoration: none; margin: 0; padding: 0;">Foodclub.ru</a>. 
							Ждем ваших <a href="http://<?=$_SERVER['HTTP_HOST']?>/all/?subscribe" target="_blank" style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #990000; text-decoration: none; margin: 0; padding: 0;">отзывов</a>, <a href="http://<?=$_SERVER['HTTP_HOST']?>/blogs/?subscribe" target="_blank" style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #990000; text-decoration: none; margin: 0; padding: 0;">постов</a> и <a href="http://<?=$_SERVER['HTTP_HOST']?>/recipe/add/?subscribe&amp;add-recipe" target="_blank" style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #990000; text-decoration: none; margin: 0; padding: 0;">рецептов</a>.
							</div>
							<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 9pt; color: #000; margin: 0; padding: 0;">
							<a href="http://<?=$_SERVER['HTTP_HOST']?>/profile/2/?subscribe" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;">Мария Савельева</a>
							</div>
							<div style="font-family: Georgia, 'Times New Roman', Times, serif; font-size: 8pt; color: #000; margin: 0; padding: 0;">Главный редактор <a href="http://<?=$_SERVER['HTTP_HOST']?>/?subscribe" style="color: #990000; text-decoration: none; margin: 0; padding: 0;">Foodclub.ru</a></div>
						</td>
					</tr>
				</table>
			</div>
			
			<div style="background-color: #ececec; padding: 35px 18px 23px; margin: 0;">
				<table style="border-collapse: collapse; width: 604px;">
					<tr>
						<td style="border-collapse: collapse; vertical-align: top; color: #333; font-family: Arial, Helvetica, sans-serif; font-size: 11px; width: 390px; padding: 5px 0 0 0;">
							© Foodclub.ru. 
							Вы получаете эту рассылку потому что вы зарегистрировались на сайте <a href="http://<?=$_SERVER['HTTP_HOST']?>/?subscribe" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;">Foodclub.ru</a> и подписаны на еженедельную рассылку.
							
							<div style="margin: 18px 0 0 0; padding: 0;"><a href="http://<?=$_SERVER['HTTP_HOST']?>/profile/subscribe/?subscribe&amp;unsubscribe" target="_blank" style="color: #990000; text-decoration: none; font-family: Arial, Helvetica, sans-serif; font-size: 11px; margin: 0; padding: 0;">Отписаться от рассылки</a></div>
						</td>
						<td style="border-collapse: collapse; vertical-align: top; width: 195px; padding: 0;">
						
							<table class="b-footer-table__iphone__table" style="border-collapse: collapse; margin: 0; padding: 0;">
								<tr>
									<td style="border-collapse: collapse; vertical-align: top; width: 93px; padding: 0;">
										<a href="http://<?=$_SERVER['HTTP_HOST']?>/iphone/?subscribe&amp;iphone" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><img src="http://<?=$_SERVER['HTTP_HOST']?>/images/mailing/subscribe/iphone.jpg" width="92" height="112" alt="Foodclub HD" style="border: 0; vertical-align: bottom; cursor: pointer; margin: 0; padding: 0;"></a>
									</td>
									<td style="border-collapse: collapse; vertical-align: top; padding: 30px 0 0 7px; margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
										<a href="http://<?=$_SERVER['HTTP_HOST']?>/iphone/?subscribe&amp;iphone" target="_blank" style="color: #990000; text-decoration: none; margin: 0; padding: 0;"><b>Foodclub HD</b><br>
										Кулинарная книга<br>для вашего iPhone<br>и iPad</a>
									</td>
								</tr>
							</table>
							
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>

<?

/*return array(
	"SUBJECT"=>$SUBSCRIBE_TEMPLATE_RUBRIC["NAME"],
	"BODY_TYPE"=>"html",
	"CHARSET"=>"UTF-8",
	"DIRECT_SEND"=>"Y",
	"FROM_FIELD"=>$SUBSCRIBE_TEMPLATE_RUBRIC["FROM_FIELD"],
);
*/
?>

