<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "рецепты печений, домашнее печенье, рецпты теста для печенья, рецепт песочного печенья");
$APPLICATION->SetPageProperty("description", "Творожное, медовое, миндальное, шоколадное, песочное печенье. Пошаговые фотографии приготовления.");
$APPLICATION->SetTitle("Рецепты печенья с фото. Foodclub");
?><style type="text/css">
div.pages_recipes div.item {
	padding-bottom:10px;}
div.pages_recipes {margin:20px 0 0 0;}
div.pages_recipes div.item div.link {display:block;}
div.pages_recipes div.photo {
	float:left;
	width:50px;
	padding:0 12px 0 0;}
div.pages_recipes div.big_photo {
	position:relative;
	display:none;}
div.pages_recipes div.big_photo div {
	position:absolute;
	top:-3px;
	left:-3px;
	z-index:4;}
div.pages_recipes h2 {
	color:#333333;
	margin:40px 0 15px;}

div.recipes_blocks {margin:30px 0 10px 0;}
div.recipes_blocks h2 {
	margin-bottom:20px;
	color:#333333;}
div.recipes_blocks p {margin:10px 0;}
div.recipes_blocks div.item {
	float:left;
	width:200px;
	display:inline;
	margin:0 30px 30px 0;}
div.recipes_blocks div.item h3 {margin:10px 0;}
div.recipes_blocks div.item p {
	font-size:10pt;
	margin:0;}
</style>
<div id="content">
			<h1>Рецепты печений с фото</h1>
			<p style="width:700px;">Для домашнего чаепития, для приёма гостей, для праздничного угощения или просто когда хочется вкусненького отлично подходят домашние печенья. Испечь их в общем-то несложно, главное точно соблюсти рецептуру, технологию приготовления теста, заранее разогреть духовку и не забудьте смазать противень сливочным маслом, чтобы печенья легко было снять.</p>
			<p style="width:700px;">Песочное тесто — самое распространённое для <strong>рецептов печенья</strong>. В него добавляют разные добавки — орешки, шоколад, пряности, цедру лимона, мёд — и получают разные по вкусу печенья. И форма у каждого вида печенья своя. Это и подговки, и звёздочки, и веночки, и розочки, и палочки.</p>
			<p style="width:700px;">Список <strong>рецептов печений</strong> на Foodclub'е постоянно увеличивается, добавьте и Вы свой рецепт! Для этого зафиксируйте на фото процесс приготовления Вашего фирменного рецепта печений и добавьте его на сайт с помощью формы <a href="/recipe/add/">добавления рецепта</a>.</p>
			
			<div class="recipes_blocks">
				<div class="item">
					<a href="http://www.foodclub.ru/detail/2941/"><img src="/upload/iblock/b34/end.jpg" width="200" height="133" alt="Творожные крекеры"></a>
					<h3><a href="http://www.foodclub.ru/detail/2941/">Творожные крекеры</a></h3>
					<p>Эти маленькие печеньица можно есть и с солью (к пиву) и с сахаром (к чаю).</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/2621/"><img src="/upload/iblock/67c/end.jpg" width="200" height="133" alt="Сырные палочки из слоеного теста"></a>
					<h3><a href="http://www.foodclub.ru/detail/2621/">Сырные палочки из слоеного теста</a></h3>
					<p>В этом рецепте и описывать-то особо нечего, но, поскольку получается очень-очень вкусно, я хочу им поделиться.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/10249/"><img src="/upload/iblock/a03/0_423fe_ad82f472_xl.jpg" width="133" height="200" alt="Печенье с тыквой"></a>
					<h3><a href="http://www.foodclub.ru/detail/10249/">Печенье с тыквой</a></h3>
					<p>Идея - Гастроном. В оригинале используется рокфор, но этот вариант будет с дор блю.</p>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="recipes_blocks">
				<h2>Рецепты овсяного печенья</h2>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/6787/"><img src="/upload/iblock/667/img_40452.jpg" width="200" height="133" alt="Овсяные талеры"></a>
					<h3><a href="http://www.foodclub.ru/detail/6787/">Овсяные талеры</a></h3>
					<p>Талеры, видимо, потому, что они круглые и тяжёлые как старинные монеты.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/8935/"><img src="/upload/iblock/e15/img_8172.jpg" width="200" height="133" alt="Шоколадные пряники с цукатами"></a>
					<h3><a href="http://www.foodclub.ru/detail/8935/">Шоколадные пряники с цукатами</a></h3>
					<p>Это очень вкусные, ароматные пряники с кусочками цитрусовых цукатов и орехов и овсяными хлопьями.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/11661/"><img src="/upload/iblock/834/0_4c311_15481939_L.jpg" width="133" height="200" alt="Овсяное печенье с изюмом"></a>
					<h3><a href="http://www.foodclub.ru/detail/11661/">Овсяное печенье с изюмом</a></h3>
					<p>Нет более простого (и этим подкупающего), всегда удающегося, домашнего печенья.</p>
				</div>
				<div class="clear"></div>
			</div>
			
			<div class="recipes_blocks">
				<h2>Рецепты песочного печенья</h2>
				<p style="width:700px;">Что важно знать для того, чтобы печенье удалось. Если тесто для печений нужно раскатывать, лучше делать это на пекарской бумаге или пергаменте, смазанном маслом. Тогда вырезанные фигурки не придётся перекладывать на противень и они не потеряют своей формы. Если необходимо отмерять одинаковые кусочки теста (чтобы скатать их в шарик, например) сделайте так. Разделите всё тесто на четыре равные части. Каждую раскатывайте в колбаску, толщиной 2-3 см, а затем разделите колбаску на равные кусочки с помощью ножа. Так вы получите одинаковые порции теста и печенье получится аккуратным. Если в вашей духовке печенье часто подгорает снизу, пеките его на кухонной решётке, выстеленной пекарской бумагой. Если печенье выпекается в несколько приёмов, убирайте оставшееся тесто в холодильник пока не придёт его очередь. Чтобы поверхность печений была ровной и блестящей, смазывайте его желтком, размешанным с парой ложек тёплой воды.</p>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/12016/"><img src="/upload/iblock/73e/_end1.jpg" width="200" height="122" alt="Песочное печенье с розмарином"></a>
					<h3><a href="http://www.foodclub.ru/detail/12016/">Песочное печенье с розмарином</a></h3>
					<p>Не слишком сладкое печенье, с розмарином и лимонной цедрой отлично подойдет не только к чаю, но и к вину и коктейлям. </p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/10405/"><img src="/upload/iblock/ce3/img_7476.jpg" width="200" height="133" alt="Шотландское печенье Shortbreads"></a>
					<h3><a href="http://www.foodclub.ru/detail/10405/">Шотландское печенье Shortbreads</a></h3>
					<p>Для этого печенья берите масло самого лучшего качества, именно оно главным образом влияет на вкус и уютный аромат изделий.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/4345/"><img src="/upload/iblock/664/dsc_2113.jpg" width="200" height="133" alt="Медовое печенье"></a>
					<h3><a href="http://www.foodclub.ru/detail/4345/">Медовое печенье</a></h3>
					<p>Рецепт этого печенья известен ещё нашим бабушкам, а немного модифицировав его, я получила более насыщенный и выраженный вкус.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/6507/"><img src="/upload/iblock/33b/111.jpg" width="200" height="133" alt="Имбирные пряники"></a>
					<h3><a href="http://www.foodclub.ru/detail/6507/">Имбирные пряники</a></h3>
					<p>Ароматные пряники с традиционным украшением глазурью помогут создать праздничную атмосферу.</p>
				</div>
				<div class="clear"></div>
				
				<div class="item">
					<a href="http://www.foodclub.ru/detail/7261/"><img src="/upload/iblock/839/_end.jpg" width="200" height="133" alt="Шоколадные бискотти с фундуком"></a>
					<h3><a href="http://www.foodclub.ru/detail/7261/">Шоколадные бискотти с фундуком</a></h3>
					<p>Эти бискотти с фундуком и шоколадной крошкой очень просто готовить и они очень хорошо подходят к чаю и кофе.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/6416/"><img src="/upload/iblock/43e/1.jpg" width="200" height="133" alt="Печенье «Рождественские венки»"></a>
					<h3><a href="http://www.foodclub.ru/detail/6416/">Печенье «Рождественские венки»</a></h3>
					<p>Эффектное печенье из песочного шоколадного теста.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/5581/"><img src="/upload/iblock/557/end1.jpg" width="200" height="133" alt="Миндальное печенье"></a>
					<h3><a href="http://www.foodclub.ru/detail/5581/">Миндальное печенье</a></h3>
					<p>Этого миндального печенья получается довольно много и с ним очень вкусно пить чай.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/3491/"><img src="/upload/iblock/970/end.jpg" width="200" height="133" alt="Миндальное печенье с рисовой мукой"></a>
					<h3><a href="http://www.foodclub.ru/detail/3491/">Миндальное печенье с рисовой мукой</a></h3>
					<p>Миндальное печенье с рисовой мукой получается хрустящим и рассыпчатым.</p>
				</div>
				<div class="clear"></div>
				
				<div class="item">
					<a href="http://www.foodclub.ru/detail/1554/"><img src="/upload/iblock/9a4/end.jpg" width="200" height="140" alt="Творожное печенье"></a>
					<h3><a href="http://www.foodclub.ru/detail/1554/">Творожное печенье</a></h3>
					<p>Просто удивительно, сколько всего можно приготовить из творога.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/1537/"><img src="/upload/iblock/1a2/4.jpg" width="200" height="133" alt="Яблочные звездочки"></a>
					<h3><a href="http://www.foodclub.ru/detail/1537/">Яблочные звездочки</a></h3>
					<p>Идея этого печенья — маленький размер, только на один укус :))</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/6578/"><img src="/upload/iblock/e0f/end.jpg" width="200" height="133" alt="Пряники на елку"></a>
					<h3><a href="http://www.foodclub.ru/detail/id/">Пряники на елку</a></h3>
					<p>Очень вкусные и ароматные пряники получаются по этому рецепту — одному из разновидностей пряничного теста.</p>
				</div>
				<div class="item">
					<a href="http://www.foodclub.ru/detail/2464/"><img src="/upload/iblock/09c/end1.jpg" width="200" height="133" alt="Миндальное печенье"></a>
					<h3><a href="http://www.foodclub.ru/detail/2464/">Миндальное печенье</a></h3>
					<p>На его приготовление уходит совсем немного времени, а хранится оно довольно долго.</p>
				</div>
				<div class="clear"></div>
			</div>
	</div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>