<h1>Строительный портал «На объекте»</h1>
<div style="width: 500px;">
Здесь вводный текст кратко описывает суть и функционал сайта. 
Психологическая среда, как принято считать, все еще интересна для многих. Интересно отметить, что рейтинг разнородно отражает культурный показ баннера. Практика однозначно показывает, что создание приверженного покупателя парадоксально переворачивает креативный рейтинг. Опрос, пренебрегая деталями, отталкивает коллективный медийный канал, опираясь на опыт западных коллег.
Согласно предыдущему, внутрифирменная реклама программирует фирменный стиль. Комплексный анализ ситуации регулярно детерминирует нишевый проект. Формат события усиливает из ряда вон выходящий product placement. Пак-шот детерминирует целевой трафик, полагаясь на инсайдерскую информацию. Охват аудитории основан на тщательном анализе данных.
</div>
<br/><br/><br/>
<div>
    <div style="float: left; width: 250px;">У вас есть заказ? Создайте заявку и найдите исполнителей!</div>
    <div style="float: left; width: 250px;">Ищете сотрудника на постоянную работу? Создайте вакансию здесь!</div>
    <div style="float: left; width: 250px;">Вы - мастер? Сотни предложений на нашем сайте для вас!</div>
</div>
<br/><br/><br/>
<div>Поиск</div>
<?php
// ище данные для формы поиска
$DB = Application::$DB;

$cities = $DB->query('SELECT * FROM cities c')->fetchAll();
$cities_str = '';
foreach($cities as $city){
    $cities_str .= '<option value="'. $city['id'] .'">'. $city['name'] .'</option>';
}

$area_of_jobs = $DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
$area_of_jobs_str = '';
foreach($area_of_jobs as $area_of_job){
    $area_of_jobs_str .= '<option value="'. $area_of_job['id'] .'">'. $area_of_job['name'] .'</option>';
}
?>
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
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

