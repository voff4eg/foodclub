<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
	<select name="type" required>
		<option>Выберите тип техники</option>
		<? foreach($arResult['ITEMS'] as $it):?>
			<option value="<?=$it['ID'];?>"><?=$it['NAME'];?></option>
		<?endforeach;?>
	</select>
	