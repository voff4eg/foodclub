<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");  

if (CModule::IncludeModule("advertising")){
	$strBanner_right = CAdvBanner::Show("right_banner");
	$strBanner_middle = CAdvBanner::Show("middle_banner");
}

$APPLICATION->SetPageProperty("title", "Кулинарные рецепты с фотографиями этапов приготовления. Foodclub.ru");
$APPLICATION->SetPageProperty("description", "Кулинарные рецепты со всего света. Удобный поиск, пошаговые фотографии кулинарных рецептов.");
$APPLICATION->SetPageProperty("keywords", "кулинарные рецепты, рецепт, кулинария, фото рецепты, рецепты с фотографиями, фото блюд");

CModule::IncludeModule("blog");
CModule::IncludeModule("socialnetwork");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/factory.class.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/classes/main.class.php');
$CFClub = new CFClub;
$CFactory = new CFactory;

$obCache = new CPHPCache;
if($obCache->InitCache(3600, "RecipesCountMain".SITE_ID, "/RecipesCountMain".SITE_ID)){
	$count = $obCache->GetVars();
}elseif($obCache->StartDataCache()){
    global $CACHE_MANAGER;
	$CACHE_MANAGER->StartTagCache("/RecipesCountMain");
	$count = $CFClub->getRecipesCount();
	$CACHE_MANAGER->RegisterTag("RecipesCountMainTag");	
	$CACHE_MANAGER->EndTagCache();
	$obCache->EndDataCache($count);
}else{
	$count = 0;
}
if($obCache->InitCache(900, "getOnlinecnt".SITE_ID, "/getOnlinecnt".SITE_ID)){
	$online = $obCache->GetVars();
}elseif($obCache->StartDataCache()){
	$online = $CFClub->getOnlineCount();
	$obCache->EndDataCache($online);
}else{
	$online = 0;
}
//Список записей блогов
if($obCache->InitCache(86400, "arBlogs_index".SITE_ID, "/blogs_index".SITE_ID)){
	$vars = $obCache->GetVars();
	$arBlogs = $vars["arBlogs"];
	$arPosts = $vars["arPosts"];
}elseif($obCache->StartDataCache()){
	$dbPosts = CBlogPost::GetList(  Array("DATE_PUBLISH" => "DESC", "NAME" => "ASC"), 
									Array("PUBLISH_STATUS" => BLOG_PUBLISH_STATUS_PUBLISH, /*"BLOG_ID" => array_keys($arBlogs),*/
									"<=DATE_PUBLISH"=>ConvertTimeStamp(date(), "FULL"),
									),
									false,
									Array('nPageSize'=>5)
								 );
								 
	while($arPost = $dbPosts->Fetch()){
		if(intval($arPost["BLOG_ID"]) > 0 && !isset($arBlogs[ $arPost["BLOG_ID"] ])){
			if($arBlog = CBlog::GetByID($arPost["BLOG_ID"])){
				$arBlogs[ $arBlog["ID"] ] = $arBlog;
			}
		}
		$dbCategory = CBlogPostCategory::GetList(Array("NAME" => "ASC"), Array("BLOG_ID" => $arPost["BLOG_ID"], "POST_ID" => $arPost["ID"]));
		while($arCategory = $dbCategory->GetNext()){
			$arCatTmp = $arCategory;
			$arCatTmp["urlToCategory"] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['SOCNET_GROUP_ID']."/blog/?category=".$arCatTmp['CATEGORY_ID'];
			$arPost["CATEGORY"][] = $arCatTmp;
		}
									
		$arPost['urlToDelete'] = "/blogs/group/".$arBlogs[ $arPost['BLOG_ID'] ]['ID']."/blog/?del_id=".$arPost['ID'];
		$res = CBlogImage::GetList(array("ID"=>"ASC"), array("POST_ID"=>$arPost['ID'], "BLOG_ID"=>$arPost['BLOG_ID']));
		
		while ($arImage = $res->Fetch())
		    $arImages[$arImage['ID']] = $arImage['FILE_ID'];
		
		$parser = new blogTextParser;
		$arParserParams = Array(
			"imageWidth" => "465",
			"imageHeight" => "600",
		);
		
		$arAllow = array("HTML" => "N", "ANCHOR" => "Y", "BIU" => "Y", "IMG" => "Y", "QUOTE" => "Y", "CODE" => "Y", "FONT" => "Y", "LIST" => "Y", "SMILES" => "Y", "NL2BR" => "N", "VIDEO" => "Y");
		
		$arPost["TEXT_FORMATTED"] = $parser->convert($arPost['DETAIL_TEXT'], true, $arImages, $arAllow, $arParserParams);
		if (preg_match("/(\[CUT\])/i",$arPost['DETAIL_TEXT']) || preg_match("/(<CUT>)/i",$arPost['DETAIL_TEXT']))
			$arPost["CUT"] = "Y";
	    
		$rsUser = CUser::GetByID($arPost['AUTHOR_ID']);
		$arUser = $rsUser->Fetch();
		$arPost["arUser"] = $arUser;
		
		if(intval($arUser['PERSONAL_PHOTO']) > 0){
			$rsAvatar = CFile::GetByID($arUser['PERSONAL_PHOTO']);
			$arAvatar = $rsAvatar->Fetch();			
			$arPost["arUser"]["avatar"] = "/upload/".$arAvatar['SUBDIR']."/".$arAvatar['FILE_NAME'];
		} else {
			$arPost["arUser"]["avatar"] = "/images/avatar/avatar_small.jpg";
		}
		
		$arPost["DATE_FORMATTED"] = explode(" ", $arPost['DATE_PUBLISH']);
		
		$arPosts[] = $arPost;
	}
	$obCache->EndDataCache(array(
		"arBlogs" => $arBlogs,
		"arPosts" => $arPosts
	));
}else{
	$arBlogs = array();
	$arPosts = array();
}
unset($arPost);

