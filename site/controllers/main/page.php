<?php
// ище данные для формы поиска
$DB = Application::$DB;
$masters_online = $DB->query('SELECT COUNT(id) as masters_online FROM users')->fetch();
$jobs = $DB->query('SELECT COUNT(id) as jobs FROM jobs WHERE status<>\'archive\'')->fetch();
$companies = $DB->query('SELECT COUNT(id) as companies FROM users WHERE type_of_registration = 0')->fetch();

if(isset($_GET['search'])){
    $href = "/". $_GET['type'] .'/?search=true&cityID='. $_GET['cityID'] .'&search_str='. $_GET['search_str'] .'&areaJID='. $_GET['areaJID'];
    echo '<meta http-equiv="refresh" content="1;URL='. $href .'">';
}

$dopSQL[] = 'o.type_of_kind<>2';
$dopSQL[] = 'o.status<>\'archive\'';
$sql = '
    SELECT o.*,
           (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = o.id AND c."type"=\'object_comment\') as comment_count,
            c.name as city_name
      FROM objects o
      LEFT JOIN cities c ON o."cityID" = c.id';
if(!empty($dopSQL)) $sql .= ' WHERE '. implode(' AND ', $dopSQL);
$objects = $DB->query($sql)->fetchAll();
$cities = $DB->query('SELECT * FROM cities')->fetchAll();
$citiesOptions = [];
foreach($cities as $city) $citiesOptions[] = '<option value="'. $city['id'] .'">'. $city['name'] .'</option>';
$area_of_jobs_select = $DB->query('SELECT * FROM kinds_of_jobs')->fetchAll();
foreach($area_of_jobs_select as $area_of_job) $area_of_jobs[] = '<option value="'. $area_of_job['id'] .'">'. $area_of_job['name'] .'</option>';

/*
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
       <div class="tipical-headline">Содружество строителей «На объекте»</div>
       <ul class="scores-block clearfix">
           <li class="scores-block-item">Мастеров онлайн: <span><?php echo $masters_online['masters_online']; ?></span></li>
           <li class="scores-block-item">Вакансий на сайте: <span><?php echo $jobs['jobs']; ?></span></li>
           <li class="scores-block-item">Компаний: <span><?php echo $companies['companies']; ?></span></li>
       </ul>
       
       <p>Сайт «На объекте» - уникальный ресурс в сфере строительства. Вы строитель и вам нужны новые строительные проекты (объекты, заказы)? Вы легко найдете их здесь! «На объекте» - это содружество строителей. Здесь для вас есть не только работа, но и необходимые инструменты. Вы крупная строительная компания и вам требуются подрядчики? Вам тоже сюда! «На объекте» - это большой банк специалистов строительной отрасли.</p>
       <p>Сайт «На объекте» - эффективная площадка для поиска интересных строительных проектов различной сложности, а также специалистов, готовых реализовывать эти проекты. Система поиска сайта гарантирует одинаковое число просмотров для каждого участника.</p>
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
        <form class="search-block-form" method="GET" action="/masters/">
            <input type="hidden" value="true" name="search" />
            <fieldset>
                <div class="search-block-holder clearfix">
                    <div class="search-block-left">
                        <select id="masters-select" class="tipical-select" name="type">
                            <option value="masters">Мастера</option>
                            <option value="objects">Заказы</option>
                            <option value="jobs">Вакансии</option>
                        </select>
                        <select class="tipical-select" name="cityID">
                            <option value="">По всем городам</option>
                            <?php echo implode('', $citiesOptions) ?>
                        </select>
                        <select id="jobs-select" class="tipical-select" name="areas_for_job[]">
                            <?php echo implode('', $area_of_jobs) ?>
                        </select>
                    </div>
                    <div class="search-block-right">
                        <textarea class="tipical-textarea" name="search_str"></textarea>
                        <button class="tipical-button" type="submit">НАЙТИ</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="tipical-content-headline">Новые объекты</div>
    <div class="objects-holder">
        <?php foreach($objects as $object){
            $object_imgs = $DB->query('
                SELECT *
                  FROM objects_imgs oi
                    WHERE oi."objectID"='. $object['id'])->fetchAll();
            $object_imgs_arr = [];
            foreach($object_imgs as $object_img)
                $object_imgs_arr[] = '<img data-u="image" width="100px" height="100px" src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'"/>';
        ?>
        <div class="object-item clearfix">
            <div class="object-item-description">
                <div class="object-item-headline"><a href="/objects/<?php echo $object['id']; ?>/"><?php echo $object['name']; ?></a></div>
                <div class="object-item-info">
                    <div class="snip-desription">Краткое описание.</div>
                    <?php echo $object['description']; ?>
                    <div class="photo-carousel-standart">
                        <div id="jssor_1" class="rotator-holder">
                            <!-- Loading Screen -->
                            <div data-u="loading" class="rotator-inner">
                                <div class="rotator-inner-block"></div>
                                <div class="rotator-inner-load"></div>
                            </div>
                            <div data-u="slides" class="rotator-content">
                                <div style="display: none;">
                                <?php echo implode('</div><div style="display: none;">', $object_imgs_arr); ?>
                                </div>
                            </div>
                            <!-- Bullet Navigator -->
                            <div data-u="navigator" class="jssorb03" style="bottom:10px;right:10px;">
                                <!-- bullet navigator item prototype -->
                                <div data-u="prototype" style="width:21px;height:21px;">
                                    <div data-u="numbertemplate"></div>
                                </div>
                            </div>
                            <!-- Arrow Navigator -->
                            <span data-u="arrowleft" class="jssora03l" style="top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
                            <span data-u="arrowright" class="jssora03r" style="top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="object-item-meta">
                <div class="object-item-meta-main">
                    <div class="object-meta-date"><?php echo date('j.m.Y | H:i', strtotime($object['created'])); ?></div>
                    <div class="object-meta-place">г. <?php echo $object['city_name']; ?></div>
                </div>
                <div class="object-item-meta-price"><?php echo $object['amount']; ?> <span>руб.</span></div>
                <a href="<?php echo '/objects/'. $object['id'] .'/'; ?>" class="answers"><?php if(!empty($object['comment_count'])) echo $object['comment_count'] .' ответ(ов)'; else echo 'Ответов нет'; ?></a>
            </div>
        </div>
        <?php } ?>
        <div class="please-login"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</div>
    </div>
</div>