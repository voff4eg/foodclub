<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$c=count($arResult['ITEMS'])-1; $i=0;
if($arResult['ITEMS'][0]):
?>

{
<? foreach($arResult['ITEMS'] as $it):?>
	"<?=$it['ID'];?>": "<?=$it['NAME'];?>"<?if($i!=$c)echo ","; $i++;?>
<?endforeach;?>
}
<?else:?>{}<?endif?>