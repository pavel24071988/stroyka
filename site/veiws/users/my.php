<?php
$user = $common_data['user'];
$DB = Application::$DB;

if(!empty($_POST['changeStatus'])){
    if($_POST['changeStatus'] === '1') $newValue = '0';
    else $newValue = '1';
    $DB->prepare('UPDATE users SET "status"='. $newValue .' WHERE "id"='. $user['id'])->execute();
    $user['status'] = $newValue;
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
                        <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>' ?>
                    </div>
                    <?php if($common_data['check_owner']){ ?>
                    <a href="/users/<?php echo $user['id']; ?>/my_settings/" class="tipical-button">Сделать фото</a>
                    <div class="file_upload">
                        <button type="button" class="tipical-button"><a href="/users/<?php echo $user['id']; ?>/my_settings/">Загрузить с компьютера</a></button>
                        <input type="file">
                    </div>
                    <?php } ?>
                    <a href="#" class="tipical-button">Написать сообщение</a>
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