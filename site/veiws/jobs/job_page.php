<?php
$job = $common_data['job'];
$DB = Application::$DB;
$creater_user = $DB->query('
    SELECT u.*
      FROM users u
        WHERE u."id"='. $job['createrUserID'])->fetchAll();
$kinds_of_jobs = $DB->query('
    SELECT *
      FROM links_kinds_of_jobs_objects lkj
      LEFT JOIN kinds_of_jobs kj ON lkj."kindOfJobID" = kj."id"
        WHERE lkj."objectID"='. $job['id'])->fetchAll();
$kinds_of_jobs_arr = [];
foreach($kinds_of_jobs as $kind_of_job){
    $kinds_of_jobs_arr[] = $kind_of_job['name'];
}
$answers = $DB->query('
    SELECT u.*,
           uj."description" as uj_description,
           uj."created" as uj_created
      FROM users_jobs uj
      JOIN users u ON uj."fromUserID" = u."id"
        WHERE uj."jobID"='. $job['id'])->fetchAll();
?>

<h1><?php echo $job['name']; ?></h1>
<span>Номер объекта: <?php echo $job['id']; ?></span>
<span>Опубликованно: <?php echo date('j.m.Y H:i:s', strtotime($job['created'])); ?></span>
<hr/>
<div><?php echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<div><?php if($job['bargain']) echo 'По договоренности'; else echo $job['amount'];?></div>
<hr/>
<br/>
<div><strong>Адрес</strong>: <?php echo $job['street'] .' '. $job['house'];?></div>
<div><strong>Виды работ</strong>: <?php echo implode(', ', $kinds_of_jobs_arr);?></div>
<div><strong>График работ</strong>: <?php echo $job['s_name'];?></div>
<br/>
<br/>
Требования:
<div><?php echo $job['require'];?></div>
Обязанности:
<div><?php echo $job['description'];?></div>
Условия:
<div><?php echo $job['conditions'];?></div>
<br/>
<br/>
<hr/>
<br/>
<div>Ответы:</div>
<?php if(!empty($answers)){
    foreach($answers as $answer){
        $part = '<br/><div style="border: 2px solid #999;">';
        $part .= '<div><img src="'. $answer['avatar'] .'"/>';
        $part .= '<span>'. $answer['surname'] .' '. $answer['name'] .' '. $answer['second_name'] .'</span><br/>';
        $part .= '<span>'. date('j.m.Y H:i:s', strtotime($answer['uj_created'])) .'</span>';
        $part .= '<div><a href="#">+</a>6 <a href="#">-</a>1</div>';
        $part .= '<div>'. $answer['uj_description'] .'</div>';
        $part .= '<div><a href="/messages/">Написать кандидату</a></div>';
        $part .= '<input type="button" value="Принять" />';
        $part .= '</div><br/>';
        echo $part;
    }
} ?>
