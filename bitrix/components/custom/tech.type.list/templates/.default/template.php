<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
				<select name="type" required>
					<option>Выберите тип техники</option>
					<? foreach($arResult as $it):?>
						<option value="<?=$arResult['ID'];?>"><?=$arResult['NAME'];?></option>
					<?endforeach;?>
					<option value="2">Мясорубка</option>
					<option value="3">Блендер</option>
					<option value="4">Миксер</option>
					<option value="5">Тостер</option>
					<option value="6">Гриль</option>
					<option value="7">Мельница</option>
					<option value="8">Соковыжималка</option>
					<option value="9">Кухонный комбайн</option>
					<option value="10">Микроволновка</option>
				</select>

