<?php
$DB = Application::$DB;
$user = $common_data['user_from_db'];


// обработка поста
if(!empty($_POST)){
    
    // записываем виды сфер деятильностей пользователя
    if(!empty($_POST['areas_for_user'])){
        $DB->prepare('DELETE FROM users_kinds_of_jobs WHERE "userID"='. $user['id'])->execute();
        foreach($_POST['areas_for_user'] as $kind_of_job_id){
            $DB->prepare('INSERT INTO users_kinds_of_jobs ("userID", "kind_of_job_id") VALUES('. $user['id'] .', '. $kind_of_job_id .')')->execute();
        }
    }
    
    $error = 'Что - то пошло не так.';
    if(isset($_POST['password_data'])){
        if(empty($_POST['current_password'])){
            $error = 'Заполните поле текущий пароль.';
        }elseif(md5($_POST['current_password']) !== $user['password']){
            $error = 'Введенный текущий пароль не совпадает с Вашим паролем.';
        }elseif(($_POST['new_password'] !== $_POST['repeat_new_password']) || empty($_POST['new_password'])){
            $error = 'Пароли не совпадают.';
        }else{
            $update_check = $DB->prepare('UPDATE users SET "password"=\''. md5($_POST['new_password']) .'\' WHERE "id"='. $user['id']);
            if($update_check->execute() === true){
                $error = 'Пароль изменен.';
            }
        }
    }elseif(isset($_POST['personal_data'])){

        $city = $DB->query('SELECT c.id FROM cities c WHERE c.name=\''. $_POST['city_name'] .'\'')->fetch();
        if(empty($city)){
            $city = $DB->prepare('INSERT INTO cities (name) VALUES(\''. $_POST['city_name'] .'\')')->execute();
            $cityID = $DB->lastInsertId('cities_id_seq');
        }else{
            $cityID = $city['id'];
        }

        $update_check = $DB->prepare('
            UPDATE users
              SET "name"=\''. $_POST['name'] .'\',
                  "surname"=\''. $_POST['surname'] .'\',
                  "second_name"=\''. $_POST['second_name'] .'\',
                  "experience"=\''. $_POST['experience'] .'\',
                  "phone"=\''. $_POST['phone'] .'\',
                  "cityID"=\''. $cityID .'\'
                WHERE "id"='. $user['id']);
        if(empty($_POST['areas_for_user'])) $error = 'Необходимо выбрать вид деятельности<br/>';
        elseif($update_check->execute() === true) $error = 'Данные отредактированы.';
        else $error = 'Не заполнены обязательные поля.';
    }
    $user = $DB->query('
        SELECT u.*,
               c.name as city_name,
               (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count
          FROM users u
          LEFT JOIN cities c ON u."cityID" = c.id
            WHERE u."id"='. $user['id'])->fetch();
    $_SESSION['user'] = $user;
}

$list_of_areas = Application::getListOfAreas('user', $user['id']);

?>



<div class="content">
    <div class="my-page-content clearfix">
        <?php if($common_data['check_owner']) echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Настройки</a>
                    </li>
                </ul>
            </div>
            <div class="my-page-wrapper-content">
                <div class="my-page-wrapper-headline">Учетные данные</div>
                <div class="personal-main-data">
                    <?php if(!empty($error)) echo '<div style="color: red; font-weight: normal;">'. $error .'</div><br/>'; ?>
                    <p>Ваш логин: <span><?php echo $user['email']; ?></span></p>
                    <p>Ваша почта: <span><?php echo $user['email']; ?></span></p>
                </div>
                <form class="personal-data-form" method="POST">
                    <fieldset>
                        <div class="personal-data-form-headline">Изменить пароль:</div>
                        <div class="personal-data-row clearfix">
                            <label>Введите текущий пароль:</label><input type="password" name="current_password"/>
                        </div>
                        <div class="personal-data-row clearfix">
                            <label>Введите новый пароль:</label><input type="password" name="new_password"/>
                        </div>
                        <div class="personal-data-row clearfix">
                            <label>Подтверждение:</label><input type="password" name="repeat_new_password"/>
                        </div>
                        <button class="personal-data-form-submit" type="submit" name="password_data">Изменить пароль</button>
                    </fieldset>
                </form>
                <form class="personal-data-form pers-data" method="POST" enctype="multipart/form-data">
                    
                    <fieldset>
                        <div class="personal-data-form-headline">Личные данные:</div>
                        <div class="personal-data-row clearfix">
                            <div class="personal-data-row-cell">
                                <label>Фамилия:</label><input type="text" value='<?php echo $user['surname']; ?>' name="surname" />
                            </div>
                            <div class="personal-data-row-cell">
                                <label>Стаж работы:</label><input type="text" value='<?php echo $user['experience']; ?>' name="experience" />
                            </div>
                        </div>
                        <div class="personal-data-row clearfix">
                            <div class="personal-data-row-cell">
                                <label>Имя:</label><input type="text" value='<?php echo $user['name']; ?>' name="name" />
                            </div>
                            <div class="personal-data-row-cell">
                                <label>Место работы:</label><input type="text" value='<?php echo $user['city_name']; ?>' name="city_name" />
                            </div>
                        </div>
                        <div class="personal-data-row clearfix">
                            <div class="personal-data-row-cell">
                                <label>Отчество:</label><input type="text" value='<?php echo $user['second_name']; ?>' name="second_name" />
                            </div>
                            <div class="personal-data-row-cell">
                                <label>Телефон:</label><input type="text" value='<?php if(!empty($user['phone'])) echo $user['phone']; ?>' name="phone" />
                            </div>
                        </div>
                        <br><br>
                        <div class="personal-data-form-headline">Специализация:</div>
                        <ul class="searcher-categories">
                        <?php echo $list_of_areas; ?>
                        </ul>
                        <button class="personal-data-form-submit" type="submit" name="personal_data">Сохранить</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>