//print_r($_SERVER);
?>

<div id="content">





<link rel="stylesheet" type="text/css" href="/new-year/2014/style.css">
<script type="text/javascript" src="/new-year/2014/script.js"></script>


<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=140629199390639";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Vkontakte -->
<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?47"></script>
<script type="text/javascript">
  VK.init({apiId: 2404991, onlyWidgets: true});
</script>

<div class="b-ny">
	<h1 class="b-ny__template">Рецепты на Новый год</h1>
	<div class="i-relative">
		<div class="b-ny__social">
			<div class="b-ny__social__item"><fb:like send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>
			<div class="b-ny__social__item">
				<div id="vk_like"></div>
				<script type="text/javascript">
				VK.Widgets.Like("vk_like", {type: "mini", height: 20, pageUrl: "http://www.foodclub.ru/new-year/2014/", pageImage: "http://www.foodclub.ru/upload/iblock/a61/IMG_8698.jpg", text: "Новогоднее меню от Foodclub.ru", pageDescription: "Новогоднее меню от Foodclub.ru"});
				</script>
			</div>
			<div class="i-clearfix"></div>
		</div>
	</div>
	<menu class="b-ny__top-menu">
		<li class="b-ny__top-menu__item"><a href="" data-id="menu1">Студенческое меню</a></li>
		<li class="b-ny__top-menu__item"><a href="" data-id="menu2">Традиционное меню</a></li>
		<li class="b-ny__top-menu__item"><a href="" data-id="menu3">Изысканное меню</a></li>
		<li class="b-ny__top-menu__item"><a href="" data-id="menu4">Фуршет</a></li>
		<li class="b-ny__top-menu__item"><a href="" data-id="menu5">Вегетарианское меню</a></li>
	</menu>
	
	<div class="b-ny__block">
		<div class="b-ny__menu" id="menu1">
			<div class="i-relative b-ny__menu__heading"><h2><span>Студенческое меню</span></h2></div>
			<div class="b-ny__menu__intro">Создать настоящий полноценный новогодний стол без лишних затрат, да к тому же с минимумом усилий? Легко! Мы создали специальную подборку рецептов, с которым справятся не только студенты, но даже школьники. В основе нашего выбора — доступность ингредиентов и простота приготовления. Мы также постарались учесть повышенную потребность молодых организмов в питательных веществах.
