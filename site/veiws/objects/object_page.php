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
        if(empty($_POST['description'])) $_POST['description'] = '';
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
        $DB->prepare('
            INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
            ('. $_SESSION['user']['id'] .', \'Вы назначены исполнителем на объект № '. $applicationURL[2] .'.\', '. $_POST['user_to_object'] .', \'system_object\', '. $applicationURL[2] .')
        ')->execute();
    }
}elseif(isset($_POST['user_remove_object']) && !empty($_SESSION['user'])){
    $update_object = $DB->prepare('UPDATE objects SET "workerID"=NULL WHERE "id"='. $applicationURL[2]);
    if($update_object->execute() === true){
        $object['workerID'] = NULL;
        $DB->prepare('
            INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
            ('. $_SESSION['user']['id'] .', \'Вы сняты с объекта № '. $applicationURL[2] .'.\', '. $_POST['user_remove_object'] .', \'system_object\', '. $applicationURL[2] .')
        ')->execute();
    }
}

$creater_user = $DB->query('SELECT u.* FROM users u WHERE u."id"='. $object['createrUserID'])->fetch();
$worker_user = null;
if(!empty($object['workerID'])) $worker_user = $DB->query('SELECT u.* FROM users u WHERE u."id"='. $object['workerID'])->fetchAll();
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
    SELECT oi.*, o.amount
      FROM objects_imgs oi
      LEFT JOIN objects o ON oi."objectID" = o.id 
        WHERE oi."objectID"='. $object['id'])->fetchAll();
$object_imgs_arr = [];
$object_imgs_arr_bg = [];
foreach($object_imgs as $key => $object_img){
    $object_imgs_arr[] = '
        <div class="product-photo-item">
            <a href="#work'. ($key+1) .'" class="modal_on product-photo">
                <div class="product-photo-scope"></div>
                <img src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'">
            </a>
            <div class="product-photo-name">Фото '. ($key+1) .'</div>
        </div>
    ';

    $object_imgs_arr_bg[] = '
	    <div id="work'. ($key+1) .'" style="width: 820px;">
		<div class="modal-title">'. $object_img['src'] .'</div>
		<img src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'" class="modal-works-photo" width="820">
		<div class="modal-photo-content">
		    <p><b>Стоимость:</b> '. $object_img['amount'] .' руб.</p>
		    <!--<p><b>Сроки:</b> 8 месяцев</p>
		    <br>
		    <p><b>Комментарий.</b></p>
		    <p>Более того, интеграл Пуассона реально накладывает лист Мёбиуса, что и требовалось доказать. Двойной интеграл продуцирует возрастающий вектор.</p>-->
		</div>
	    </div>
    ';
}
$object_docs = $DB->query('
    SELECT *
      FROM objects_docs oi
        WHERE oi."objectID"='. $object['id'])->fetchAll();
$object_docs_arr = [];
foreach($object_docs as $key => $object_doc){
    $object_docs_arr[] = $key. '. <a href="/data/objects/'. $object_doc['objectID'] .'/'. $object_doc['src'] .'" type="application/file" target="_blank" download>'. $object_doc['name'] .'</a>';
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

<?php
// помещаем в архив
if(!empty($applicationURL['3']) && $applicationURL['3'] === 'close' && $check_owner){
    $update_job = $DB->prepare('UPDATE objects SET "status"=\'archive\' WHERE "id"='. $applicationURL[2])->execute();
    echo '<meta http-equiv="refresh" content="1;URL=/users/'. $_SESSION['user']['id'] .'/my_objects/">';
}
?>

<!--
<h1><?php //echo $object['name']; ?></h1>
<span>Номер объекта: <?php //echo $object['id']; ?></span>
<span>Опубликованно: <?php //echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></span>
<hr/>
<div>Заказчик: <?php //echo $creater_user['surname'] .' '. $creater_user['name'] .' '. $creater_user['second_name']; ?></div>
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
    /*if(!empty($worker_user)){
        echo 'Исполнитель: '. $worker_user['name'] .' '. $worker_user['surname'];
        if(!empty($_SESSION['user']))
            echo '<br/><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $worker_user['id'] .'/">написать исполнителю</a>';
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
                    <?php if($object['type_of_kind'] === 2){ ?>
                        <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_works/'; ?>">Мои работы</a>
                    <?php }else{ ?>
                        <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_objects/'; ?>">Объекты и вакансии</a>
                    <?php } ?>
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
                            <span>Заказчик:</span><br><?php echo $creater_user['surname'] .' '. $creater_user['name'] .' '. $creater_user['second_name']; ?>
                            <?php } ?>
                        </div>
                        <div class="product-customer-right">
                            Бюджет: <?php echo $object['amount']; ?> руб.
                        </div>
                    </div>
                    <?php if(!empty($_SESSION['user'])){ ?>
                    <p class="product-meta-title place">Адрес: <?php echo $object['street'] .' '. $object['house'];?></p>
                    <p class="product-meta-title phone">Тел. <?php echo $object['phone']; ?></p>
                    <?php } ?>
                </div>
                <div class="product-sub-meta">
                    <p>Описание объекта заказчиком.</p>
                    <?php echo $object['description'];?><br/><br/>
                    <div class="product-sub-meta-headline">Фото работ</div>
                    <div class="product-photo-holder clearfix">
                        <?php echo implode(' ', $object_imgs_arr); ?>
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
                        <?php if($check_owner){ ?>
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
                                        <img width="100px" height="81px" src="<?php echo '/images/users/'. $answer['id'] .'/'. $answer['avatar']; ?>" />
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
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_remove_object"/><input class="tipical-button" style="line-height: normal;" type="submit" value="Отказаться" /></form>';
                                            elseif(empty($object['workerID']))
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_to_object"/><input class="tipical-button" style="line-height: normal;" type="submit" value="Принять" /></form>';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php
                            }
                        } ?>
                    </div>
                </div>
                <?php 
                if(!empty($_SESSION['user'])){
                    if($_SESSION['user']['id'] !== $object['createrUserID']){
                        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><!--<textarea class="tipical-textarea" name="description"></textarea>--><input class="tipical-button" style="line-height: normal;" type="submit" name="submitOrder" value="Откликнуться"/></form>';
                        else echo '<form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"><input type="submit" class="tipical-button" style="line-height: normal;" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div style="display: none;">
	<?php echo implode(' ', $object_imgs_arr_bg); ?>
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
                    <span>Заказчик:</span><br><?php echo $creater_user['surname'] .' '. $creater_user['name'] .' '. $creater_user['second_name']; ?>
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
    <div class="please-login"><a href="/registration/"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</a></div>
</div>
<div style="display: none;">
	<?php echo implode(' ', $object_imgs_arr_bg); ?>
</div>
<?php } ?>
