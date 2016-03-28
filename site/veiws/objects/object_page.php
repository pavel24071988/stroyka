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
$object_imgs = $DB->query('
    SELECT *
      FROM objects_imgs oi
        WHERE oi."objectID"='. $object['id'])->fetchAll();
$object_imgs_arr = [];
foreach($object_imgs as $object_img){
    $object_imgs_arr[] = '<img src="'. $object_img['src'] .'"/>';
}
$object_docs = $DB->query('
    SELECT *
      FROM objects_docs oi
        WHERE oi."objectID"='. $object['id'])->fetchAll();
$object_docs_arr = [];
foreach($object_docs as $object_doc){
    $object_docs_arr[] = '<li><a href="'. $object_doc['src'] .'"/>'. $object_doc['name'] .'</a></li>';
}
$answers = $DB->query('
    SELECT u.*,
           uo."description" as uo_description,
           uo."created" as uo_created
      FROM users_objects uo
      JOIN users u ON uo."fromUserID" = u."id"
        WHERE uo."objectID"='. $object['id'])->fetchAll();
?>

<?php
    echo '<br/>';
    $edit_buttons = '';
    if(!empty($_SESSION['user'])){
        if($_SESSION['user']['id'] === $object['createrUserID']){
            $edit_buttons = '<div><a href="/objects/'. $object['id'] .'/edit/">Редактировать</a> <a href="/objects/'. $object['id'] .'/delete/">Удалить</a></div>';
            echo Application::getLeftMenu();
        }
    }
    echo '<br/>';
    echo $edit_buttons;
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
<div style="width: 400px;"><?php echo implode(' ', $object_imgs_arr);?></div>
<hr/>
<br/>
<div>Приложенные файлы</div>
<?php if(!empty($object_docs_arr)){ ?>
<div style="width: 400px;">
<ol>
    <?php echo implode(' ', $object_docs_arr);?>
</ol>
</div>
<?php } ?>
<div>Рекомендации заказчику: <?php echo $object['recomendations']; ?></div>
<hr/>
<br/>
<div>Ответы:</div>
<?php if(!empty($answers)){
    foreach($answers as $answer){
        $part = '<br/><div style="border: 2px solid #999;">';
        $part .= '<div><img src="'. $answer['avatar'] .'"/>';
        $part .= '<span>'. $answer['surname'] .' '. $answer['name'] .' '. $answer['second_name'] .'</span><br/>';
        $part .= '<span>'. date('j.m.Y H:i:s', strtotime($answer['uo_created'])) .'</span>';
        $part .= '<div><a href="#">+</a>6 <a href="#">-</a>1</div>';
        $part .= '<div>'. $answer['uo_description'] .'</div>';
        $part .= '<div><a href="/messages/">Написать кандидату</a></div>';
        $part .= '<input type="button" value="Принять" />';
        $part .= '</div><br/>';
        echo $part;
    }
} ?>
