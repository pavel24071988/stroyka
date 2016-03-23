<?php
$object = $common_data['object'];
$DB = Application::$DB;
$creater_user = $DB->query('
    SELECT u.*
      FROM users u
        WHERE u."id"='. $object['createrUserID'])->fetchAll();
$kinds_of_jobs = $DB->query('
    SELECT *
      FROM links_kinds_of_jobs_objects lkj
      LEFT JOIN kinds_of_jobs kj ON lkj."kindOfJobID" = kj."id"
        WHERE lkj."objectID"='. $object['id'])->fetchAll();
$kinds_of_jobs_arr = [];
foreach($kinds_of_jobs as $kind_of_job){
    $kinds_of_jobs_arr[] = $kind_of_job['name'];
}
$object_photoes = $DB->query('
    SELECT *
      FROM objects_imgs oi
        WHERE oi."objectID"='. $object['id'])->fetchAll();
$object_photoes_arr = [];
foreach($object_photoes as $object_photo){
    $object_photoes_arr[] = '<img src="'. $object_photo['src'] .'"/>';
}
?>

<h1><?php echo $object['name']; ?></h1>
<span>Номер объекта: <?php echo $object['id']; ?></span>
<span>Опубликованно: <?php echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></span>
<hr/>
<div>Заказчик: <?php echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<div><strong>Бюджет</strong>: <?php echo $object['amount'];?></div>
<hr/>
<br/>
<div><strong>Виды работ</strong>: <?php echo implode(', ', $kinds_of_jobs_arr);?></div>
<div><strong>Адрес</strong>: <?php echo $object['street'] .' '. $object['house'];?></div>
<br/>
<br/>
<span>Сроки: с <?php echo date('j.m.Y', strtotime($object['dateFrom'])); ?> по <?php echo date('j.m.Y', strtotime($object['dateTo'])); ?></span>
<div>Наличие СРО и лицензий: <?php echo $object['cpo'];?></div>
<div>Требуемый исполнитель: <?php if($object['type_of_kind'] === 0) echo 'Частный мастер'; else echo 'Бригада'; ;?></div>
<div>Требуемый стаж: <?php echo $object['require'];?></div>
<br/>
<br/>
<div style="width: 400px;">Описание объекта: <?php echo $object['description'];?></div>
<br/>
<br/>
<div style="width: 400px;"><?php echo implode(' ', $object_photoes_arr);?></div>
<hr/>
<br/>