</div>
			
			<div class="b-ny__menu__sub i-salads recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Салаты на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1024/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/0c4/_end.jpg" width="170" alt="Салат «Краш-тест китайского джипа»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1024/" target="_blank">Салат «Краш-тест китайского джипа»</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/42824/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/9d8/_end.jpg" width="170" alt="Салат с тунцом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/42824/" target="_blank">Салат с тунцом</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/14762/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/1ec/04.jpg" width="170" alt="«Пёстрый» салат из перца, сыра и кукурузы"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/14762/" target="_blank">«Пёстрый» салат из перца, сыра и кукурузы</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/4529/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/493/1.jpg" width="170" alt="Свекольно-морковный салат"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/4529/" target="_blank">Свекольно-морковный салат</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Закуски на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/2621/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/67c/end.jpg" width="170" alt="Сырные палочки из слоеного теста"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/2621/" target="_blank">Сырные палочки из слоеного теста</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6566/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/788/img_0999.jpg" width="170" alt="Канапе по-русски с селедкой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6566/" target="_blank">Канапе по-русски с селедкой</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3986/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/ab2/end.jpg" width="170" alt="Канапе на шпажках"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3986/" target="_blank">Канапе на шпажках</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1457/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/100/2.jpg" width="170" alt="Помидоры с чесноком"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1457/" target="_blank">Помидоры с чесноком</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/2627/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/5ea/1.jpg" width="170" alt="Рулетики из баклажанов"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/2627/" target="_blank">Рулетики из баклажанов</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10561/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/b98/_end.jpg" width="170" alt="Слоеные палочки с ветчиной"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10561/" target="_blank">Слоеные палочки с ветчиной</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-main-courses recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Горячие блюда</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5894/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/428/kur.jpg" width="170" alt="Куриные крылышки в медово-горчичном соусе"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5894/" target="_blank">Куриные крылышки в медово-горчичном соусе</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5042/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/746/end.jpg" width="170" alt="Куриные крылья в кисло-сладком соусе"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5042/" target="_blank">Куриные крылья в кисло-сладком соусе</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/42729/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/eb0/information_items_14418.jpg" width="170" alt="Картошка по-деревески"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/42729/" target="_blank">Картошка по-деревески</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/46269/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/fe1/_end.jpg" width="170" alt="Рыба, запеченная в беконе"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/46269/" target="_blank">Рыба, запеченная в беконе</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-desserts recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Десерты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/38510/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/8c7/_end.jpg" width="170" alt="Сливочное малиновое мороженое"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/38510/" target="_blank">Сливочное малиновое мороженое</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1315/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/4c8/_end.jpg" width="170" alt="Бананово-клубничный коктейль"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1315/" target="_blank">Бананово-клубничный коктейль</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/46912/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/63c/image.jpg" width="170" alt="Трюфели"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/46912/" target="_blank">Трюфели</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31514/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c1a/IMG_8672.JPG" width="170" alt="Конфеты «Peanut butter balls»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31514/" target="_blank">Конфеты «Peanut butter balls»</a></h5>
				</div>
			</div>
		</div>
		
		<div class="b-ny__menu" id="menu2">
			<div class="i-relative b-ny__menu__heading"><h2><span>Традиционное меню</span></h2></div>
			<div class="b-ny__menu__intro">Как ни крути, для новогоднего стола в России очень характерны сытные многокомпонентные салаты, мясные закуски и соленья. Рецепты этих блюд, как правило, передаются из уст в уста и становятся своеобразным признаком праздника, таким же, как елка или запах мандаринов. По многочисленным опросам первая ассоциация русского человека со словом «Оливье» — это Новый год. Некоторые скажут, что это слишком банально, но ведь есть люди, которые сталкиваются с необходимостью самостоятельной подготовки традиционного праздничного стола впервые. Именно для них мы сделали эту подборку рецептов, включив в нее блюда, которые помогут почувствовать нужную атмосферу.</div>
			
			<div class="b-ny__menu__sub i-salads recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Салаты на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6201/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/8b0/img_0853.jpg" width="170" alt="Салат «Мимоза»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6201/" target="_blank">Салат «Мимоза»</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5821/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/416/end.jpg" width="170" alt="Салат Оливье"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5821/" target="_blank">Салат Оливье</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15125/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/eb2/_end.jpg" width="170" alt="Винегрет"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15125/" target="_blank">Винегрет</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3117/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/b68/1.jpg" width="170" alt="Селедка под шубой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3117/" target="_blank">Селедка под шубой</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Закуски на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1457/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/100/2.jpg" width="170" alt="Помидоры с чесноком"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1457/" target="_blank">Помидоры с чесноком</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/14243/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/842/_end.jpg" width="170" alt="Рулетики из баклажан"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/14243/" target="_blank">Рулетики из баклажан</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6014/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/e27/end.jpg" width="170" alt="Маринованная капуста"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6014/" target="_blank">Маринованная капуста</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6632/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d7c/end2.jpg" width="170" alt="Паштет из куриной печени"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6632/" target="_blank">Паштет из куриной печени</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3449/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/046/1.jpg" width="170" alt="Фаршированные яйца"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3449/" target="_blank">Фаршированные яйца</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10561/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/b98/_end.jpg" width="170" alt="Слоеные палочки с ветчиной"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10561/" target="_blank">Слоеные палочки с ветчиной</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/11387/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/8e4/_end.jpg" width="170" alt="Заливная рыба"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/11387/" target="_blank">Заливная рыба</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/8695/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/cbb/_end2.jpg" width="170" alt="Холодец из говядины"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/8695/" target="_blank">Холодец из говядины</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5460/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/bf4/img_9714.jpg" width="170" alt="Малосольные огурцы (горячего засола)"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5460/" target="_blank">Малосольные огурцы (горячего засола)</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/50946/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/5ca/IMG_5265.jpg" width="170" alt="Волованы с муссом из масла, икры и петрушки"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/50946/" target="_blank">Волованы с муссом из масла, икры и петрушки</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-main-courses recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Горячие блюда</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5360/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/809/_1.jpg" width="170" alt="Запеченная свинина"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5360/" target="_blank">Запеченная свинина</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/11163/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/096/_end.jpg" width="170" alt="Рулет из свинины"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/11163/" target="_blank">Рулет из свинины</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10131/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c87/_end.jpg" width="170" alt="Цыпленок табака"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10131/" target="_blank">Цыпленок табака</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5933/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/e37/end.jpg" width="170" alt="Ростбиф (запеченная говядина)"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5933/" target="_blank">Ростбиф (запеченная говядина)</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-desserts recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Десерты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/7099/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/060/img_1337.jpg" width="170" alt="Торт «Наполеон»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/7099/" target="_blank">Торт «Наполеон»</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/7420/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/a53/ekler.jpg" width="170" alt="Эклеры со сливочным кремом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/7420/" target="_blank">Эклеры со сливочным кремом</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/51150/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/074/tortische.jpg" width="170" alt="Большой праздничный торт"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/51150/" target="_blank">Большой праздничный торт</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6427/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/aa6/end2.jpg" width="170" alt="Пирожное картошка"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6427/" target="_blank">Пирожное картошка</a></h5>
				</div>
			</div>
		</div>
		
		<div class="b-ny__menu" id="menu3">
			<div class="i-relative b-ny__menu__heading"><h2><span>Изысканное меню</span></h2></div>
			<div class="b-ny__menu__intro">Чтобы почувствовать но-настоящему праздничную атмосферу в Новый год, создайте соответствующий новогодний стол! Чудесные деликатесы украсят праздник, поднимут настроение и зададут планку на весь будущий год.</div>
			
			<div class="b-ny__menu__sub i-salads recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Салаты на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31378/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/a7a/_end.jpg" width="170" alt="Салат с грушей и голубым сыром"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31378/" target="_blank">Салат с грушей и голубым сыром</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/11025/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/013/_end1.jpg" width="170" alt="Китайский новогодний салат"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/11025/" target="_blank">Китайский новогодний салат</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/21047/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/975/x_e541027d.jpg" width="170" alt="Салат с авокадо, креветками и грейпфрутом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/21047/" target="_blank">Салат с авокадо, креветками и грейпфрутом</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10922/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/a97/_end.jpg" width="170" alt="Салат уолдорф (вальдорф)"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10922/" target="_blank">Салат уолдорф (вальдорф)</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Закуски на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/52835/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2cb/_13.jpg" width="170" alt="Гужеры с сыром"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/52835/" target="_blank">Гужеры с сыром</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/32699/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/efa/_korzinki.jpg" width="170" alt="Корзиночки из пармезана"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/32699/" target="_blank">Корзиночки из пармезана</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/13338/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2f8/_end1.jpg" width="170" alt="Креветочные тосты"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/13338/" target="_blank">Креветочные тосты</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3904/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/04c/end.jpg" width="170" alt="Жареный сыр"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3904/" target="_blank">Жареный сыр</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/18500/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/8d9/x_af99d8b8.jpg" width="170" alt="Финики с горгонзолой в ветчине"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/18500/" target="_blank">Финики с горгонзолой в ветчине</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/12289/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/53f/_end1.jpg" width="170" alt="Паштет из семги с креветками"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/12289/" target="_blank">Паштет из семги с креветками</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/38654/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/f49/_end.jpg" width="170" alt="Спринг роллы с индейкой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/38654/" target="_blank">Спринг роллы с индейкой</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-main-courses recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Горячие блюда</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/19976/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/201/_end.jpg" width="170" alt="Индейка в духовке"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/19976/" target="_blank">Индейка в духовке</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/36561/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/7ab/_end.jpg" width="170" alt="Рулет из свинины с курицей и грибами"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/36561/" target="_blank">Рулет из свинины с курицей и грибами</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10569/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/0c4/end.jpg" width="170" alt="Новогодний гусь с яблоками и тмином" style="margin-top: -90px;"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10569/" target="_blank">Новогодний гусь с яблоками и тмином</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15837/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/7b3/2DSC_0062-1.jpg" width="170" alt="Корона из бараньей корейки"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15837/" target="_blank">Корона из бараньей корейки</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15833/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/34a/smoked-duack11.jpg" width="170" alt="Копченая утка"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15833/" target="_blank">Копченая утка</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/17025/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/79f/0_4a149_83f790e1_orig.jpg" width="170" alt="Гусь с соусом из сидра"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/17025/" target="_blank">Гусь с соусом из сидра</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-desserts recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Десерты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31995/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2ed/_pavlova.jpg" width="170" alt="Десерт Павлова"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31995/" target="_blank">Десерт Павлова</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10944/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/0d6/IMG_7798.jpg" width="170" alt="Новогодний Black Bun"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10944/" target="_blank">Новогодний Black Bun</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10694/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d7e/_end1.jpg" width="170" alt="Крокембуш"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10694/" target="_blank">Крокембуш</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10484/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/27a/img_7637.jpg" width="170" alt="Новогодний торт Ёлочка"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10484/" target="_blank">Новогодний торт Ёлочка</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10212/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/157/img_0579.jpg" width="170" alt="Новогодний торт Vassilopitta"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10212/" target="_blank">Новогодний торт Vassilopitta</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/21128/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/36f/_11.jpg" width="170" alt="Пряничный домик"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/21128/" target="_blank">Пряничный домик</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6416/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/e21/IMG_8698.jpg" width="170" alt="Печенье «Рождественские венки»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6416/" target="_blank">Печенье «Рождественские венки»</a></h5>
				</div>
			</div>
		</div>
		
		<div class="b-ny__menu" id="menu4">
			<div class="i-relative b-ny__menu__heading"><h2><span>Фуршет</span></h2></div>
			<div class="b-ny__menu__intro">Если на Новые год вы не планируете сидеть за столом, а предпочитаете активное впемяпрепровождение, то это меню как раз для вас! В нем все блюда подобраны так, чтобы их можно было есть буквально на ходу и даже без приборов.</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Закуски на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/52835/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2cb/_13.jpg" width="170" alt="Гужеры с сыром"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/52835/" target="_blank">Гужеры с сыром</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/13338/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2f8/_end1.jpg" width="170" alt="Креветочные тосты"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/13338/" target="_blank">Креветочные тосты</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/18500/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/8d9/x_af99d8b8.jpg" width="170" alt="Финики с горгонзолой в ветчине"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/18500/" target="_blank">Финики с горгонзолой в ветчине</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/20573/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/b71/IMG_4458.jpg" width="170" alt="Сырное печенье"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/20573/" target="_blank">Сырное печенье</a></h5>
				</div>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/14032/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c8a/_end1.jpg" width="170" alt="Куриные крокеты"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/14032/" target="_blank">Куриные крокеты</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/8580/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d91/_end.jpg" width="170" alt="Тарталетки с песто и креветками"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/8580/" target="_blank">Тарталетки с песто и креветками</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6370/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c0c/end2.jpg" width="170" alt="Канапе из блинов на шпажках"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6370/" target="_blank">Канапе из блинов на шпажках</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3986/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/ab2/end.jpg" width="170" alt="Канапе на шпажках"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3986/" target="_blank">Канапе на шпажках</a></h5>
				</div>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/3259/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/902/end.jpg" width="170" alt="Тарталетки с начинкой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/3259/" target="_blank">Тарталетки с начинкой</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/2536/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/5d3/end.jpg" width="170" alt="Творожные кольца с куриным салатом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/2536/" target="_blank">Творожные кольца с куриным салатом</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1869/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/851/end.jpg" width="170" alt="Пирожки с грибами"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1869/" target="_blank">Пирожки с грибами</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/14195/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/040/_end.jpg" width="170" alt="Слоеные пирожки с яйцом и зеленым луком"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/14195/" target="_blank">Слоеные пирожки с яйцом и зеленым луком</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6189/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/2e5/img_0620.jpg" width="170" alt="Брускетта со сливочным пюре"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6189/" target="_blank">Брускетта со сливочным пюре</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-main-courses recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Горячие блюда</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/17168/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/885/_end.jpg" width="170" alt="Шашлычки из гребешков и креветок"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/17168/" target="_blank">Шашлычки из гребешков и креветок</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1813/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/e45/1.jpg" width="170" alt="Жареные креветки"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1813/" target="_blank">Жареные креветки</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/13308/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/035/_end1.jpg" width="170" alt="Шашлычки из индейки в имбирном маринаде"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/13308/" target="_blank">Шашлычки из индейки в имбирном маринаде</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5042/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/746/end.jpg" width="170" alt="Куриные крылья в кисло-сладком соусе"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5042/" target="_blank">Куриные крылья в кисло-сладком соусе</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/43476/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/981/_burrito.jpg" width="170" alt="Буррито с мясом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/43476/" target="_blank">Буррито с мясом</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-desserts recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Десерты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/7420/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/a53/ekler.jpg" width="170" alt="Эклеры со сливочным кремом"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/7420/" target="_blank">Эклеры со сливочным кремом</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31905/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/3d4/_pechenye.jpg" width="170" alt="Ореховое печенье без муки"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31905/" target="_blank">Ореховое печенье без муки</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6416/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/e21/IMG_8698.jpg" width="170" alt="Печенье «Рождественские венки»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6416/" target="_blank">Печенье «Рождественские венки»</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/6507/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/a05/111.jpg" width="170" alt="Имбирные пряники"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/6507/" target="_blank">Имбирные пряники</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/2464/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/249/_end.jpg" width="170" alt="Миндальное печенье"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/2464/" target="_blank">Миндальное печенье</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/18161/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/625/_end.jpg" width="170" alt="Бисквитное печенье савоярди"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/18161/" target="_blank">Бисквитное печенье савоярди</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/38886/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/503/IMG_8610.jpg" width="170" alt="Шоколадные конфеты с начинкой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/38886/" target="_blank">Шоколадные конфеты с начинкой</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31514/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c1a/IMG_8672.JPG" width="170" alt="Конфеты «Peanut butter balls»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31514/" target="_blank">Конфеты «Peanut butter balls»</a></h5>
				</div>
			</div>
		</div>
		
		<div class="b-ny__menu" id="menu5">
			<div class="i-relative b-ny__menu__heading"><h2><span>Вегетарианское меню</span></h2></div>
			<div class="b-ny__menu__intro">Для вегетарианцев, а также для тех, кто соблюдает Рождественский пост, мы создали специальное новогоднее меню, с которым никто не останется голодным, а праздничный стол будет вкусным и разнообразным.</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Новогодние салаты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/39459/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d8d/IMG_4284copy.jpg" width="170" alt="Буррито Салат"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/39459/" target="_blank">Буррито Салат</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/37632/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/15b/_end.jpg" width="170" alt="Салат табуле (tabbouleh)"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/37632/" target="_blank">Салат табуле (tabbouleh)</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/36356/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/6b0/_end.jpg" width="170" alt="Овощной салат с красной капустой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/36356/" target="_blank">Овощной салат с красной капустой</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/21579/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/fe2/_salad.jpg" width="170" alt="Салат из кабачков с кедровыми орешками"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/21579/" target="_blank">Салат из кабачков с кедровыми орешками</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15125/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/eb2/_end.jpg" width="170" alt="Винегрет"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15125/" target="_blank">Винегрет</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/12687/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/0ec/_end1.jpg" width="170" alt="Салат с авокадо по-техасски"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/12687/" target="_blank">Салат с авокадо по-техасски</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/10661/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/4db/_end1.jpg" width="170" alt="Острый салат из моркови"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/10661/" target="_blank">Острый салат из моркови</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/5548/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/88b/slad.jpg" width="170" alt="Салат с проростками фасоли маш"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/5548/" target="_blank">Салат с проростками фасоли маш</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/1523/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/11b/1.jpg" width="170" alt="Салат из рукколы"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/1523/" target="_blank">Салат из рукколы</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-starters recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Закуски на Новый год</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/42367/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/7da/_end.jpg" width="170" alt="Террин из запечённых овощей"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/42367/" target="_blank">Террин из запечённых овощей</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15952/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/25c/IMG_5092.JPG" width="170" alt="Маринованные печёные перцы"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15952/" target="_blank">Маринованные печёные перцы</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/11771/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/004/_end.jpg" width="170" alt="Хумус"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/11771/" target="_blank">Хумус</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/18988/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/1d3/509_w.jpg" width="170" alt="Ролл «Мозаика»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/18988/" target="_blank">Ролл «Мозаика»</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-main-courses recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Горячие блюда</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/11786/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/b64/_end.jpg" width="170" alt="Фалафель"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/11786/" target="_blank">Фалафель</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/9612/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c1d/_end.jpg" width="170" alt="Плацинды с картошкой"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/9612/" target="_blank">Плацинды с картошкой</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/43215/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/4d3/8633097567_c12f104f09_b.jpg" width="170" alt="Бериане"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/43215/" target="_blank">Бериане</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31454/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/1bc/_end.jpg" width="170" alt="Кокосовый рис"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31454/" target="_blank">Кокосовый рис</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31759/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/9d7/_10.jpg" width="170" alt="Баклажаны с чечевицей"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31759/" target="_blank">Баклажаны с чечевицей</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/9486/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d12/_0end1.jpg" width="170" alt="Паста с белыми грибами"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/9486/" target="_blank">Паста с белыми грибами</a></h5>
				</div>
			</div>
			
			<div class="b-ny__menu__sub i-desserts recipes_blocks">
				<div class="b-ny__menu__sub__hr"></div>
				<h3 class="b-ny__menu__sub__heading">Десерты</h3>
				
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/37869/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/18b/_end.jpg" width="170" alt="Сиеннский рождественский пирог (панфорте)"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/37869/" target="_blank">Сиеннский рождественский пирог (панфорте)</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31235/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/476/IMG_5259.jpg" width="170" alt="Миндальная халва"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31235/" target="_blank">Миндальная халва</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/43605/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/caa/d2wmycubw90%20i1w.jpg" width="170" alt="Кокосово-клубничный десерт"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/43605/" target="_blank">Кокосово-клубничный десерт</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/31514/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/c1a/IMG_8672.JPG" width="170" alt="Конфеты «Peanut butter balls»"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/31514/" target="_blank">Конфеты «Peanut butter balls»</a></h5>
				</div>
				<div class="item recipe_list_item">
					<div class="photo"><a href="http://www.foodclub.ru/detail/15646/" target="_blank"><img src="http://www.foodclub.ru/upload/iblock/d7f/923bcdaabc23.jpg" width="170" alt="Домашний грильяж"></a></div>
					<h5><a href="http://www.foodclub.ru/detail/15646/" target="_blank">Домашний грильяж</a></h5>
				</div>
			</div>
		</div>
		
	</div>
</div>









<?


		$APPLICATION->AddHeadString('<meta property="og:type" content="article"/>
		<meta property="og:title" content="Новогоднее меню от Foodclub.ru"/>
		<meta property="og:image" content="http://www.foodclub.ru/upload/iblock/a61/IMG_8698.jpg" />
		<meta property="og:url" content="http://www.foodclub.ru/new-year/2014/" />
		<meta property="og:site_name" content="foodclub.ru"/>
		<meta property="og:description" content="Новогоднее меню от Foodclub.ru - Подборка новогодних рецептов, класические и оригинальные блюда к праздничному столу.">');




?>







</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
