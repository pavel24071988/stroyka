<?php
// ище данные для формы поиска
/*$DB = Application::$DB;

$cities = $DB->query('SELECT * FROM cities c')->fetchAll();
$cities_str = '';
foreach($cities as $city){
    $cities_str .= '<option value="'. $city['id'] .'">'. $city['name'] .'</option>';
}

$area_of_jobs = $DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
$area_of_jobs_str = '';
foreach($area_of_jobs as $area_of_job){
    $area_of_jobs_str .= '<option value="'. $area_of_job['id'] .'">'. $area_of_job['name'] .'</option>';
}*/
?>
<!--
<form action="/search/" method="POST">
    <select name="object">
        <option value="1">Проекты</option>
        <option value="2">Вакансии</option>
        <option value="3">Мастера</option>
    </select>
    <br/>
    <select name="city">
        <option value="0">По всем городам</option>
        <?php echo $cities_str; ?>
    </select>
    <br/>
    <select name="area_of_job">
        <?php echo $area_of_jobs_str; ?>
    </select>
    <br/>
    <input type="text" name="search_str"/>
    <input type="submit" value="Найти"/>
</form>
-->
<div class="sub-header">
    <div class="inner-wrapper">
       <div class="tipical-headline">Строительный портал «На объекте»</div>
       <ul class="scores-block clearfix">
           <li class="scores-block-item">Мастеров онлайн: <span>293</span></li>
           <li class="scores-block-item">Вакансий на сайте: <span>2154</span></li>
           <li class="scores-block-item">Компаний: <span>1045</span></li>
       </ul>
       <div class="sub-header-headline">Здесь вводный текст кратко описывает суть и функционал сайта.</div>
       <p>Психологическая среда, как принято считать, все еще интересна для многих. Интересно отметить, что рейтинг разнородно отражает культурный показ баннера. Практика однозначно показывает, что создание приверженного покупателя парадоксально переворачивает креативный рейтинг. Опрос, пренебрегая деталями, отталкивает коллективный медийный канал, опираясь на опыт западных коллег.</p>
       <p>Согласно предыдущему, внутрифирменная реклама программирует фирменный стиль. Комплексный анализ ситуации регулярно детерминирует нишевый проект. Формат события усиливает из ряда воноснован на тщательном анализе данных.</p>
       <ul class="sub-header-slogan">
           <li class="sub-header-slogan-item">
               У вас есть заказ?<br><span>Создайте заявку и найдите<br>исполнителей!</span>
           </li>
           <li class="sub-header-slogan-item">
               Ищете сотрудника на<br>постоянную работу?<br><span>Создайте вакансию здесь!</span>
           </li>
           <li class="sub-header-slogan-item">
               Вы - мастер?<br><span>Сотни предложений на<br>нашем сайте для вас!</span>
           </li>
       </ul>
    </div>
</div>

<div class="content">
    <div class="search-block">
        <div class="search-block-headline">Поиск</div>
        <form class="search-block-form">
            <fieldset>

            </fieldset>
        </form>
    </div>
    <div class="tipical-content-headline">Новые объекты</div>
    <div class="objects-holder">
        <div class="object-item clearfix">
            <div class="object-item-description">
                <div class="object-item-headline">Строительство бани</div>
                <div class="object-item-info">
                    <div class="snip-desription">Краткое описание.</div>
                    Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                </div>
            </div>
            <div class="object-item-meta">
                <div class="object-item-meta-main">
                    <div class="object-meta-date">28 марта 2016 | 10:24</div>
                    <div class="object-meta-place">г. Воронеж</div>
                </div>
                <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                <a href="#" class="answers">Ответов нет</a>
            </div>
        </div>
        <div class="object-item clearfix">
            <div class="object-item-description">
                <div class="object-item-headline">Строительство бани</div>
                <div class="object-item-info">
                    <div class="snip-desription">Краткое описание.</div>
                    Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                </div>
            </div>
            <div class="object-item-meta">
                <div class="object-item-meta-main">
                    <div class="object-meta-date">28 марта 2016 | 10:24</div>
                    <div class="object-meta-place">г. Воронеж</div>
                </div>
                <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                <a href="#" class="answers">Ответов нет</a>
            </div>
        </div>
        <div class="object-item clearfix">
            <div class="object-item-description">
                <div class="object-item-headline">Строительство бани</div>
                <div class="object-item-info">
                    <div class="snip-desription">Краткое описание.</div>
                    Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                    <div class="object-item-images clearfix">
                        <img src="images/img1.png">
                        <img src="images/img2.png">
                        <img src="images/img3.png">
                        <img src="images/img4.png">
                    </div>
                </div>
            </div>
            <div class="object-item-meta">
                <div class="object-item-meta-main">
                    <div class="object-meta-date">28 марта 2016 | 10:24</div>
                    <div class="object-meta-place">г. Воронеж</div>
                </div>
                <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                <a href="#" class="answers">Ответов нет</a>
            </div>
        </div>
        <div class="please-login"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</div>
    </div>
</div>