<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'): ?>

<div class="b-lavka" data-ajax-url="index.php" data-ajax-type="GET">

	<div class="b-lavka__filter b-filter" data-floating="true" data-col="3">
		<div class="b-filter__item" data-array="filterStoresArray">
			<script>
				var filterStoresArray = [ {id:0, name:"Все"},
				<?
					foreach($arResult["bannercontracts"] as $cList)
					{
						if(!empty($arResult["banners"][ $cList["id"] ]))
						echo "{id:".$cList['id'].",name:'".$cList['name']."'},";
						//echo json_encode($arResult["contractshead"]);
					}

				?>
				];
			</script>
			<h5 class="b-filter__item__heading">Фильтр по магазинам</h5>
			<a href="#" class="frame_bg b-filter__item__button">
				<span class="left">
					<span class="right">
						<span class="bg"><span>Все</span></span>
					</span>
				</span>
			</a>
		</div>
		<div class="i-clearfix"></div>
		<div class="b-filter__lists">
			<div class="b-filter__list">
			</div>
		</div>
	</div>

	<div class="b-lavka__blocks">
	<?endif?>
	<?foreach ( $arResult["bannercontracts"] as $cList ):?>
		<?if(!empty($arResult["banners"][ $cList["id"] ])): ?>
		<div class="b-collection-block b-store-block">
		
			<div class="b-collection-block__heading">
				<span class="b-collection-block__heading__content"><?=$cList['name'];?></span>
			</div>
			
			<div class="b-store-block__content">
			<? $countdeltabanners=0; $count1=0; $countbannersincontract=count($arResult["banners"][ $cList["id"] ]); ?>
			<?foreach ( $arResult["banners"][ $cList["id"] ] as $bList ):?>
			<?//echo "<pre>";print_r($bList);echo "</pre>";?>
				<?if(strlen($bList["URL"])):?><a href="<?=$bList["URL"]?>"><?endif;?>
				<?if($bList['CODE'])echo $bList['CODE'];?>
				<?if(strlen($bList["URL"])):?></a><?endif;?>
				<? if($countdeltabanners==2 && $count1!==$countbannersincontract-1){echo '<div class="b-store-block__hr"></div>'; $countdeltabanners=0;}else {$countdeltabanners++;} $count1++; ?>
			<?endforeach; ?>
			<? if($countdeltabanners==2) echo '<div class="b-store-block__item"></div>'; ?>
			<? if($countdeltabanners==1) echo '<div class="b-store-block__item"></div><div class="b-store-block__item"></div>'; ?>

			
			</div>
		</div>
		<?endif?>
	<?endforeach; ?>
		<!--конец блока контракта-->
	
	
	
	</div>
	
	
		

<? if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'): ?>	
</div>
<?endif?>



	