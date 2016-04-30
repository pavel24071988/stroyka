<?php

$object = $common_data['object'];
$DB = Application::$DB;
$applicationURL = Application::$URL;
$checkSubmitUser = false;

if(isset($_SESSION['user'])){
    $checkSubmitUser = $DB->query('SELECT uo.* FROM users_objects uo WHERE uo."fromUserID"='. $_SESSION['user']['id'] .' AND uo."objectID" = '. $object['id'])->fetchAll();
    if(!empty($checkSubmitUser)) $checkSubmitUser = true;
    $check_owner = ($_SESSION['user']['id'] === $object['createrUserID']) ? true : false;
}

// обработаем POST - например при подписывании на объект
if((isset($_POST['submitOrder']) || isset($_POST['unsubmitOrder'])) && isset($_SESSION['user'])){
    if(isset($_POST['submitOrder'])){
        $sql = $DB->prepare('
            INSERT INTO users_objects ("description", "fromUserID", "objectID")
              VALUES(\''. $_POST['description'] .'\', \''. $_SESSION['user']['id'] .'\', \''. $_POST['objectID'] .'\')');
        if($sql->execute() === true) $checkSubmitUser = true;
    }elseif(isset($_POST['unsubmitOrder'])){
        $sql = $DB->prepare('DELETE FROM users_objects WHERE "objectID"='. $_POST['objectID'] .' AND "fromUserID"='. $_SESSION['user']['id']);
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
foreach($object_docs as $key => $object_doc){
    $object_docs_arr[] = $key. '. <a href="'. $object_doc['src'] .'"/>'. $object_doc['name'] .'</a>';
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
    /*echo '<br/>';
    $edit_buttons = '';
    if(!empty($_SESSION['user'])){
        if($_SESSION['user']['id'] === $object['createrUserID']){
            $edit_buttons = '<div><a href="/objects/'. $object['id'] .'/edit/">Редактировать</a> <a href="/objects/'. $object['id'] .'/delete/">Удалить</a></div>';
            echo Application::getLeftMenu();
        }
    }
    echo '<br/>';
    echo $edit_buttons;*/
?>
<!--
<h1><?php //echo $object['name']; ?></h1>
<span>Номер объекта: <?php //echo $object['id']; ?></span>
<span>Опубликованно: <?php //echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></span>
<hr/>
<div>Заказчик: <?php //echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<div><strong>Бюджет</strong>: <?php //echo $object['amount'];?></div>
<hr/>
<br/>
<div><strong>Виды работ</strong>: <?php //echo implode(', ', $kinds_of_jobs_arr);?></div>
<div><strong>Адрес</strong>: <?php //echo $object['street'] .' '. $object['house'];?></div>
<br/>
<br/>
<span>Сроки: с <?php //echo date('j.m.Y', strtotime($object['dateFrom'])); ?> по <?php echo date('j.m.Y', strtotime($object['dateTo'])); ?></span>
<div>Наличие СРО и лицензий: <?php //echo $object['cpo'];?></div>
<div>Требуемый исполнитель: <?php //if($object['type_of_kind'] === 0) echo 'Частный мастер'; else echo 'Бригада'; ;?></div>
<div>Требуемый стаж: <?php //echo $object['require'];?></div>
<br/>
<br/>
<div style="width: 400px;">Описание объекта: <?php //echo $object['description'];?></div>
<br/>
<br/>
<?php //echo implode(' ', $object_imgs_arr);?>
<br/>
<div>Приложенные файлы</div>

<div>Рекомендации заказчику: <?php //echo $object['recomendations']; ?></div>
-->
<?php /*if(!empty($_SESSION['user'])){
    if($_SESSION['user']['id'] !== $object['createrUserID']){
        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><textarea name="description"></textarea><br/><input type="submit" name="submitOrder" value="Откликнуться"/></form>';
        else echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
    }
}*/
?>
<?php
    /*if(!empty($worker_user[0])){
        echo 'Исполнитель: '. $worker_user[0]['name'] .' '. $worker_user[0]['surname'];
        if(!empty($_SESSION['user']))
            echo '<br/><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $worker_user[0]['id'] .'/">написать исполнителю</a>';
    }else{
        echo 'Исполнитель не назначен.';
    }*/
?>
<!-- авторезированный пользователь -->
<?php if(!empty($_SESSION['user'])){ ?>
<div class="content">
    <div class="my-page-content clearfix">
        <?php echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="/objects/">Объекты и вакансии</a>
                    </li>
                    <li>
                        <a href="#"><?php echo $object['name']; ?></a>
                    </li>
                </ul>
            </div>
            <div class="product-holder">
                <div class="product-title"><?php echo $object['name']; ?></div>
                <?php if($check_owner){ ?>
                <div class="product-holder-control">
                    <a href="<?php echo '/objects/'. $object['id'] .'/edit/'; ?>">Редактировать</a>
                    <a href="<?php echo '/objects/'. $object['id'] .'/close/'; ?>">Закрыть</a>
                </div>
                <?php } ?>
                <div class="product-meta">
                    <p class="product-meta-title date">Номер объекта: <?php echo $object['id']; ?></p>
                    <div class="product-customer clearfix">
                        <div class="product-customer-left">
                            <?php if(!empty($creater_user)){ ?>
                            <span>Заказчик:</span><br><?php echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?>
                            <?php } ?>
                        </div>
                        <div class="product-customer-right">
                            Бюджет: <?php echo $object['amount']; ?> руб.
                        </div>
                    </div>
                    <?php if(!empty($_SESSION['user'])){ ?>
                    <p class="product-meta-title place">Адрес: <?php echo $object['street'] .' '. $object['house'];?></p>
                    <p class="product-meta-title phone">Тел. +8 987 456 45 45</p>
                    <?php } ?>
                </div>
                <div class="product-sub-meta">
                    <p>Описание объекта заказчиком.</p>
                    <?php echo $object['description'];?><br/><br/>
                    <div class="product-sub-meta-headline">Фото работ</div>
                    <div class="product-photo-holder clearfix">

                    </div>
                    <div class="product-theme">
                        <div class="product-theme-headline">
                            <span>Приложенные файлы</span>
                        </div>
                        <?php if(!empty($object_docs_arr))
                                echo implode('<br>', $object_docs_arr);
                        ?>
                    </div>
                    <div class="product-theme">
                        <div class="product-theme-headline">
                            <span>Ответы</span>
                        </div>
                        <?php
                        if(!empty($answers)){
                            foreach($answers as $answer){
                        ?>
                        <div class="feedback-item">
                            <div class="feedback-item-body clearfix">
                                <div class="feedback-item-avatar">
                                    <a href="<?php echo '/users/'. $answer['id'] .'/'; ?>">
                                        <img src="<?php echo '/images/users/'. $answer['id'] .'/'. $answer['avatar']; ?>" />
                                    </a>
                                </div>
                                <div class="feedback-item-content clearfix">
                                    <div class="feedback-item-content-left">
                                        <div class="feedback-name">
                                            <a href="<?php echo '/users/'. $answer['id'] .'/'; ?>">
                                                <span><?php echo $answer['surname'] .' '. $answer['name'] .' '. $answer['second_name']; ?></span><br><!--Бригада. 20 человек-->
                                            </a>
                                        </div>
                                        <div class="feedback-text"><?php echo $answer['uo_description']; ?></div>
                                        <?php if($_SESSION['user']['id'] !== $answer['id']) echo '<a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $answer['id'] .'/" class="feedback-candidate">Написать кандидату</a>'; ?>
                                    </div>
                                    <div class="feedback-item-content-right">
                                        <div class="feedback-item-date"><?php echo date('j.m.Y H:i:s', strtotime($answer['uo_created'])); ?></div>
                                        <div class="feedback-likes clearfix">
                                            <span class="like"><?php echo 10; ?></span>
                                            <span class="like dislike"><?php echo 0; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="feedback-item-reply">
                                <?php
                                    if(!empty($_SESSION['user'])){
                                        if($_SESSION['user']['id'] === $object['createrUserID']){
                                            if((int)$object['workerID'] === $answer['id'])
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_remove_object"/><input type="submit" value="Отказаться" /></form>';
                                            elseif(empty($object['workerID']))
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_to_object"/><input type="submit" value="Принять" /></form>';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <?php
                            }
                        } ?>
                    </div>
                </div>
            </div>
            <?php 
            if(!empty($_SESSION['user'])){
                if($_SESSION['user']['id'] !== $object['createrUserID']){
                    if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><textarea class="tipical-textarea" name="description"></textarea><input class="tipical-button" style="line-height: normal;" type="submit" name="submitOrder" value="Откликнуться"/></form>';
                    else echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
                }
            }
            ?>
        </div>
    </div>
</div>
<!-- Не авторезированный пользователь -->
<?php }else{ ?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="/">Главная</a>
            </li>
            <li>
                <a href="/orders/">Заказы</a>
            </li>
            <li>
                <a href="#"><?php echo $object['name']; ?></a>
            </li>
        </ul>
    </div>
    <div class="product-holder">
        <div class="product-title"><?php echo $object['name']; ?></div>
        <div class="product-meta">
            <p class="product-meta-title date">Опубликовано: <?php echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></p>
            <div class="product-customer clearfix">
                <div class="product-customer-left">
                    <?php if(!empty($creater_user)){ ?>
                    <span>Заказчик:</span><br><?php echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?>
                    <?php } ?>
                </div>
                <div class="product-customer-right">
                    Бюджет: <?php echo $object['amount']; ?> руб.
                </div>
            </div>
            <?php if(!empty($_SESSION['user'])){ ?>
            <p class="product-meta-title place">Адрес: <?php echo $object['street'] .' '. $object['house'];?></p>
            <p class="product-meta-title phone">Тел. +8 987 456 45 45</p>
            <?php } ?>
        </div>
        <div class="product-sub-meta">
            <p>Описание объекта закачиком.</p>
            <?php echo $object['description'];?>
            <div class="product-sub-meta-headline">Фото работ</div>
            <div class="product-photo-holder clearfix">
                <?php echo implode(' ', $object_imgs_arr); ?>
            </div>
        </div>
    </div>
    <div class="please-login"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</div>
</div>
<?php } ?>
