<?php
$user = $common_data['user'];
$DB = Application::$DB;

if(isset($_POST['changeStatus'])){
    if($_POST['changeStatus'] === '1') $newValue = '0';
    else $newValue = '1';
    $DB->prepare('UPDATE users SET "status"='. $newValue .' WHERE "id"='. $user['id'])->execute();
    $user['status'] = $newValue;
}elseif(isset($_POST['positive_negative'])){
    $sql = $DB->prepare('
        INSERT INTO comments ("ownerUserID", "typeID", type, positive_description, negative_description, conclusion, positive_negative)
          VALUES(\''. $_POST['ownerUserID'] .'\', \''. $_POST['typeID'] .'\', \''. $_POST['type'] .'\', \''. $_POST['positive_description'] .'\', \''. $_POST['negative_description'] .'\', \''. $_POST['conclusion'] .'\', \''. $_POST['positive_negative'] .'\')
    ');
    $sql->execute();
}

$professions = $DB->query('
    SELECT *
      FROM users_professions up
      JOIN professions p ON up."professionID" = p."id"
        WHERE up."userID"='. $user['id'])->fetchAll();
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
<?php if($user['type_of_registration'] === '0'){ ?>
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
                    <div class="company-passport-avatar">
                        <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>'; else echo '<img src="/images/img1.jpg">'; ?>
                    </div>
                    <?php if($common_data['check_owner']){ ?>
                    <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="tipical-button">Сделать фото</a>
                    <div class="file_upload">
                        <button type="button" class="tipical-button"><a href="/users/<?php echo $user['id']; ?>/my_settings/">Загрузить с компьютера</a></button>
                        <input type="file">
                    </div>
                    <?php } ?>
                    <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $user['id'] .'/'; ?>" class="tipical-button">Написать сообщение</a>
                </div>
                <div class="company-passport-right">
                    <div class="company-passport-title"><?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></div>
                    <div class="company-passport-content">
                        <p><b>Руководитель:</b> <?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></p>
                        <p><b>Виды деятельности:</b> <?php echo implode(', ', $professions_str); ?></p>
                        <br>
                        <p><b>Стаж работы:</b> <?php echo $user['age']; ?> лет</p>
                        <p><b>Место работы:</b> г. <?php echo $user['work_city']; ?></p>
                        <br>
                        <p>Наличие СРО и сертификатов</p>
                        <br>
                        <p><span style="color: #054157;">Контактная информация:</span><br>
                        г. Воронеж, ул. Артамонова, оф. 12<br>
                        +7 (473) 2-232-322</p>
                    </div>
                    <div class="company-passport-map">
                        !!сюда вставить карту!!
                    </div>


                    <!-- Понятия не имею на какую кнопку должны вызываться эти окна. Поэтому тупо добавил эти ссылки -->
                    <!-- Удалить к хуям отсюда-->
                    <br><br><br><br>
                    <a href="#write-message" class="modal_on">Написать быстрое сообщение</a>
                    <br>
                    <a href="#conditions" class="modal_on">Условия использования ресурса</a>
                    <br>
                    <a href="#user-login" class="modal_on">Авторизация</a>
                    <br>
                    <a href="#cancel-application" class="modal_on">Отмена заявки</a>
                    <br><br><br><br>
                    <!-- Удалить к хуям до сюда-->

                </div>

            </div>

        </div>
    </div>
</div>
<?php }else{ ?>
<!--Страница пользователя-->
<?php if(!$common_data['check_owner']){ ?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="#">Главная</a>
            </li>
            <li>
                <a href="#">Исполнители</a>
            </li>
            <li>
                <a href="#"><?php echo $user['city_name']; ?></a>
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
                        <span class="views"><b>Просмотров:</b> <?php echo ''; ?>2480</span>
                    </a>
                    <p style="color: #054157;"><b><?php if($user['status'] === '1') echo 'занят'; else echo 'свободен'; ?></b></p>
                    <br>
                    <div class="specialist-personal">
                        <p><b>Место работы:</b> г. <?php echo $user['city_name']; ?></p>
                        <p><b>На сайте:</b> <?php echo ''; ?>2 года</p>
                        <p><b>Стаж работы:</b> <?php echo $user['experience']; ?> лет</p>
                        <p><b>Возраст:</b> <?php echo $user['age']; ?> года</p>
                        <p><b>Тел.</b> <?php echo ''; ?>+8 987 456 45 45</p>
                        <p><b>Виды деятельности:</b></p>
                        <?php echo '<p>'. implode('</p><p>', $professions_str) .'</p>'; ?>
                    </div>
                </div>
                <span class="star-master active"></span>
                <span class="last-time">Был 5 часов 11 минут назад</span>
            </div>
            <div class="product-sub-headline">О себе</div>
            <?php echo $user['description']; ?>
            <br>
            <div class="product-sub-headline">Фото работ</div>
            <?php echo implode(' ', $imgs); ?>
            <div class="product-sub-headline">Цены на услуги</div>
            <?php echo $user['price_description']; ?>
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
                        <a href="#" class="tipical-button good"><input type="submit" value="on" name="positive_negative" /></a>
                        <a href="#" class="tipical-button bad"><input type="submit" value="off" name="positive_negative" /></a>
                    </div>
                </div>
            </form>
            <?php } ?>
            <div class="product-sub-headline">Отзывы</div>
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
                Быстро, качественно, в срок и не дорого.</p>
                <a href="#" class="feedback-author"><?php echo $comment['user_name']; ?>, <?php echo date('m.Y', strtotime($comment['created'])); ?></a>
            </div>
            <?php } ?>
            <div class="show-more-holder">
                <a href="#" class="show-more">Смотреть ещё отзывы</a>
            </div>
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
                        <a href="#" class="passport-avatar">
                            <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>'; else echo '<img src="/images/img1.jpg">'; ?>
                        </a>
                        <a href="#" class="tipical-button">Сделать фото</a>
                        <div class="file_upload">
                            <button type="button" class="tipical-button"><a href="/users/<?php echo $user['id']; ?>/my_settings/">Загрузить с компьютера</a></button>
                            <input type="file">
                        </div>
                        <a href="<?php echo '/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $user['id'] .'/'; ?>" class="tipical-button">Написать сообщение</a>
                    </div>
                    <div class="passport-main-right">
                        <div class="specialist-holder clearfix">
                            <div class="specialist-meta">
                                <a href="#" class="specialist-name">
                                    <?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?>
                                    <span class="valid">(проверено)</span>
                                </a>
                                <form method="POST"><p style="color: #054157;"><b><?php if($user['status'] === '1') echo 'занят'; else echo 'свободен'; ?></b><input type="hidden" value="<?php echo $user['status']; ?>" name="changeStatus"/> <input class="change-status" type="submit" value="изменить статус"/></form>
                                <br>
                                <div class="specialist-personal">
                                    <p><b>Место работы:</b> г. <?php echo $user['city_name']; ?></p>
                                    <p><b>На сайте:</b> <?php echo ''; ?>2 года</p>
                                    <p><b>Стаж работы:</b> <?php echo $user['experience']; ?> лет</p>
                                    <p><b>Возраст:</b> <?php echo $user['age']; ?> года</p>
                                    <p><b>Тел.</b> <?php echo ''; ?>+8 987 456 45 45</p>
                                    <p><b>Виды деятельности:</b></p>
                                    <?php echo '<p>'. implode('</p><p>', $professions_str) .'</p>'; ?>
                                </div>
                            </div>
                            <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="change-personal-data">Изменить личные данные</a>
                        </div>
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>Избранное портфолио</span>
                                <a href="#" class="tipical-button">Добавить</a>
                            </div>
                        </div>
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>О себе</span>
                                <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="tipical-button">Редактировать</a>
                            </div>
                            <?php echo $user['description']; ?>
                        </div>
                        <div class="specialist-meta-block">
                            <div class="specialist-block-title">
                                <span>Услуги и цены</span>
                                <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="tipical-button">Добавить</a>
                            </div>
                            <?php echo $user['price_description']; ?>
                        </div>
                    </div>
                </div>
                <!--
                <div class="specialist-meta-block">
                    <div class="specialist-block-title">
                        <span>Отзывы</span>
                    </div>
                    <div class="feedback-counter clearfix">
                        <a href="#">10 положительных</a>
                        <span>|</span>
                        <a href="#">0 отрицательных</a>
                    </div>
                    <div class="feedback-passport-item">
                        <p><b>Что понравилось</b><br>
                        Мастер очень вежлив (обращался всегда на Вы), пунктуален, ремонт сделан качественно и в обозначенные сроки. В ближайшем будущем планирую так же ремонт в коридоре и в выборе мастера вопрос уже не стоит. Александр настоящий знаток своего дела!</p>
                        <br>
                        <p><b>Что не понравилось</b><br>
                        Все понравилось.</p>
                        <br>
                        <p><b>Общие выводы</b><br>
                        Быстро, качественно, в срок и не дорого.</p>
                    </div>
                    <div class="feedback-passport-item">
                        <p><b>Что понравилось</b><br>
                        Мастер очень вежлив (обращался всегда на Вы), пунктуален, ремонт сделан качественно и в обозначенные сроки. В ближайшем будущем планирую так же ремонт в коридоре и в выборе мастера вопрос уже не стоит. Александр настоящий знаток своего дела!</p>
                        <br>
                        <p><b>Что не понравилось</b><br>
                        Все понравилось.</p>
                        <br>
                        <p><b>Общие выводы</b><br>
                        Быстро, качественно, в срок и не дорого.</p>
                        <br>
                        <p><b>Фотографии</b></p>
                    </div>
                </div>
                -->
            </div>
        </div>
    </div>
</div>
<?php } ?>
<?php } ?>
