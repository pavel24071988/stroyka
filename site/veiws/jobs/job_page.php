<?php
$job = $common_data['job'];
$DB = Application::$DB;
$checkSubmitUser = false;

if(isset($_SESSION['user'])){
    $checkSubmitUser = $DB->query('SELECT uj.* FROM users_jobs uj WHERE uj."fromUserID"='. $_SESSION['user']['id'] .' AND uj."jobID" = '. $job['id'])->fetchAll();
    if(!empty($checkSubmitUser)) $checkSubmitUser = true;
}

// обработаем POST - например при подписывании на объект
if((isset($_POST['submitOrder']) || isset($_POST['unsubmitOrder'])) && isset($_SESSION['user'])){
    if(isset($_POST['submitOrder'])){
        $sql = $DB->prepare('
            INSERT INTO users_jobs ("description", "fromUserID", "jobID")
              VALUES(\''. $_POST['description'] .'\', \''. $_SESSION['user']['id'] .'\', \''. $_POST['jobID'] .'\')');
        if($sql->execute() === true) $checkSubmitUser = true;
    }elseif(isset($_POST['unsubmitOrder'])){
        $sql = $DB->prepare('DELETE FROM users_jobs WHERE "jobID"='. $_POST['jobID']);
        if($sql->execute() === true) $checkSubmitUser = false;
    }
}

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

<?php
    echo '<br/>';
    $edit_buttons = '';
    if(!empty($_SESSION['user'])){
        if($_SESSION['user']['id'] === $job['createrUserID']){
            $edit_buttons = '<div><a href="/jobs/'. $job['id'] .'/edit/">Редактировать</a> <a href="/jobs/'. $job['id'] .'/delete/">Удалить</a></div>';
            echo Application::getLeftMenu();
        }
    }
    echo '<br/>';
    echo $edit_buttons;
?>

<h1><?php echo $job['name']; ?></h1>
<span>Номер вакансии: <?php echo $job['id']; ?></span>
<span>Опубликованно: <?php echo date('j.m.Y H:i:s', strtotime($job['created'])); ?></span>
<hr/>
<div><?php echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<div><?php if($job['bargain']) echo 'По договоренности'; else echo $job['amount']; ?></div>
<hr/>
<br/>
<div><strong>Адрес</strong>: <?php echo $job['street'] .' '. $job['house']; ?></div>
<div><strong>Виды работ</strong>: <?php echo implode(', ', $kinds_of_jobs_arr); ?></div>
<div><strong>График работ</strong>: <?php echo $job['s_name']; ?></div>
<br/>
<br/>
Требования:
<div><?php echo $job['require'];?></div>
Обязанности:
<div><?php echo $job['description'];?></div>
Условия:
<div><?php echo $job['conditions'];?></div>
<br/>
<?php if(!empty($_SESSION['user'])){
    if($_SESSION['user']['id'] !== $job['createrUserID']){
        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><textarea name="description"></textarea><br/><input type="submit" name="submitOrder" value="Откликнуться"/></form>';
        else echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
    }
}
?>
<br/>
<hr/>
<br/>
<div>Ответы:</div>
<?php if(!empty($answers)){
    foreach($answers as $answer){
        $part = '<br/><div style="border: 2px solid #999;">';
        $part .= '<div><img width="100px" src="'. $answer['avatar'] .'"/>';
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
