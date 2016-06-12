<?php
$user = $common_data['user'];
$DB = Application::$DB;

if(isset($_POST['changeStatus'])){
    if($_POST['changeStatus'] === '1') $newValue = '0';
    else $newValue = '1';
    $DB->prepare('UPDATE users SET "status"='. $newValue .' WHERE "id"='. $user['id'])->execute();
    $user['status'] = $newValue;
}elseif(isset($_POST['positive_negative'])){
    if($_POST['positive_negative'] === 'Плохо') $_POST['positive_negative'] = 'off';
    elseif($_POST['positive_negative'] === 'Хорошо') $_POST['positive_negative'] = 'on';
    $sql = $DB->prepare('
        INSERT INTO comments ("ownerUserID", "typeID", type, positive_description, negative_description, conclusion, positive_negative)
          VALUES(\''. $_POST['ownerUserID'] .'\', \''. $_POST['typeID'] .'\', \''. $_POST['type'] .'\', \''. $_POST['positive_description'] .'\', \''. $_POST['negative_description'] .'\', \''. $_POST['conclusion'] .'\', \''. $_POST['positive_negative'] .'\')
    ');
    $sql->execute();
}elseif(!empty($_FILES['avatar']['tmp_name'])){
    if(!file_exists("images/users/". $user['id'])) mkdir("images/users/". $user['id'], 0777);
    if(copy($_FILES['avatar']['tmp_name'], "images/users/". $user['id'] ."/". $_FILES['avatar']['name'])){
        $update_avatar = $DB->prepare('UPDATE users SET "avatar"=\''. $_FILES['avatar']['name'] .'\' WHERE "id"='. $user['id']);
        if($update_avatar->execute() === true){
            $error = 'Фотография загружена.';
            $_SESSION['user']['avatar'] = $_FILES['avatar']['name'];
            $user['avatar'] = $_FILES['avatar']['name'];
        }
        else $error = 'Не удалось загрузить фотография.';
    }
}elseif(!empty($_POST['about'])){
    $update_about = $DB->prepare('UPDATE users SET "description"=\''. $_POST['description'] .'\' WHERE "id"='. $user['id']);
    if($update_about->execute()){
        $_SESSION['user']['description'] = $_POST['description'];
        $user['description'] = $_POST['description'];
    }
}elseif(!empty($_POST['price_service'])){
    $sql = $DB->prepare('DELETE FROM users_prices WHERE "userID"='. $user['id']);
    $sql->execute();
    foreach($_POST['name'] as $key => $name){
        $sql = $DB->prepare('
            INSERT INTO users_prices (name, amount, value, "userID")
              VALUES(\''. $name .'\', \''. $_POST['amount'][$key] .'\', \''. $_POST['value'][$key] .'\', '. $user['id'] .')');
        $sql->execute();
    }
    
    if(!empty($_FILES['price_doc']['tmp_name'])){
        if(!file_exists("data/users/". $user['id'])) mkdir("data/users/". $user['id'], 0777);
        $filename = $_FILES['price_doc']['name'];
        if($_FILES['price_doc']['size'][$key] / 1000000 > 3){
            $error .= '<div style="color: red; font-weight: normal;">Не удалось загрузить файл '. $filename .', размер больше 3 Мб.</div>';
        }else{
            if(copy($_FILES['price_doc']['tmp_name'], "data/users/". $user['id'] ."/". $filename)){
                $update_avatar = $DB->prepare('UPDATE users SET "price_doc"=\''. $filename .'\' WHERE "id"='. $user['id']);
                if($update_avatar->execute() === true){
                    $error = 'Фотография загружена.';
                    $_SESSION['user']['price_doc'] = $filename;
                    $user['price_doc'] = $filename;
                }
                else $error = 'Не удалось загрузить фотография.';
            }
        }
    }
}elseif(!empty($_POST['del_price_doc'])){
    // удаляем прикрепленный файл к услугам и ценам
    $DB->prepare('UPDATE users SET "price_doc"=NULL WHERE "id"='. $user['id'])->execute();
    $_SESSION['user']['price_doc'] = NULL;
    $user['price_doc'] = NULL;
}

$prices_services = $DB->query('
    SELECT *
      FROM users_prices up
        WHERE up."userID"='. $user['id'])->fetchAll();

$professions = $DB->query('
    SELECT *
      FROM users_kinds_of_jobs ukj
      LEFT JOIN kinds_of_jobs kj ON ukj.kind_of_job_id = kj.id
        WHERE ukj."userID"='. $user['id'])->fetchAll();
$professions_str = [];
foreach($professions as $profession){
    $professions_str[] = $profession['name'];
}

$objects_images = $DB->query('
    SELECT *
        FROM objects o
        LEFT JOIN objects_imgs oi ON o."id" = oi."objectID"
          WHERE o."createrUserID"='. $user['id'])->fetchAll();

$imgs = [];
foreach($objects_images as $image){
    $imgs[] = '<img width="100px" src="/images/objects/'. $image['objectID'] .'/'. $image['src'] .'" />';
}

$countOfViews = $DB->query('SELECT COUNT(DISTINCT(session_id)) FROM logs WHERE url=\'/users/'. $user['id'] .'/\'')->fetch();

$my_works_query = $DB->query('
    SELECT r.*
      FROM (SELECT DISTINCT ON (o.id) o.id, o.*, oi.src FROM objects o LEFT JOIN objects_imgs oi ON o.id = oi."objectID" ORDER BY o.id, oi.main DESC) as r
        WHERE r.src IS NOT NULL AND
              r."createrUserID"='. $user['id'] .' AND
              r."type_of_kind"=2 AND
              r."status" <> \'archive\'')->fetchAll();
$my_works = [];
foreach($my_works_query as $my_work){
    if(empty($my_work['src'])) continue;
    $my_works[] = '<a href="#portfo" data-objectid="'. $my_work['id'] .'" class="portfo-item modal_on"><img width="100px" height="100px" src="/images/objects/'. $my_work['id'] .'/'. $my_work['src'] .'"/></a>';
}
/*
if($common_data['check_owner']) echo '<h1>Мой поспорт</h1>';
else echo '<h1>Страница пользователя</h1>';*/
?>
<!--
<div style="width: 200px; height: 200px; border: 1px solid black;">
    <?php //if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>' ?>
</div>
<?php //if($common_data['check_owner']){ ?>
<br/>
<a href="/users/<?php //echo $user['id']; ?>/my_settings/">Загрузить фотографию</a>
<br/>
<a href="#">Написать сообщение</a>
<br/>
<a href="/users/<?php //echo $user['id']; ?>/my_settings">изменить личные данные</a>
<br/>
<?php //} ?>
<br/>
<div style="width: 1000px; height: 300px; border: 1px solid black;">
    <span><?php //echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></span>
    <?php //if($common_data['check_owner']){ ?>
    <br/>
    <form method="POST"><span><?php //if($user['status'] === '1') echo 'занят'; else echo 'свободен'; ?></span><input type="hidden" value="<?php echo $user['status']; ?>" name="changeStatus"/> <input type="submit" value="поменять статус"/></form>
    <?php //} ?>
    <br/><br/>
    <div><?php //echo $user['age']; ?>  года</div>
    <div>Стаж работы: <?php //echo $user['experience']; ?> лет</div>
    <div>Место работы: <?php //echo $user['work_city']; ?></div>
    <br/>
    <?php
        
    ?>
    
    <div>Специализации: <?php //echo implode(', ', $professions_str); ?></div>
</div>
-->
<?php if($user['type_of_registration'] === 2){ ?>
<div class="content">
    <div class="my-page-content clearfix">
        <?php if($common_data['check_owner']) echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Паспорт организации</a>
                    </li>
                </ul>
            </div>

            <div class="my-page-wrapper-content clearfix">

                <div class="company-passport-left">
                    <a href="#add_my_photo" class="passport-avatar modal_on">
                        <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>'; else echo '<img src="/images/img1.jpg">'; ?>
                    </a>
                    <?php if($common_data['check_owner']){ ?>
                    <a href="#add_my_photo" class="tipical-button modal_on">Сделать фото</a>

                    <div style="display: none;">
                        <div id="add_my_photo" style="width: 684px;">
                            <form class="modal_add_my_photo" method="POST" enctype="multipart/form-data">
                                <fieldset>
                                    <div class="first-view">
                                        <div class="modal-title">Загрузить фотографию</div>
                                        <div class="add_photo_modal_text">
                                            <br>
                                            <p>Вы можете загрузить фотографию с вашего компьютера или сделать при помощи веб-камеры.</p>
                                            <br>
                                            <p>Допустимые форматы: <span class="semi-red">jpg, jpeg, png.</span></p>
                                            <p>Ограничение по размеру: <span class="semi-red">3 Мб.</span></p>
                                        </div>
                                        <div class="add_photo_modal_buttons clearfix">
                                            <div class="file_upload">
                                                <button type="button" class="tipical-button">Загрузить фото</button>
                                                <input type="file" name="avatar">
                                            </div>
                                            <a href="#" class="tipical-button">Фото с веб-камеры</a>
                                        </div>
                                    </div>
                                    <!-- Ниже идёт вид после загрузки фотографии -->
                                    <div class="add_photo_modal_photo">
                                        <div class="modal-title">Ваша фотография</div>
                                        <div class="add_photo_modal_img">
                                            <img src="">
                                            <div class="add_photo_modal_imgtext">
                                                Так будет выглядеть ваша фотография на сайте. Нажмите “Сохранить” и ваша страница обновится.
                                            </div>
                                        </div>
                                        <input type="submit" class="tipical-button" value="Сохранить"> 
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                    <div class="file_upload">
                        <a href="#add_my_photo" class="tipical-button modal_on">Загрузить с компьютера</a>
                    </div>
                    <?php } ?>
                    <?php if(!$common_data['check_owner'] && !empty($_SESSION['user'])){ ?>
                    <a href="<?php echo '/users/'. $user['id'] .'/my_messages/dialogs/'. $user['id'] .'/'; ?>" class="tipical-button">Написать сообщение</a>
                    <?php } ?>
                </div>
                <div class="company-passport-right">
                    <div class="company-passport-title"><?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></div>
                    <div class="company-passport-content">
                        <?php if(!empty($user['name'])){ ?><p><b>Руководитель:</b> <?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></p><?php } ?>
                        <?php if(!empty($professions_str)){ ?><p><b>Виды деятельности:</b> <?php echo implode(', ', $professions_str); ?></p><?php } ?>
                        <br>
                        <?php if(!empty($user['age'])){ ?><p><b>Стаж работы:</b> <?php echo $user['age']; ?> лет</p><?php } ?>
                        <?php if(!empty($user['city_name'])){ ?><p><b>Место работы:</b> г. <?php echo $user['city_name']; ?></p><?php } ?>
                        <br>
                        <p>Наличие СРО и сертификатов:
			<?php if($user['cpo']) echo 'СРО есть'; ?>
			</p>
			<br>
                        <?php if(!empty($_SESSION['user'])){ ?>
                        <p><span style="color: #054157;">Контактная информация:</span><br>
                        <?php echo $user['adress_of_organization']; ?><br>
                        <?php echo $user['phone']; ?></p>
                        <?php } ?>
                    </div>
                    <div class="company-passport-map">
                        <!-- !!сюда вставить карту!! -->
                    </div>


                    <!-- Понятия не имею на какую кнопку должны вызываться эти окна. Поэтому тупо добавил эти ссылки -->
                    <!--
                    <br><br><br><br>
                    <a href="#write-message" class="modal_on">Написать быстрое сообщение</a>
                    <br>
                    <a href="#conditions" class="modal_on">Условия использования ресурса</a>
                    <br>
                    <a href="#user-login" class="modal_on">Авторизация</a>
                    <br>
                    <a href="#cancel-application" class="modal_on">Отмена заявки</a>
                    <br><br><br><br>
                    -->
                </div>

            </div>

        </div>
    </div>
</div>
<?php }else{ ?>
<!--Страница неавторезированного пользователя-->
<?php if(empty($_SESSION['user'])){ ?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="/">Главная</a>
            </li>
            <li>
                <a href="/masters/">Исполнители</a>
            </li>
            <li>
                <a href="/masters/?cityID=<?php echo $user['cityID']; ?>&search=true"><?php echo $user['city_name']; ?></a>
            </li>
            <li>
                <a href="#"><?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></a>
            </li>
        </ul>
    </div>
    <div class="columns-holder clearfix">
        <div class="column-product-item">
            <div class="specialist-holder clearfix">
                <a href="#" class="specialist-avatar">
                    <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>'; else echo '<img src="/images/img1.jpg">'; ?>
                </a>
                <div class="specialist-meta">
                    <a href="#" class="specialist-name">
                        <?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?>
                        <span class="valid">(проверено)</span>
                        <span class="views"><b>Просмотров:</b> <?php echo $countOfViews['count']; ?></span>
                    </a>
                    <p style="color: #054157;"><b><?php if($user['status'] === '1') echo 'занят'; else echo 'свободен'; ?></b></p>
                    <br>
                    <div class="specialist-personal">
                        <?php if(!empty($user['city_name'])){ ?><p><b>Место работы:</b> г. <?php echo $user['city_name']; ?></p><?php } ?>
                        <p><b>На сайте:</b> <?php echo ceil((strtotime("now") - strtotime($user['created'])) / 60 / 60 / 24) . ' день(ней)'; ?></p>
                        <?php if(!empty($user['experience'])){ ?><p><b>Стаж работы:</b> <?php echo $user['experience']; ?> лет</p><?php } ?>
                        <?php if(!empty($user['age'])){ ?><p><b>Возраст:</b> <?php echo $user['age']; ?> года</p><?php } ?>
                        <?php if(!empty($_SESSION['user'])){ ?>
                        <?php if(!empty($user['phone'])){ ?><p><b>Тел.</b> <?php echo $user['phone']; ?></p><?php } ?>
                        <?php } ?>
                        <?php if(!empty($professions_str)){ ?>
                        <p><b>Виды деятельности:</b></p>
                        <?php echo '<p>'. implode('</p><p>', $professions_str) .'</p>'; ?>
                        <?php } ?>
                    </div>
                </div>
                <span class="star-master <!--active-->"></span>
                <!--<span class="last-time">Был 5 часов 11 минут назад</span>-->
            </div>
            <?php if(!empty($user['description'])){ ?>
            <div class="product-sub-headline">О себе</div>
            <?php echo $user['description']; ?>
            <br>
            <?php } ?>
            <?php if(!empty($my_works)){ ?>
            <div class="product-sub-headline">Фото работ</div>
            <div class="specialist-meta-block">
                <div class="portfo-holder clearfix">
                    <?php echo implode('', $my_works); ?>
                </div>
            </div>
            <?php } ?>
            <?php if(!empty($prices_services)){ ?>
            <div class="product-sub-headline">Цены на услуги</div>
            <?php foreach($prices_services as $price_service){ ?>
            <p><?php echo $price_service['name']; ?>......................от <?php echo $price_service['amount']; ?> р/<?php echo $price_service['value']; ?></p>
            <?php } ?>
            <?php } ?>
            <?php
            $comments = $DB->query('
                SELECT c.*,
                       j.name as type_name,
                       \'jobs\' as href_name,
                       u.name as user_name
                  FROM jobs j
                  LEFT JOIN comments c ON j.id = c."typeID"
                  LEFT JOIN users u ON u.id = c."ownerUserID"
                    WHERE j."workerID" = '. $user['id'] .' AND c.type = \'job_comment\'
                UNION ALL
                SELECT c.*,
                       o.name as type_name,
                       \'objects\' as href_name,
                       u.name as user_name
                  FROM objects o
                  LEFT JOIN comments c ON o.id = c."typeID"
                  LEFT JOIN users u ON u.id = c."ownerUserID"
                    WHERE o."workerID" = '. $user['id'] .' AND c.type = \'object_comment\'
                UNION ALL
                SELECT c.*,
                       u.name as type_name,
                       \'users\' as href_name,
                       u_new.name as user_name
                  FROM users u
                  LEFT JOIN comments c ON u.id = c."typeID"
                  LEFT JOIN users u_new ON u_new.id = c."ownerUserID"
                    WHERE u."id" = '. $user['id'] .' AND c.type = \'user_comment\'
            ')->fetchAll();
            if(!empty($comments)){
            ?>
            <div class="product-sub-headline">Отзывы</div>
            <?php
            foreach($comments as $comment){
            ?>
            <div class="specialist-feedback">
                <div class="specialist-feedback-headline"><?php if($comment['type'] === 'object_comment') echo 'По заказу'; elseif($comment['type'] === 'job_comment') echo 'По вакансии'; ?> <a href="<?php echo '/'. $comment['href_name'] .'/'. $comment['typeID'] .'/' ?>"><?php echo $comment['type_name']; ?></a></div>
                <p><b>Что понравилось</b><br>
                <?php echo $comment['negative_description']; ?>
                </p>
                <br>
                <p><b>Что не понравилось</b><br>
                <?php echo $comment['positive_description']; ?>
                </p>
                <br>
                <p><b>Общие выводы</b><br>
                <?php echo $comment['conclusion']; ?>
                </p>
                <a href="<?php echo '/'. $comment['href_name'] .'/'. $comment['typeID'] .'/' ?>" class="feedback-author"><?php echo $comment['type_name']; ?>, <?php echo date('m.Y', strtotime($comment['created'])); ?></a>
            </div>
            <?php } ?>
            <div class="show-more-holder">
                <a href="#" class="show-more">Смотреть ещё отзывы</a>
            </div>
            <?php } ?>
        </div>  
    </div>
</div>
<?php }else{ ?>
<!-- Своя страница -->
<div class="content">
    <div class="my-page-content clearfix">
        <?php echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Мой паспорт</a>
                    </li>
                </ul>
            </div>
            <div class="my-page-wrapper-content">
                <div class="passport-main-holder clearfix">
                    <div class="passport-main-left">
                        <a href="#add_my_photo" class="passport-avatar modal_on">
                            <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>'; else echo '<img src="/images/img1.jpg">'; ?>
                        </a>
                        <?php if($common_data['check_owner']){ ?>
                        <a href="#add_my_photo" class="tipical-button modal_on">Сделать фото</a>

                        <div style="display: none;">
                            <div id="add_my_photo" style="width: 684px;">
                                <form class="modal_add_my_photo" method="POST" enctype="multipart/form-data">
                                    <fieldset>
                                        <div class="first-view">
                                            <div class="modal-title">Загрузить фотографию</div>
                                            <div class="add_photo_modal_text">
                                                <br>
                                                <p>Вы можете загрузить фотографию с вашего компьютера или сделать при помощи веб-камеры.</p>
                                                <br>
                                                <p>Допустимые форматы: <span class="semi-red">jpg, jpeg, png.</span></p>
                                                <p>Ограничение по размеру: <span class="semi-red">3 Мб.</span></p>
                                            </div>
                                            <div class="add_photo_modal_buttons clearfix">
                                                <div class="file_upload">
                                                    <button type="button" class="tipical-button">Загрузить фото</button>
                                                    <input type="file" id="ava-files" name="avatar" multiple>
                                                </div>
                                                <a href="#" class="tipical-button">Фото с веб-камеры</a>
                                            </div>
                                        </div>
                                        <!-- Ниже идёт вид после загрузки фотографии -->
                                        <div class="add_photo_modal_photo">
                                            <!-- <div class="modal-title">Ваша фотография</div> -->
                                            <div class="add_photo_modal_img">
                                                <output id="ava-photo"></output>
                                            </div>
                                            <input style="display: none;" type="submit" id="add_photo_save" class="tipical-button" value="Сохранить"> 
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <div class="file_upload">
                            <a href="#add_my_photo" class="tipical-button modal_on">Загрузить с компьютера</a>
                        </div>
                        <?php } ?>
                        <?php if(!$common_data['check_owner']){ ?>
                        <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $user['id'] .'/'; ?>" class="tipical-button">Написать сообщение</a><?php } ?>
                    </div>
                    <div class="passport-main-right">
                        <div class="specialist-holder clearfix">
                            <div class="specialist-meta">
                                <a href="#" class="specialist-name">
                                    <?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?>
                                    <span class="valid">(проверено)</span>
                                    <span class="views"><b>Просмотров:</b> <?php echo $countOfViews['count']; ?></span>
                                </a>
                                <?php if($common_data['check_owner']){ ?><form method="POST"><p style="color: #054157;"><b><?php if($user['status'] === '1') echo 'занят'; else echo 'свободен'; ?></b><input type="hidden" value="<?php echo $user['status']; ?>" name="changeStatus"/> <input class="change-status" type="submit" value="изменить статус"/></form><?php } ?>
                                <br>
                                <div class="specialist-personal">
                                    <?php if(!empty($user['city_name'])){ ?><p><b>Место работы:</b> г. <?php echo $user['city_name']; ?></p><?php } ?>
                                    <p><b>На сайте:</b> <?php echo ceil((strtotime("now") - strtotime($user['created'])) / 60 / 60 / 24) . ' день(ней)'; ?></p>
                                    <?php if(!empty($user['experience'])){ ?><p><b>Стаж работы:</b> <?php echo $user['experience']; ?> лет</p><?php } ?>
                                    <?php if(!empty($user['age'])){ ?><p><b>Возраст:</b> <?php echo $user['age']; ?> года</p><?php } ?>
                                    <?php if(!empty($_SESSION['user'])){ ?>
                                    <?php if(!empty($user['phone'])){ ?><p><b>Тел.</b> <?php echo $user['phone']; ?></p><?php } ?>
                                    <?php } ?>
                                    <?php if(!empty($professions_str)){ ?>
                                    <p><b>Виды деятельности:</b></p>
                                    <?php echo '<p>'. implode('</p><p>', $professions_str) .'</p>'; ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if($common_data['check_owner']){ ?>
                                <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="change-personal-data">Изменить личные данные</a>
                                
                            <?php } ?>
                        </div>
                        <!--
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>Избранное портфолио</span>
                                <?php if($common_data['check_owner']){ ?><a href="/users/<?php echo $user['id']; ?>/my_works/" class="tipical-button">Добавить</a><?php } ?>
                            </div>
                            <div class="photo-carousel-standart">
                                <div id="jssor_1" class="rotator-holder">
                                    <!-- Loading Screen --><!--
                                    <div data-u="loading" class="rotator-inner">
                                        <div class="rotator-inner-block"></div>
                                        <div class="rotator-inner-load"></div>
                                    </div>
                                    <div data-u="slides" class="rotator-content">
                                        <div style="display: none;">
-                                            <a href="" rel="photo_group">
-                                                <?php echo implode('</div><div style="display: none;">', $my_works); ?>
-                                            </a>
-                                        </div>
                                    </div>
                                    <!-- Bullet Navigator --><!--
                                    <div data-u="navigator" class="jssorb03" style="bottom:10px;right:10px;">
                                        <!-- bullet navigator item prototype --><!--
                                        <div data-u="prototype" style="width:21px;height:21px;">
                                            <div data-u="numbertemplate"></div>
                                        </div>
                                    </div>
                                    <!-- Arrow Navigator --><!--
                                    <span data-u="arrowleft" class="jssora03l" style="top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
                                    <span data-u="arrowright" class="jssora03r" style="top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
                                </div>
                            </div>
                        </div>
                        -->
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>О себе</span>
                                <?php if($common_data['check_owner']){ ?><a href="#user-about" class="modal_on tipical-button">Редактировать</a><?php } ?>
                                <!-- <a href="#" class="tipical-button">Редактировать</a> -->
                            </div>
                            <?php echo $user['description']; ?>
                        </div>
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>Услуги и цены</span>
                                <?php if($common_data['check_owner']){ ?><a href="#prices" class="modal_on tipical-button">Добавить</a><?php } ?>
                            </div>
                            <?php foreach($prices_services as $price_service){ ?>
                            <p><?php echo $price_service['name']; ?>......................от <?php echo $price_service['amount']; ?> р/<?php echo $price_service['value']; ?></p>
                            <?php } ?>
                            <a download="" target="_blank" type="application/file" href="/data/users/<?php echo $user['id']; ?>/<?php echo $user['price_doc']; ?>"><?php echo $user['price_doc']; ?></a>
                            <?php if($common_data['check_owner'] && !empty($user['price_doc'])){ ?><form method="POST"><input type="submit" name="del_price_doc" value="Удалить"/></form><?php } ?>
                        </div>
                    </div>
                </div>
                <?php if(!empty($_SESSION['user']) && $_SESSION['user']['id'] !== $user['id']){ ?>
                <form method="POST">
                    <input type="hidden" name="ownerUserID" value="<?php echo $_SESSION['user']['id']; ?>" />
                    <input type="hidden" name="typeID" value="<?php echo $user['id']; ?>" />
                    <input type="hidden" name="type" value="user_comment" />
                    <div class="specialist-meta-block">
                        <div class="specialist-block-title">
                            <span>Оставьте отзыв</span>
                        </div>
                        <p><b>Внимание!</b><br>
                        Этот мастер работал на вашем объекте <a href="#">«Отделка квартиры»</a>. Вы можете оставить отзыв.</p>
                        <div class="feedback-field">
                            <div class="feedback-field-headline">Что понравилось?</div>
                            <textarea class="tipical-textarea" name="positive_description"></textarea>
                        </div>
                        <div class="feedback-field">
                            <div class="feedback-field-headline">Что не понравилось?</div>
                            <textarea class="tipical-textarea" name="negative_description"></textarea>
                        </div>
                        <div class="feedback-field">
                            <div class="feedback-field-headline">Выводы</div>
                            <textarea class="tipical-textarea" name="conclusion"></textarea>
                        </div>
                        <div class="feedback-evaluation clearfix">
                            <span>Оценка:</span>
                            <input class="tipical-button good" type="submit" value="Хорошо" name="positive_negative" />
                            <input class="tipical-button bad" type="submit" value="Плохо" name="positive_negative" />
                        </div>
                    </div>
                </form>
                <?php } ?>

                <div class="specialist-meta-block">
                    <div class="specialist-block-title">
                        <span>Избранное портфолио</span>
                        <?php if($common_data['check_owner']){ ?><a href="/users/<?php echo $user['id']; ?>/my_works/" class="tipical-button">Добавить</a><?php } ?>
                    </div>
                    <div class="portfo-holder clearfix">
                        <?php echo implode('', $my_works); ?>
                    </div>
                </div>

                <div class="specialist-meta-block">
                    <div class="specialist-block-title">
                        <span>Отзывы</span>
                    </div>
                    <?php
                    $positive_negative = $DB->query('
                        SELECT r.* FROM (
                            SELECT c.id, c.positive_negative
                              FROM jobs j
                              LEFT JOIN comments c ON j.id = c."typeID"
                              LEFT JOIN users u ON u.id = c."ownerUserID"
                                WHERE j."workerID" = '. $user['id'] .' AND c.type = \'job_comment\'
                            UNION ALL
                            SELECT c.id, c.positive_negative
                              FROM objects o
                              LEFT JOIN comments c ON o.id = c."typeID"
                              LEFT JOIN users u ON u.id = c."ownerUserID"
                                WHERE o."workerID" = '. $user['id'] .' AND c.type = \'object_comment\'
                            UNION ALL
                            SELECT c.id, c.positive_negative
                              FROM comments c
                                WHERE c."typeID" = '. $user['id'] .' AND c.type = \'user_comment\'
                        ) as r
                    ')->fetchAll();
                    
                    $positive = 0;
                    $negative = 0;
                    foreach($positive_negative as $comment){
                        if($comment['positive_negative'] === true) $positive++;
                        else $negative++;
                    }
                    
                    echo '<div class="feedback-counter clearfix">
                        <a href="#">'. $positive .' положительных</a>
                        <span>|</span>
                        <a href="#">'. $negative .' отрицательных</a>
                    </div>';
                    
                    $comments = $DB->query('
                        SELECT c.*,
                               j.name as type_name,
                               \'jobs\' as href_name,
                               u.name as user_name
                          FROM jobs j
                          LEFT JOIN comments c ON j.id = c."typeID"
                          LEFT JOIN users u ON u.id = c."ownerUserID"
                            WHERE j."workerID" = '. $user['id'] .' AND c.type = \'job_comment\'
                        UNION ALL
                        SELECT c.*,
                               o.name as type_name,
                               \'objects\' as href_name,
                               u.name as user_name
                          FROM objects o
                          LEFT JOIN comments c ON o.id = c."typeID"
                          LEFT JOIN users u ON u.id = c."ownerUserID"
                            WHERE o."workerID" = '. $user['id'] .' AND c.type = \'object_comment\'
                        UNION ALL
                        SELECT c.*,
                               u.name as type_name,
                               \'users\' as href_name,
                               u_new.name as user_name
                          FROM users u
                          LEFT JOIN comments c ON u.id = c."typeID"
                          LEFT JOIN users u_new ON u_new.id = c."ownerUserID"
                            WHERE u."id" = '. $user['id'] .' AND c.type = \'user_comment\'
                    ')->fetchAll();
                    foreach($comments as $comment){
                    ?>
                    <div class="feedback-passport-item">
                        <?php if($comment['type'] === 'object_comment') echo 'По заказу'; elseif($comment['type'] === 'job_comment') echo 'По вакансии'; ?> <a href="<?php echo '/'. $comment['href_name'] .'/'. $comment['typeID'] .'/' ?>"><?php echo $comment['type_name']; ?></a>
                        <p><b>Что понравилось</b><br>
                        <?php echo $comment['positive_description']; ?>
                        </p>
                        <br>
                        <p><b>Что не понравилось</b><br>
                        <?php echo $comment['negative_description']; ?>
                        </p>
                        <br>
                        <p><b>Общие выводы</b><br>
                        <?php echo $comment['conclusion']; ?>
                        </p>
                        <a href="<?php echo '/'. $comment['href_name'] .'/'. $comment['typeID'] .'/' ?>" class="feedback-author"><?php echo $comment['type_name']; ?>, <?php echo date('m.Y', strtotime($comment['created'])); ?></a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="user-about" style="width: 620px;">
        <div class="modal-title">Информация о себе</div>
        <form class="user-about-form" method="POST">
            <fieldset>
                <p>Пояснительный текст</p>
                <br>
                <textarea class="tipical-textarea" name="description"><?php echo $user['description']; ?></textarea>
                <input type="submit" value="Отправить" name="about" class="tipical-button">
            </fieldset>
        </form>
    </div>
</div>

<div style="display: none;">
    <div id="prices" style="width: 728px;">
        <div class="modal-title">Услуги и цены</div>
        <form class="user-about-form clearfix" method="POST" enctype="multipart/form-data">
            <fieldset>
                <p>Добавьте услуги или прикрепите файл.</p>
                <br>
                <div class="add-price-table">
                    <div class="add-price-table-row clearfix">
                        <div class="add-price-name">
                            <span>Наименование услуги</span><br>
                            Введите наименование своей услуги
                        </div>
                        <div class="add-price-price">
                            <span>Цена</span><br>
                            Стоимость в рублях.
                        </div>
                        <div class="add-price-value">
                            <span>Единица измерения</span><br>
                            Например, «м2»
                        </div>
                    </div>
                    <?php foreach($prices_services as $price_service){ ?>
                    <div class="add-price-table-row clearfix">
                        <div class="add-price-name">
                            <input type="text" value="<?php echo $price_service['name']; ?>" name="name[]">
                        </div>
                        <div class="add-price-price">
                            <input type="text" value="<?php echo $price_service['amount']; ?>" name="amount[]">
                        </div>
                        <div class="add-price-value">
                            <input type="text" value="<?php echo $price_service['value']; ?>" name="value[]">
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <a href="#" id="add-pricerow" class="tipical-button">Добавить строку</a>
                <div class="attach-fileblock">
                    <p class="attach-fileblock-title">
                        <span>Прикрепить файл</span><br>
                        Вы можете прикрепить свой файл с ценами.
                    </p>
                    <p class="attach-fileblock-format">
                        Допустимые форматы файла: <span>exel.word.docs</span>
                        <br>
                        Ограничение по размеру: <span>3 Мб</span>
                    </p>
                </div>
                <div class="file_upload att-file">
                    <button type="button" class="tipical-button">Выбрать файл</button>
                    <input id="name-files" type="file" name="price_doc">
                </div>
                <output id="names-list" class="names-list"></output>
                <input type="submit" class="tipical-button" name="price_service" value="Сохранить услуги и цены">
            </fieldset>
        </form>
    </div>
</div>
<?php } ?>
<?php } ?>

<div style="display: none;">
    <div id="portfo" style="width: 750px;">
        <div class="modal-subtitle">Ремонт на балконе</div>
        <div class="big-portfo-photo">
            <img src="" id="big-portfo-photo">
            <a href="#" class="portfo-nav left"></a>
            <a href="#" class="portfo-nav right"></a>
        </div>

        <!-- Эта часть видна только владельцу страницы -->
        <?php if($common_data['check_owner']){ ?>
        <div class="make-main-holder">
            <a href="#">Сделать фотографию основной</a>
            <p>Фотография станет обложкой вашей работы.</p>
        </div>
        <?php } ?>
        <!-- Эта часть видна только владельцу страницы и если текущая фотка уже выбрана основной -->
        <div class="make-main-holder">
            <b>Это главная фотография</b>
            <p>Она является обложкой вашей работы.</p>
        </div>

        <div class="portfo-thumbs-holder clearfix">
            
        </div>
        <div class="portfo-info">
            <p><b>Год сдачи:</b> </p>
            <p><b>Срок работы:</b> </p>
            <p><b>Стоимость:</b> </p>
            <br>
            <!--<p>Комментарий пользователя. Что сделано: разводка сантехники,подключение сантехники,плиточные работы,тёплый пол</p>-->
        </div>
    </div>
</div>
