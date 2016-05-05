<?php

$job = $common_data['job'];
$DB = Application::$DB;
$applicationURL = Application::$URL;
$checkSubmitUser = false;

if(isset($_SESSION['user'])){
    $checkSubmitUser = $DB->query('SELECT uj.* FROM users_jobs uj WHERE uj."fromUserID"='. $_SESSION['user']['id'] .' AND uj."jobID" = '. $job['id'])->fetchAll();
    if(!empty($checkSubmitUser)) $checkSubmitUser = true;
    $check_owner = ($_SESSION['user']['id'] === $job['createrUserID']) ? true : false;
}

// обработаем POST - например при подписывании на объект
if((isset($_POST['submitOrder']) || isset($_POST['unsubmitOrder'])) && isset($_SESSION['user'])){
    if(isset($_POST['submitOrder'])){
        if(empty($_POST['description'])) $_POST['description'] = '';
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
        $DB->prepare('
            INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
            ('. $_SESSION['user']['id'] .', \'Вы назначены исполнителем на вакансию № '. $applicationURL[2] .'.\', '. $_POST['user_to_job'] .', \'system_job\', '. $applicationURL[2] .')
        ')->execute();
    }
}elseif(isset($_POST['user_remove_job']) && !empty($_SESSION['user'])){
    $update_job = $DB->prepare('UPDATE jobs SET "workerID"=NULL WHERE "id"='. $applicationURL[2]);
    if($update_job->execute() === true){
        $job['workerID'] = NULL;
        $DB->prepare('
            INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
            ('. $_SESSION['user']['id'] .', \'Вы сняты с вакансии № '. $applicationURL[2] .'.\', '. $_POST['user_remove_job'] .', \'system_job\', '. $applicationURL[2] .')
        ')->execute();
    }
}

$creater_user = $DB->query('SELECT u.* FROM users u WHERE u."id"='. $job['createrUserID'])->fetch();
$worker_user = null;
if(!empty($job['workerID'])) $worker_user = $DB->query('SELECT u.* FROM users u WHERE u."id"='. $job['workerID'])->fetch();
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
    /*
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
    */
?>

<?php
// помещаем в архив
if(!empty($applicationURL['3']) && $applicationURL['3'] === 'close' && $check_owner){
    $update_job = $DB->prepare('UPDATE jobs SET "status"=\'archive\' WHERE "id"='. $applicationURL[2])->execute();
    echo '<meta http-equiv="refresh" content="1;URL=/users/'. $_SESSION['user']['id'] .'/my_objects/">';
}
?>

<?php if(!empty($_SESSION['user'])){ ?>
<div class="content">
    <div class="my-page-content clearfix">
        <?php echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_objects/'; ?>">Объекты и вакансии</a>
                    </li>
                    <li>
                        <a href="#"><?php echo $job['name']; ?></a>
                    </li>
                </ul>
            </div>
            <div class="product-holder">
                <div class="product-title"><?php echo $job['name']; ?></div>
                <?php if($check_owner){ ?>
                <div class="product-holder-control">
                    <a href="<?php echo '/jobs/'. $job['id'] .'/edit/'; ?>">Редактировать</a>
                    <a href="<?php echo '/jobs/'. $job['id'] .'/close/'; ?>">Закрыть</a>
                </div>
                <?php } ?>
                <div class="product-meta">
                    <p>Номер вакансии: <b><?php echo $job['id']; ?></b></p>
                    <div class="product-customer clearfix">
                        <div class="product-customer-left">
                            <span>Заказчик:</span><br><?php echo $creater_user['surname'] .' '. $creater_user['name'] .' '. $creater_user['second_name']; ?>
                        </div>
                        <div class="product-customer-right">
                            Бюджет: <?php echo $job['amount']; ?> руб.
                        </div>
                    </div>
                    <p><b>Город:</b> Воронеж</p>
                    <p><b>Сфера деятельности:</b> <?php echo implode(', ', $kinds_of_jobs_arr); ?></p>
                    <p><b>График работы:</b> <?php echo $job['s_name']; ?></p>
                </div>
                <div class="product-sub-meta">
                    <div class="product-sub-meta-item">Требования:<br>
                    <?php echo $job['require'];?>
                    </div>
                    <div class="product-sub-meta-item">Обязанности:<br>
                    <?php echo $job['description'];?>
                    </div>
                    <div class="product-sub-meta-item">Условия:<br>
                    <?php echo $job['conditions'];?>
                    </div>
                    <div class="product-theme">
                        <?php if($check_owner){ ?>
                        <div class="product-theme-headline">
                            <span>Ответы</span>
                        </div>
                        <?php if(!empty($answers)){
                            foreach($answers as $answer){
                        ?>
                        <div class="feedback-item">
                            <div class="feedback-item-body clearfix">
                                <div class="feedback-item-avatar">
                                    <a href="<?php echo '/users/'. $answer['id'] .'/'; ?>">
                                        <img src="<?php echo '/images/users/'. $answer['id'] .'/'. $answer['avatar']; ?>">
                                    </a>
                                </div>
                                <div class="feedback-item-content clearfix">
                                    <div class="feedback-item-content-left">
                                        <div class="feedback-name">
                                            <a href="<?php echo '/users/'. $answer['id'] .'/'; ?>"><span><?php echo $answer['surname'] .' '. $answer['name'] .' '. $answer['second_name']; ?></span><br>Частный мастер</a>
                                        </div>
                                        <div class="feedback-text">
                                        <?php echo $answer['uj_description']; ?>
                                        </div>
                                        <?php if($_SESSION['user']['id'] !== $answer['id']) echo '<a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $answer['id'] .'/" class="feedback-candidate">Написать кандидату</a>'; ?>
                                    </div>
                                    <div class="feedback-item-content-right">
                                        <div class="feedback-item-date"><?php echo date('j.m.Y H:i:s', strtotime($answer['uj_created'])); ?></div>
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
                                        if($_SESSION['user']['id'] === $job['createrUserID']){
                                            if((int)$job['workerID'] === $answer['id'])
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_remove_job"/><input class="tipical-button" style="line-height: normal;" type="submit" value="Отказаться" /></form>';
                                            elseif(empty($object['workerID']))
                                                echo '<form method="POST"><input type="hidden" value="'. $answer['id'] .'" name="user_to_job"/><input class="tipical-button" style="line-height: normal;" type="submit" value="Принять" /></form>';
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
                    if($_SESSION['user']['id'] !== $job['createrUserID']){
                        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><!--<textarea class="tipical-textarea" name="description"></textarea>--><input class="tipical-button" style="line-height: normal;" type="submit" name="submitOrder" value="Откликнуться"/></form>';
                        else echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><input type="submit" class="tipical-button" style="line-height: normal;" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php }else{ ?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="/">Главная</a>
            </li>
            <li>
                <a href="/jobs/">Вакансии</a>
            </li>
            <li>
                <a href=""><?php echo $job['name']; ?></a>
            </li>
        </ul>
    </div>
    <div class="product-holder">
        <div class="product-title"><?php echo $job['name']; ?></div>
        <?php
            /*if(!empty($worker_user)){
                echo 'Исполнитель: '. $worker_user['name'] .' '. $worker_user['surname'];
                if(!empty($_SESSION['user']))
                    echo '<br/><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $worker_user['id'] .'/">написать исполнителю</a>';
            }else{
                echo 'Исполнитель не назначен.';
            }*/
        ?>
        <div class="product-meta">
            <p>Опубликовано: <?php echo date('j.m.Y H:i:s', strtotime($job['created'])); ?></p>
            <p>Город: <?php echo $job['street'] .' '. $job['house']; ?></p>
            <p>Сфера деятельности: <?php echo implode(', ', $kinds_of_jobs_arr); ?></p>
            <p>График работы: <?php echo $job['s_name']; ?></p>
            <p>Требуемый опыт работы: <?php echo $job['require'];?></p>
            <p>Работодатель: <?php echo $creater_user['surname'] .' '. $creater_user['name'] .' '. $creater_user['second_name']; ?></p>
            <?php if(!empty($_SESSION['user'])){ ?>
            <p>тел. +8 987 456 45 45</p>
            <?php } ?>
        </div>
        <div class="product-sub-meta">
            <div class="product-sub-meta-item">Требования:<br><?php echo $job['require'];?></div>
            <div class="product-sub-meta-item">Обязанности:<br><?php echo $job['description'];?></div>
            <div class="product-sub-meta-item">Условия:<br><?php echo $job['conditions'];?></div>
        </div>
    </div>
    <div class="please-login"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</div>
</div>
<?php } ?>
<?php
/* if(!empty($_SESSION['user']) && empty($job['workerID'])){
    if($_SESSION['user']['id'] !== $job['createrUserID']){
        if(empty($checkSubmitUser)) echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><textarea name="description"></textarea><br/><input type="submit" name="submitOrder" value="Откликнуться"/></form>';
        else echo '<form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"><input type="submit" name="unsubmitOrder" value="Отказаться от выполнения"/></form>';
    }
}*/
?>
<?php /*if(!empty($worker_user)){
        echo 'Исполнитель: '. $worker_user['name'] .' '. $worker_user['surname'];
        if(!empty($_SESSION['user']))
            echo '<br/><a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $worker_user['id'] .'/">написать исполнителю</a>';
    }else{
        echo 'Исполнитель не назначен.';
    }*/
?>