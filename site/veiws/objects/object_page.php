<?php

$object = $common_data['object'];
$DB = Application::$DB;
$applicationURL = Application::$URL;
$checkSubmitUser = false;

if(isset($_SESSION['user'])){
    $checkSubmitUser = $DB->query('SELECT uo.* FROM users_objects uo WHERE uo."fromUserID"='. $_SESSION['user']['id'] .' AND uo."objectID" = '. $object['id'])->fetchAll();
    if(!empty($checkSubmitUser)) $checkSubmitUser = true;
}

// обработаем POST - например при подписывании на объект
if((isset($_POST['submitOrder']) || isset($_POST['unsubmitOrder'])) && isset($_SESSION['user'])){
    if(isset($_POST['submitOrder'])){
        $sql = $DB->prepare('
            INSERT INTO users_objects ("description", "fromUserID", "objectID")
              VALUES(\''. $_POST['description'] .'\', \''. $_SESSION['user']['id'] .'\', \''. $_POST['objectID'] .'\')');
        if($sql->execute() === true) $checkSubmitUser = true;
    }elseif(isset($_POST['unsubmitOrder'])){
        $sql = $DB->prepare('DELETE FROM users_objects WHERE "objectID"='. $_POST['objectID'] .' AND "fromUserID="'. $_SESSION['user']['id']);
        if($sql->execute() === true) $checkSubmitUser = false;
    }
}elseif(isset($_POST['user_to_object']) && !empty($_SESSION['user'])){
    $update_object = $DB->prepare('UPDATE objects SET "workerID"=\''. $_POST['user_to_object'] .'\' WHERE "id"='. $applicationURL[2]);
    if($update_object->execute() === true){
        $object['workerID'] = $_POST['user_to_object'];
    }
}elseif(isset($_POST['user_remove_object']) && !empty($_SESSION['user'])){
    $update_object = $DB->prepare('UPDATE objects SET "workerID"=NULL WHERE "id"='. $applicationURL[2]);
    if($update_object->execute() === true){
        $object['workerID'] = NULL;
    }
}

$creater_user = $DB->query('
    SELECT u.*
      FROM users u
        WHERE u."id"='. $object['createrUserID'])->fetchAll();
$worker_user = null;
if(!empty($object['workerID']))
    $worker_user = $DB->query('
        SELECT u.*
          FROM users u
            WHERE u."id"='. $object['workerID'])->fetchAll();
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
    $object_imgs_arr[] = '<img width="100px" src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'"/>';
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
<?php echo implode(' ', $object_imgs_arr);?>
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
<?php if(!empty($_SESSION['user'])){
    if($_SESSION['user']['id'] !== $object['createrUserID']){
        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><textarea name="description"></textarea><br/><input type="submit" name="submitOrder" value="Откликнуться"/></form>';
        else echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
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
        $part .= '<span>'. date('j.m.Y H:i:s', strtotime($answer['uo_created'])) .'</span>';
        $part .= '<div><a href="#">+</a>6 <a href="#">-</a>1</div>';
        $part .= '<div>'. $answer['uo_description'] .'</div>';
        if(!empty($_SESSION['user'])){
            if($_SESSION['user']['id'] !== $answer['id']) $part .= '<div><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $answer['id'] .'/">Написать кандидату</a></div>';
            if($_SESSION['user']['id'] === $object['createrUserID']){
                if((int)$object['workerID'] === $answer['id']){
                    $part .= '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_remove_object"/><input type="submit" value="Отказаться" /></form>';
                }else{
                    if(empty($object['workerID']))
                        $part .= '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_to_object"/><input type="submit" value="Принять" /></form>';
                }
            }
        }
        $part .= '</div><br/>';
        echo $part;
    }
} ?>