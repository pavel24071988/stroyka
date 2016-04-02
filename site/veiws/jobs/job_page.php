<?php

$job = $common_data['job'];
$DB = Application::$DB;
$applicationURL = Application::$URL;
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
        $sql = $DB->prepare('DELETE FROM users_jobs WHERE "jobID"='. $_POST['jobID'] .' AND "fromUserID"='. $_SESSION['user']['id']);
        if($sql->execute() === true) $checkSubmitUser = false;
    }
}elseif(isset($_POST['user_to_job']) && !empty($_SESSION['user'])){
    $update_job = $DB->prepare('UPDATE jobs SET "workerID"=\''. $_POST['user_to_job'] .'\' WHERE "id"='. $applicationURL[2]);
    if($update_job->execute() === true){
        $job['workerID'] = $_POST['user_to_job'];
    }
}elseif(isset($_POST['user_remove_job']) && !empty($_SESSION['user'])){
    $update_job = $DB->prepare('UPDATE jobs SET "workerID"=NULL WHERE "id"='. $applicationURL[2]);
    if($update_job->execute() === true){
        $job['workerID'] = NULL;
    }
}

$creater_user = $DB->query('
    SELECT u.*
      FROM users u
        WHERE u."id"='. $job['createrUserID'])->fetchAll();
$worker_user = null;
if(!empty($job['workerID']))
    $worker_user = $DB->query('
        SELECT u.*
          FROM users u
            WHERE u."id"='. $job['workerID'])->fetchAll();
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
<?php if(!empty($_SESSION['user']) && empty($job['workerID'])){
    if($_SESSION['user']['id'] !== $job['createrUserID']){
        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><textarea name="description"></textarea><br/><input type="submit" name="submitOrder" value="Откликнуться"/></form>';
        else echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
    }
}
?>
<br/>
<?php
    if(!empty($worker_user[0])){
        echo 'Исполнитель: '. $worker_user[0]['name'] .' '. $worker_user[0]['surname'];
        if(!empty($_SESSION['user']))
            echo '<br/><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $worker_user[0]['id'] .'/">написать исполнителю</a>';
    }else{
        echo 'Исполнитель не назначен.';
    }
?>
<hr/>
<br/>
<div>Ответы:</div>
<?php if(!empty($answers)){
    foreach($answers as $answer){
        $part = '<br/><div style="border: 2px solid #999;">';
        $part .= '<div><img width="100px" src="/images/users/'. $answer['id'] .'/'. $answer['avatar'] .'"/>';
        $part .= '<span>'. $answer['surname'] .' '. $answer['name'] .' '. $answer['second_name'] .'</span><br/>';
        $part .= '<span>'. date('j.m.Y H:i:s', strtotime($answer['uj_created'])) .'</span>';
        $part .= '<div><a href="#">+</a>6 <a href="#">-</a>1</div>';
        $part .= '<div>'. $answer['uj_description'] .'</div>';
        if(!empty($_SESSION['user'])){
            if($_SESSION['user']['id'] !== $answer['id']) $part .= '<div><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $answer['id'] .'/">Написать кандидату</a></div>';
            if($_SESSION['user']['id'] === $job['createrUserID']){
                if((int)$job['workerID'] === $answer['id']){
                    $part .= '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_remove_job"/><input type="submit" value="Отказаться" /></form>';
                }else{
                    if(empty($job['workerID']))
                        $part .= '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_to_job"/><input type="submit" value="Принять" /></form>';
                }
            }
        }
        $part .= '</div><br/>';
        echo $part;
    }
} ?>
