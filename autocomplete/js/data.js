var recipes = [{id:'10736',name:'Фаршированная свинина'},{id:'10694',name:'Крокембуш'},{id:'10678',name:'Тарт татен со сливами'},{id:'10661',name:'Острый салат из моркови'},{id:'10654',name:'Медово-кунжутное печенье'},{id:'10620',name:'Омлет в духовке'},{id:'10637',name:'Шейка свиная, запеченная с грибами и айвой'},{id:'10627',name:'Картофельная запеканка.'},{id:'10569',name:'Новогодний гусь с яблоками и тмином'},{id:'10543',name:'Тако со свининой'},{id:'10561',name:'Слоеные палочки с ветчиной'},{id:'10548',name:'Домашняя паста с томатным соусом и базиликом'},{id:'10530',name:'Ризотто с тыквой'},{id:'10510',name:'Техасский новогодний салат с авокадо'},{id:'10484',name:'Новогодний торт Ёлочка'},{id:'10452',name:'Пряный суп-пюре из нута'},{id:'10433',name:'Салат из свеклы и лука'},{id:'10430',name:'Суп с шиитаке'},{id:'10412',name:'Чизкейк с белым шоколадом'},{id:'10405',name:'Шотландское печенье Shortbreads'},{id:'10373',name:'Семга со сливочным соусом'},{id:'10345',name:'Сырные палочки к супу'},{id:'10334',name:'Whoopie Pie'},{id:'10316',name:'Луковый суп'},{id:'10295',name:'Молочный коктейль с мороженым'},{id:'10276',name:'Рыбная солянка с семгой'},{id:'10255',name:'Карамельный кофе в турке'},{id:'10249',name:'Печенье с тыквой'},{id:'10234',name:'Гречневые блины'},{id:'10230',name:'Рулет из омлета с ветчиной и рукколой'},{id:'10218',name:'Варенье из айвы с корицей'},{id:'10212',name:'Новогодний торт Vassilopitta'},{id:'10192',name:'Гуакамоле'},{id:'10182',name:'Пшенная каша с тыквой'},{id:'10167',name:'Щи из капусты'},{id:'10155',name:'Гуляш из свинины'},{id:'10138',name:'Паста с томатным соусом и розмарином'},{id:'10131',name:'Цыпленок табака'},{id:'10113',name:'Баклажаны в йогурте'},{id:'10085',name:'Сырники с манкой'},{id:'10077',name:'Клубничное мороженое'},{id:'10064',name:'Ризотто с грибами'},{id:'10042',name:'Тушеные баклажаны'},{id:'10036',name:'Пирожки с яблоками'},{id:'10005',name:'Бастурма'},{id:'10028',name:'Суп с вермишелью'},{id:'9981',name:'Глинтвейн'},{id:'9955',name:'Японский омлет'},{id:'9931',name:'Шведский картофельный салат'},{id:'9919',name:'Ризотто с креветками'},{id:'9893',name:'Сырные булочки'},{id:'9886',name:'Икра баклажанная (Melitzanes Salata)'},{id:'9879',name:'Запеканка картофельная с фаршем'},{id:'9859',name:'Джем из груш и персиков'},{id:'9849',name:'Маринованная свекла'},{id:'9836',name:'Тушеные кабачки'},{id:'9825',name:'Маринованные грузди'},{id:'9799',name:'Салат из помидоров'},{id:'9788',name:'Блинчики с мясом'},{id:'9780',name:'Картофельные крокеты с 2 соусами'},{id:'9766',name:'Фриттата с кабачками'},{id:'9748',name:'Курица с шалфеем в апельсиновом соусе'},{id:'9677',name:'Лимонный кекс'},{id:'9670',name:'Варенье из слив'},{id:'9663',name:'Опята с картошкой в сметане'},{id:'9639',name:'Омлет с опятами'},{id:'9612',name:'Плацинды с картошкой'},{id:'9597',name:'Баранья нога, запеченная в духовке'},{id:'9581',name:'Гамбургер с белыми грибами'},{id:'9565',name:'Овсяный кекс со сливами'},{id:'9539',name:'Свинина, фаршированная курагой и луком'},{id:'9524',name:'Овощной бульон'},{id:'9514',name:'Кондитерская колбаска'},{id:'9486',name:'Паста с белыми грибами'},{id:'9479',name:'Мармелад'},{id:'9466',name:'Кекс столичный'},{id:'9456',name:'Салат с инжиром'},{id:'9447',name:'Оссобуко'},{id:'9435',name:'Миш-маш (блюдо из поджаренных яиц, брынзы, свежего перца и специй)'},{id:'9421',name:'Шопский салат'},{id:'9412',name:'Гороховый суп'},{id:'9400',name:'Запеченная дорада'},{id:'9393',name:'Рыба в имбирном маринаде'},{id:'9387',name:'Тортилья с мясом'},{id:'9357',name:'Венский яблочный штрудель'},{id:'9377',name:'Фасолевый суп-крем'},{id:'9336',name:'Шакшука'},{id:'9310',name:'Паста в восточном стиле'},{id:'9301',name:'Тушеное мясо'},{id:'9277',name:'Свекольник на кефире'},{id:'9265',name:'Фаршированный перец'},{id:'9249',name:'Блины на кефире'},{id:'9222',name:'Соус бешамель'},{id:'9213',name:'Зеленый гаспачо'},{id:'9201',name:'Морские гребешки'},{id:'9192',name:'Варенье из черной смородины'},{id:'9180',name:'Гаспачо'},{id:'9162',name:'Плацинды с творогом'},{id:'9145',name:'Окрошка на тане'},{id:'9110',name:'Холодные пирожные из вишни в тарталетках'},{id:'9118',name:'Свекольник холодный'},{id:'9084',name:'Лимонно-имбирный лимонад'},{id:'9078',name:'Котлеты куриные'},{id:'9057',name:'Спагетти карбонара с пармской ветчиной'},{id:'9038',name:'Пирог с разноцветным перцем'},{id:'9036',name:'Салат с креветками'},{id:'9019',name:'Таратор'},{id:'9010',name:'Голубцы'},{id:'8964',name:'Испанская тортилья'},{id:'8946',name:'Гуляш из говядины'},{id:'8935',name:'Шоколадные пряники с цукатами'},{id:'8816',name:'Клубничный коктейль'},{id:'8798',name:'Буррито'},{id:'8772',name:'Зеленый сливочный соус'},{id:'8787',name:'Стеклянная лапша с креветками'},{id:'8747',name:'Картофельные клецки со шпинатом'},{id:'8738',name:'Шоколадно-апельсиновый рулет'},{id:'8721',name:'Сибас по-мароккански'},{id:'8716',name:'Морковная запеканка с черносливом. Zepter'},{id:'8695',name:'Холодец из говядины'},{id:'8677',name:'Лапша из цуккини'},{id:'8655',name:'Цыпленок по-мексикански'},{id:'8646',name:'Овощная лазанья'},{id:'8627',name:'Запеканка из цветной капусты. Zepter'},{id:'8598',name:'Бисквитный рулет с клубникой'},{id:'8580',name:'Тарталетки с песто и креветками'},{id:'8578',name:'Фокачча с сыром и базиликом'},{id:'8556',name:'Кесадилья с мясом'},{id:'8567',name:'Горячий банановый коктейль с шоколадом'},{id:'8544',name:'Фаршированная курица'},{id:'8535',name:'Спагетти по-итальянски'},{id:'8522',name:'Рулетики из индейки'},{id:'8509',name:'Каннеллони с мясным фаршем'},{id:'8478',name:'Рибай стейк'},{id:'8395',name:'Стейк портерхаус'},{id:'8458',name:'Миндальные бискотти'},{id:'8440',name:'Неаполитанская пицца. Zepter'},{id:'8426',name:'Котлеты из трески со сливками'},{id:'8416',name:'Быстрый домашний майонез'},{id:'8403',name:'Морковный суп-пюре'},{id:'8393',name:'Фруктовый коктейль'},{id:'8362',name:'Паэлья с морепродуктами'},{id:'8325',name:'Домашний хлеб с орехами, базиликом и козьим сыром'},{id:'8315',name:'Овощная пицца'},{id:'8303',name:'Киш с шампиньонами и салом'},{id:'8284',name:'Тесто для пиццы'},{id:'8269',name:'Рыба, запеченная в сливках'},{id:'8234',name:'Пицца с колбасой'},{id:'8214',name:'Жареная треска с овощами'},{id:'8206',name:'Ризотто с морепродуктами'},{id:'8181',name:'Рыбный суп с креветками'},{id:'8179',name:'Дорада в имбирно-мятном маринаде'},{id:'8140',name:'Котлеты говяжьи'},{id:'8127',name:'Банановый хлеб'},{id:'8118',name:'Паста с индейкой и грибами'},{id:'8089',name:'Брауни с фисташками'},{id:'8077',name:'Сибас, запеченный с овощами'},{id:'8058',name:'Вишневое парфе'},{id:'8042',name:'Пицца с беконом'},{id:'8028',name:'Брусничный соус к чему угодно'},{id:'8011',name:'Пасха заварная (превкуснейшая)'},{id:'8006',name:'Суфле из брокколи'},{id:'7993',name:'Пирог со сгущёнкой'},{id:'7973',name:'Томатный соус с эстрагоном'},{id:'7975',name:'Пицца с грибами и козьим сыром'},{id:'7963',name:'Манная каша'},{id:'7943',name:'Плов с тыквой и фруктами. Zepter'},{id:'7930',name:'Сладкий мятный песто'},{id:'7918',name:'Фаршированные кабачки'},{id:'7898',name:'Суп-пюре из горошка. Zepter'},{id:'7889',name:'Блины с зеленым луком'},{id:'7877',name:'Творожные маффины с вишней'},{id:'7848',name:'Клюквенный мусс с манкой'},{id:'7820',name:'Закрытая пицца кальцоне с овощами'},{id:'7738',name:'Ароматное абрикосовое пирожное'},{id:'7772',name:'Апельсиновый рулет'},{id:'7779',name:'Японский чизкейк'},{id:'7752',name:'Творожные маффины с вялеными помидорами'},{id:'7730',name:'Суп-пюре из цветной капусты'},{id:'7722',name:'Морковь с грибами. Zepter'},{id:'7711',name:'Круглые эклеры с заварным кремом'},{id:'7649',name:'Миндальный кулич'},{id:'7638',name:'Джем из лимонов'},{id:'7588',name:'Домашний салат'},{id:'7624',name:'Салат из фасоли'},{id:'7568',name:'Томатный суп с тыквой'},{id:'7580',name:'Гречка с грибами и семечками. Zepter'},{id:'7556',name:'Клюквенный мусс'},{id:'7549',name:'Паста с индейкой в сливочном соусе'},{id:'7540',name:'Десерт «Пьяная ягода». Zepter'},{id:'7530',name:'Коричневый рис с индейкой и овощами'},{id:'7519',name:'Суфле из печени'},{id:'7507',name:'Печеный картофель в Zepter'},{id:'7474',name:'Желудки индейки'},{id:'7455',name:'Свекольный пирог'},{id:'7440',name:'Говядина в горшочках'},{id:'7446',name:'Филе, обжаренное без масла. Zepter'},{id:'7420',name:'Эклеры со сливочным кремом'},{id:'7384',name:'Апри кот'},{id:'7377',name:'Паста с морепродуктами'},{id:'7362',name:'Яблоки, запечённые с геранью'},{id:'7348',name:'Творожное суфле с зеленой фасолью'},{id:'7336',name:'Ризотто с курицей'},{id:'7315',name:'Яблоки, запечённые в зелёном чае'},{id:'7300',name:'Ленивые вареники'},{id:'7261',name:'Шоколадные бискотти с фундуком'},{id:'7251',name:'Яблочный соус'},{id:'7242',name:'Блины на кефире'},{id:'7201',name:'Ореховый пирог'},{id:'7171',name:'Запеченый перец с мясом'},{id:'7168',name:'Сочни с творогом'},{id:'7151',name:'Суп-пюре из зеленого горошка'},{id:'7145',name:'Салат из курицы с ананасами'},{id:'7099',name:'Торт Наполеон из слоеного теста'},{id:'7121',name:'Фруктовый рождественский кекс'},{id:'7119',name:'Чизкейк Нью-Йорк с фисташками'},{id:'7067',name:'Тушеная капуста с копченостями'},{id:'7095',name:'Блинчики с творогом'},{id:'7064',name:'Апельсиновые конфеты'},{id:'7049',name:'Борщ в кастрюле Zepter'},{id:'7038',name:'Суп из говядины с тмином'},{id:'7028',name:'Маффины с сыром'},{id:'7015',name:'Запеченная баранина с рисом'},{id:'7007',name:'Запеченный паштет из куриной печени'},{id:'6998',name:'Индейка с ананасами'},{id:'6989',name:'Коктейль «Ночной бриз»'},{id:'6980',name:'Суп-пюре из брокколи и зеленой фасоли'},{id:'6964',name:'Мандариновый кекс'},{id:'6936',name:'Чебуреки'},{id:'6941',name:'Дорада с розмарином'},{id:'6915',name:'Картофельная запеканка с грибами'},{id:'6908',name:'Овощной суп-пюре с тефтельками'},{id:'6892',name:'Картофельная запеканка с мясом'},{id:'6869',name:'Рулеты с творожной начинкой'},{id:'6880',name:'Шоколадный торт с ириской'},{id:'6865',name:'Постные фаршированные шампиньоны'},{id:'6850',name:'Сырные булочки из слоеного теста'},{id:'6808',name:'Морковный пирог'},{id:'6791',name:'Рулеты с рыбой'},{id:'6787',name:'Овсяные талеры'},{id:'6738',name:'Макароны по-флотски (макароны с мясом)'},{id:'6751',name:'Чесночный майонез'},{id:'6718',name:'Тарталетки из слоеного теста'},{id:'6714',name:'Рулеты с печеночным паштетом'},{id:'6732',name:'Суп-пюре из шампиньонов'},{id:'6705',name:'Салат с креветками'},{id:'6680',name:'Горчичный майонез'},{id:'6632',name:'Паштет из куриной печени'},{id:'6590',name:'Суп из куриных сердечек'},{id:'6606',name:'Банановые кексы с орехами'},{id:'6578',name:'Пряники на елку'},{id:'6566',name:'Канапе по-русски с селедкой'},{id:'6528',name:'Французский луковый пирог «Писсаладьер»'},{id:'6548',name:'Запеченная курица с тимьяном'},{id:'6517',name:'Клюквенный морс с медом'},{id:'6507',name:'Имбирные пряники'},{id:'6488',name:'Медальоны из перцев и помидоров'},{id:'6490',name:'Салат из капусты'},{id:'6483',name:'Ягодный компот с медом'},{id:'6459',name:'Датский рыбный сэндвич'},{id:'6472',name:'Торт из безе с шоколадным кремом'},{id:'6447',name:'Ребра, запеченные с овощами'},{id:'6416',name:'Печенье «Рождественские венки»'},{id:'6427',name:'Пирожное картошка'},{id:'6407',name:'Рыбные котлеты'},{id:'6345',name:'Суфле с креветками'},{id:'6392',name:'Перец фаршированный мясом и рисом'},{id:'6358',name:'Кокосово-клубничное пирожное'},{id:'6370',name:'Канапе из блинов на шпажках'},{id:'6333',name:'Морковный напиток'},{id:'6328',name:'Закуска из сельди «Почти Форшмак»'},{id:'6314',name:'Рассольник с перловкой'},{id:'6305',name:'Овсяный хлеб'},{id:'6292',name:'Тушеная говядина'},{id:'6282',name:'Пирог с капустой'},{id:'6270',name:'Лимонный торт'},{id:'6252',name:'Швейцарская (итальянская) меренга'},{id:'6232',name:'Салат с кукурузой и кунжутом'},{id:'6240',name:'Омлет'},{id:'6201',name:'Салат «Мимоза»'},{id:'6225',name:'Пряничный домик'},{id:'6189',name:'Брускетта со сливочным пюре'},{id:'6198',name:'Сырники'},{id:'6184',name:'Фаршированные шампиньоны'},{id:'6174',name:'Мясная пицца'},{id:'6167',name:'Суп-пюре из свеклы'},{id:'6155',name:'Пряное мясо с фасолью'},{id:'6133',name:'Зразы из говядины по-варшавски'},{id:'6146',name:'Карп по-старорусски'},{id:'6123',name:'Картошка с розмарином'},{id:'6104',name:'Безе (меренги) с орехами'},{id:'6096',name:'Ватрушки'},{id:'6086',name:'Отварной язык'},{id:'6081',name:'Кукурузно-творожная запеканка'},{id:'6074',name:'Мясо с баклажанами'},{id:'6062',name:'Курица с грибами в сливочном соусе'},{id:'6054',name:'Пельмени сибирские'},{id:'6036',name:'Булочки с изюмом (сконы)'},{id:'6020',name:'Печень по-японски'},{id:'6014',name:'Маринованная капуста'},{id:'5999',name:'Пшенично-ржаной хлеб'},{id:'5990',name:'Суп из домашних пельменей'},{id:'5968',name:'Хачапури по-тбилисски'},{id:'5979',name:'Ароматные овощи'},{id:'5942',name:'Печеные яблоки'},{id:'5933',name:'Ростбиф (запеченная говядина)'},{id:'5915',name:'Пельмени дальневосточные'},{id:'5926',name:'Кукурузный пирог с яблоками'},{id:'5894',name:'Куриные крылышки в медово-горчичном соусе'},{id:'5884',name:'Пицца с креветками'},{id:'5866',name:'Варенье из фейхоа'},{id:'5850',name:'Пирожное «Картошка» с имбирём'},{id:'5859',name:'Тушеная говядина в томатном соусе'},{id:'5834',name:'Слоеные пирожки с мясом'},{id:'5821',name:'Салат Оливье (советский вариант)'},{id:'5804',name:'Запеченные баклажаны с сыром'},{id:'5700',name:'Пасхальный кулич'},{id:'5771',name:'Мини-кексы с белым шоколадом и орешками'},{id:'5766',name:'Оладьи с яблоками'},{id:'5764',name:'Пудинг из кабачков'},{id:'5750',name:'Ризотто с овощами'},{id:'5738',name:'Творожно-рисовая запеканка'},{id:'5719',name:'Мясо по-итальянски в томатном соусе'},{id:'5705',name:'Гарнир из стручковой фасоли'},{id:'5685',name:'Томатный соус для пиццы'},{id:'5677',name:'Котлеты по-домашнему'},{id:'5671',name:'Шоколадно-ягодный милк-шейк'},{id:'5664',name:'Блинчики с икрой'},{id:'5654',name:'Запеченые караси с луком'},{id:'5630',name:'Том ям гунг (с креветками)'},{id:'5626',name:'Томатный суп-пюре'},{id:'5616',name:'Кукурузный пирог со сливами'},{id:'5605',name:'Жареная картошка'},{id:'5597',name:'Экспресс-пицца'},{id:'5581',name:'Миндальное печенье'},{id:'5563',name:'Просто вкусная запеченная курица'},{id:'5559',name:'Блины скороспелые'},{id:'5539',name:'Мясо по-французски'},{id:'5548',name:'Салат с проростками фасоли маш'},{id:'5525',name:'Пирог с персиками'},{id:'5511',name:'Малосольные огурцы (холодного засола)'},{id:'5488',name:'Чахохбили из кур'},{id:'5460',name:'Малосольные огурцы (горячего засола)'},{id:'5453',name:'Телятина в сливочно-грибном соусе'},{id:'5432',name:'Зеленый салат с козьим сыром'},{id:'5427',name:'Блинчики со сливами'},{id:'5392',name:'Лечо'},{id:'5386',name:'Отбивные из свинины'},{id:'5373',name:'Пирожки с капустой'},{id:'5360',name:'Запеченная свинина'},{id:'5351',name:'Чили из черной фасоли и баклажан'},{id:'5340',name:'Ароматное желе из персиков'},{id:'5327',name:'Компот из слив'},{id:'5314',name:'Брусничное варенье'},{id:'5299',name:'Кабачковая икра'},{id:'5288',name:'Лосось запеченный под соусом из трав'},{id:'5281',name:'Гаспачо'},{id:'5271',name:'Рисовая лапша с мясом и креветками'},{id:'5256',name:'Жареная рыба с миндальной корочкой'},{id:'5238',name:'Жареные караси'},{id:'5226',name:'Сырный суп с кольраби'},{id:'5207',name:'Утка по-деревенски с квашеной капустой'},{id:'5317',name:'Обжаренная курица с базиликом и чили'},{id:'5146',name:'Апельсиновое желе с шоколадным муссом'},{id:'5128',name:'Миндальный пирог с клубникой'},{id:'5111',name:'Курица в горшочках с овощами'},{id:'5087',name:'Холодный чай с мятой и яблоком'},{id:'5075',name:'Холодец из курицы'},{id:'5063',name:'Фаршированные баклажаны'},{id:'5042',name:'Куриные крылья в кисло-сладком соусе'},{id:'5034',name:'Овощное рагу'},{id:'5023',name:'Дорада, фаршированная овощами'},{id:'5000',name:'Гороховая каша'},{id:'4989',name:'Окрошка с квасом'},{id:'4976',name:'Грибной суп'},{id:'4956',name:'Клубничный блинный торт'},{id:'4940',name:'Блины шоколадные'},{id:'4926',name:'Домашний майонез'},{id:'4918',name:'Шоколадные кексы с жидкой начинкой'},{id:'4910',name:'Свинина в кисло-сладком соусе'},{id:'4898',name:'Марципановые конфеты'},{id:'4883',name:'Пицца с фаршем «по-домашнему»'},{id:'4860',name:'Сырно-кукурузный суп'},{id:'4853',name:'Запеченные куриные бедра с розмарином'},{id:'4835',name:'Свинина с кабачками'},{id:'4811',name:'Дорада фаршированная сладким перцем'},{id:'4803',name:'Крем-брюле'},{id:'4785',name:'Рыба в томатном соусе'},{id:'4775',name:'Картофельные котлеты'},{id:'4768',name:'Грибной соус'},{id:'4730',name:'Крем-суп из курицы'},{id:'4735',name:'Салат с перловкой'},{id:'4715',name:'Плов праздничный «Туй оши»'},{id:'4704',name:'Вишневый пирог из творожного теста'},{id:'4692',name:'Копченые куриные грудки'},{id:'4678',name:'Откидной плов с курицей'},{id:'4657',name:'Неаполитанская пастьера'},{id:'4648',name:'Каша в тыкве'},{id:'4638',name:'Салат из баклажанов'},{id:'4628',name:'Ягодный кисель'},{id:'4619',name:'Огуречный салат с медом'},{id:'4615',name:'Бефстроганов'},{id:'4595',name:'Тайский салат из яиц'},{id:'4580',name:'Русский борщ'},{id:'4561',name:'Умбрийский цыпленок'},{id:'4538',name:'Тефтели в томатном соусе с мятой'},{id:'4529',name:'Свекольно-морковный салат'},{id:'4518',name:'Ягодный пирог'},{id:'4509',name:'Куриные котлеты'},{id:'4497',name:'Салат «Утро на болоте»'},{id:'4492',name:'Парфэ с грецкими орехами и медом'},{id:'4482',name:'Морковные котлеты'},{id:'4476',name:'Паста с курицей и сливочным соусом'},{id:'4455',name:'Сметанный соус'},{id:'4450',name:'Завиванцы (булочки с корицей)'},{id:'4435',name:'Запеченые каннеллони по-итальянски'},{id:'4415',name:'Гречневый торт'},{id:'4404',name:'Запеченные помидоры'},{id:'4369',name:'Банановые блинчики с клубничным соусом'},{id:'4362',name:'Постная паста в томатном соусе'},{id:'4353',name:'Тыквенно-картофельная запеканка'},{id:'4345',name:'Медовое печенье'},{id:'4335',name:'Салат из авокадо и рукколы'},{id:'4328',name:'Шоколадные оладьи'},{id:'4323',name:'Яблочный штрудель'},{id:'4311',name:'Салат с мидиями'},{id:'4302',name:'Окрошка на минеральной воде'},{id:'4293',name:'Котлеты из баранины'},{id:'4283',name:'Постные вареники с картошкой'},{id:'4269',name:'Капуста маринованная со свёклой'},{id:'4259',name:'Ореховый пирог с абрикосами'},{id:'4250',name:'Греческий салат'},{id:'4242',name:'Постные вареники с капустой'},{id:'4225',name:'Блинчики «Креп Сюзетт»'},{id:'4209',name:'Ореховый пирог'},{id:'4202',name:'Шурпа'},{id:'4193',name:'Печеночные оладьи'},{id:'4177',name:'Сырный хлеб'},{id:'4166',name:'Корзинки с пшенной кашей'},{id:'4148',name:'Салат из сельди с сельдереем'},{id:'4136',name:'Томатный суп с лапшой'},{id:'4128',name:'Морковная творожная запеканка'},{id:'4118',name:'Десерт из груш с мороженым'},{id:'4108',name:'Творожная запеканка с кукурузной мукой'},{id:'4101',name:'Салат из креветок и авокадо с мятной заправкой'},{id:'4091',name:'Суп из куриной печени'},{id:'4084',name:'Телячья печенка по-берлински'},{id:'4075',name:'Тыквенный хлеб'},{id:'4065',name:'Творожная запеканка с бананами'},{id:'4059',name:'Куриные грудки в сливочном соусе'},{id:'4054',name:'Будайский творожный пирог'},{id:'4044',name:'Драники или картофельные оладьи'},{id:'4030',name:'Миндально-ягодный десерт'},{id:'4019',name:'Дрожжевые блины'},{id:'4011',name:'Котлеты из телятины'},{id:'4000',name:'Тыквенные оладьи'},{id:'3986',name:'Канапе на шпажках'},{id:'3982',name:'Блинчатый пирог с лососем'},{id:'3910',name:'Зеленые оладьи из шпината и брокколи'},{id:'3904',name:'Жареный сыр'},{id:'3881',name:'Рисово-кукурузные оладьи с творогом'},{id:'3805',name:'Гороховый суп с копченостями и гренками'},{id:'3778',name:'Желе из абрикосов'},{id:'3771',name:'Кабачковые оладьи'},{id:'3743',name:'Суп из шпината'},{id:'3751',name:'Фаршированный картофель'},{id:'3729',name:'Рисовые блинчики с грушевым соусом'},{id:'3735',name:'Шарлотка с яблоками'},{id:'3662',name:'Блинный пирог с творожной начинкой'},{id:'3632',name:'Паста с семгой и креветками'},{id:'3649',name:'Овощной коктейль'},{id:'3594',name:'Салат «Ташкент»'},{id:'3588',name:'Жареный рис со свининой'},{id:'3571',name:'Луковый пирог'},{id:'3575',name:'Салат из курицы с ананасом'},{id:'3563',name:'Франкфуртский овощной суп'},{id:'3544',name:'Цыпленок в апельсиново-сметанном соусе'},{id:'3500',name:'Фаршированный лук'},{id:'3507',name:'Суп из щавеля'},{id:'3495',name:'Острый томатный суп с фасолью'},{id:'3491',name:'Миндальное печенье с рисовой мукой'},{id:'3474',name:'Капуста, запеченная под молочным соусом'},{id:'3470',name:'Спагетти с песто'},{id:'3439',name:'Рубленый бифштекс по-гольштейнски'},{id:'3449',name:'Фаршированные яйца'},{id:'3426',name:'Гречневая каша с грибами'},{id:'3391',name:'Цветной пирог с овощами'},{id:'3375',name:'Куриная печень в сливочном соусе'},{id:'3359',name:'Пирог с рыбой'},{id:'3342',name:'Тушеная капуста с мясом'},{id:'3337',name:'Каннеллони с морковной начинкой'},{id:'3319',name:'Сдобный творожный пирог'},{id:'3328',name:'Шоколадные кексы'},{id:'3306',name:'Паста а-ля болоньез'},{id:'3284',name:'Творожная запеканка'},{id:'3279',name:'Зеленое ризотто с грибами'},{id:'3224',name:'Яблочный пирог из слоеного теста'},{id:'3228',name:'Тарталетки с креветками под сырным соусом'},{id:'3259',name:'Тарталетки с начинкой'},{id:'3148',name:'Куриный салат с авокадо и сладким перцем'},{id:'3169',name:'Фаршированная тыква'},{id:'3161',name:'Салат из чечевицы с копченой рыбой'},{id:'3088',name:'Рулеты из индейки'},{id:'3117',name:'Селедка под шубой'},{id:'3104',name:'Салат из сладкого перца с авокадо'},{id:'3083',name:'Творожный пирог'},{id:'3092',name:'Шашлычок из рыбы'},{id:'3025',name:'Клубника с творожным кремом'},{id:'3067',name:'Цукаты из арбузных корок'},{id:'3075',name:'Фаршированные помидоры'},{id:'3059',name:'Цукаты из тыквы'},{id:'3015',name:'Куриные наггетсы'},{id:'3021',name:'Творожная запеканка с овсяными хлопьями'},{id:'2976',name:'Паста с овощами в сливочном соусе'},{id:'2994',name:'Котлеты из индейки с кабачками'},{id:'3000',name:'Постный рис'},{id:'2982',name:'Мусс из куриной печени'},{id:'2965',name:'Ванильные сырники'},{id:'2951',name:'Слоеные пирожки с курицей и сыром'},{id:'2941',name:'Творожные крекеры'},{id:'2910',name:'Пряничный домик'},{id:'2897',name:'Закуска «Новогодние Ёлочки»'},{id:'4185',name:'Мясные рулетики'},{id:'2809',name:'Бифштекс по-мексикански'},{id:'2814',name:'Салат из авокадо и груш'},{id:'2804',name:'Курица с брюссельской капустой'},{id:'2778',name:'Греческий суп авголемоно'},{id:'2763',name:'Баранина с кус-кусом'},{id:'2621',name:'Сырные палочки из слоеного теста'},{id:'2627',name:'Рулетики из баклажанов'},{id:'2611',name:'Кофейный пирог с апельсинами'},{id:'2601',name:'Слоеные коржики с заварным кремом'},{id:'2536',name:'Творожные кольца с куриным салатом'},{id:'2507',name:'Тушеная капуста под соусом'},{id:'2575',name:'Суккоташ (кукурузно-фасолевая похлебка)'},{id:'2580',name:'Бараньи ребрышки с рисом'},{id:'2491',name:'Кукурузные блинчики с творогом и кумкватом'},{id:'4186',name:'Цуккини по-итальянски'},{id:'2464',name:'Миндальное печенье'},{id:'2437',name:'Венгерский гарнир'},{id:'2443',name:'Сангрита'},{id:'2430',name:'Мясо в цахтоне'},{id:'2395',name:'Манные оладьи'},{id:'2423',name:'Неклассический глинтвейн'},{id:'2376',name:'Ежевичный пирог'},{id:'2285',name:'Курица в «рукаве» с гарниром из чечевицы'},{id:'2264',name:'Пикантные баклажаны с сыром'},{id:'2256',name:'«А-ля крошка-картошка»'},{id:'2362',name:'Блинчики с сердцем'},{id:'2191',name:'Салат-коктейль из креветок'},{id:'2155',name:'Среднеазиатское блюдо «Лагман»'},{id:'2137',name:'Баклажаны по-гречески'},{id:'2090',name:'Голубцы мясные'},{id:'2129',name:'Пирог с кабачками'},{id:'2105',name:'Пирог со смородиной'},{id:'2077',name:'Овощи в сметане'},{id:'2013',name:'Суп-крем из кабачков с авокадо'},{id:'2072',name:'Салат «Авокадо и креветки»'},{id:'2067',name:'Запеченный судак под сыром'},{id:'2056',name:'Сангрия'},{id:'2029',name:'Салат из креветок и авокадо'},{id:'1955',name:'Тыква по-итальянски'},{id:'2004',name:'Пирожки с вишней'},{id:'1886',name:'Брокколи, запеченная в твороге'},{id:'1875',name:'Бананы под соусом'},{id:'1897',name:'Куриный салат с манго'},{id:'1903',name:'Чернослив в кляре'},{id:'1912',name:'Манговый чай'},{id:'1925',name:'Мясо в армянском лаваше'},{id:'1908',name:'Тальятелле с индейкой и креветками'},{id:'1869',name:'Пирожки с грибами'},{id:'1827',name:'Солянка'},{id:'1648',name:'Печеночный торт'},{id:'1662',name:'Сырный суп с креветками'},{id:'1813',name:'Жареные креветки'},{id:'1810',name:'Запеченные куриные крылышки'},{id:'1639',name:'Тропический коктейль'},{id:'1636',name:'Гречневые зразы с творогом'},{id:'1709',name:'Безалкогольный мохито'},{id:'1697',name:'Баскский пирог с вишней'},{id:'1601',name:'Рецепт мохито'},{id:'1593',name:'Печень-гриль'},{id:'1581',name:'Салат из фризе'},{id:'1589',name:'Запеченное рагу'},{id:'1567',name:'Салат «А-ля Цезарь»'},{id:'1546',name:'Лазанья с фаршем'},{id:'1549',name:'Салат с цветной капустой'},{id:'1554',name:'Творожное печенье'},{id:'1541',name:'Салат из стеблей сельдерея'},{id:'1457',name:'Помидоры с чесноком'},{id:'1537',name:'Яблочные звездочки'},{id:'1523',name:'Салат из рукколы'},{id:'1528',name:'Бочонки из кабачков'},{id:'1476',name:'Пирог с сыром и мясом'},{id:'1454',name:'Итальянский салат с сыром и макаронами'},{id:'1410',name:'Космополитен'},{id:'1406',name:'Легкий салат с клубникой'},{id:'1400',name:'Имбирный напиток «lomi-lomi» (напиток любви)'},{id:'1384',name:'Шашлык в беконе'},{id:'1370',name:'Ямайский куриный салат'},{id:'1363',name:'Курица в миндальной корочке'},{id:'1430',name:'Маки суши (Сякэ маки, эби маки)'},{id:'1340',name:'Кукурузные сырники с вяленой грушей'},{id:'1322',name:'Запеченные овощи в томатном желе'},{id:'1315',name:'Бананово-клубничный коктейль'},{id:'1308',name:'Творожный пирог с черносливом и морковью'},{id:'1299',name:'Каннелони с сырно-шпинатной начинкой'},{id:'1292',name:'Чайное суфле'},{id:'1285',name:'Клубничные корзинки (тарталетки) со сливочным желе'},{id:'1273',name:'Желейный вишневый торт'},{id:'1258',name:'Бифштекс'},{id:'1232',name:'Террин из баклажанов и перцев'},{id:'1193',name:'Кабачки с сыром и морковью'},{id:'1188',name:'Семга под сыром с овощами'},{id:'1156',name:'Чанфайна'},{id:'1136',name:'Украинский борщ'},{id:'1126',name:'Бананы с творогом и йогуртом'},{id:'1122',name:'Медово-яблочный пирог'},{id:'1116',name:'Банановый пирог'},{id:'1108',name:'Свиная корейка в соевом кляре'},{id:'1101',name:'Пышные оладьи с вишней'},{id:'1097',name:'Бразильский кофейный крем'},{id:'1084',name:'Миндальный кекс с вишней'},{id:'1077',name:'Творожный торт с клубникой'},{id:'1046',name:'Немецкий творожный торт'},{id:'1034',name:'Китайский куриный суп'},{id:'1024',name:'Салат «Краш-тест китайского джипа»'},{id:'1021',name:'Банановый кекс'},{id:'998',name:'Тыквенно-ореховый пирог'},{id:'987',name:'Тыквенный гарнир по-гречески'},{id:'982',name:'Тыква в слоеном тесте'},{id:'976',name:'Тыквенный суп-пюре'},{id:'968',name:'Тыква в песочном тесте'},{id:'950',name:'Псевдо-паэлья'},{id:'939',name:'Эдвардианский тыквенный пирог'},{id:'1419',name:'Пюре из баклажанов'}];