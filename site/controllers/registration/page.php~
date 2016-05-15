 <?php
// получаем сферы деятильности с подвидами
$DB = Application::$DB;
/*
$area_of_jobs = $DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
$list_of_areas = '<ul>';
foreach($area_of_jobs as $key => $area_of_job){
    $list_of_areas .= '<li data-area_id="'. $area_of_job['id'] .'">'. $area_of_job['name'];
    $kinds_of_jobs = $DB->query('SELECT * FROM kinds_of_jobs kj WHERE kj."areaID"='. $area_of_job['id'])->fetchAll();
    if(!empty($kinds_of_jobs)) $list_of_areas .= '<ul ">';
    foreach($kinds_of_jobs as $key => $kind_of_job){
        $list_of_areas .= '<li data-kind_id="'. $kind_of_job['id'] .'">'. $kind_of_job['name'] .'</li>';
    }
    if(!empty($kinds_of_jobs)) $list_of_areas .= '</ul>';
    $list_of_areas .= '</li>';
}
$list_of_areas .= '</ul>';
*/
// регистрируем пользователя
$error = '';
if(!empty($_POST)){
    $array_of_check = ['email' => 'Почта(логин)', 'surname' => 'Фамилия', 'name' => 'Имя', 'work_city' => 'Основное место работы (город или область)'];
    foreach($_POST as $key => $row){
        if(isset($array_of_check[$key])){
            if(empty($row)) $error .= 'Не заполнено поле: '. $array_of_check[$key] .'<br/>';
        }
    }
    
    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
    $max=10;
    $size=StrLen($chars)-1;
    $newPassword=null;
    while($max--) $newPassword.=$chars[rand(0,$size)];
    $_POST['password'] = $newPassword;
    $_POST['repeat_password'] = $newPassword;
    
    if(empty($_POST['assignment'])) $error .= 'Не заполнено поле: Я согласен с пользовательским соглашением<br/>';
    if(empty($_POST['password'])) $error .= 'Не заполнено поле: Пароль<br/>';
    elseif($_POST['password'] !== $_POST['repeat_password']) $error .= 'Поля пароль и повторите пароль не совпадают<br/>';
    
    $city = $DB->query('SELECT * FROM cities c WHERE c.name ILIKE \''. $_POST['city'] .'\'')->fetch();
    if(empty($city)){
        $DB->prepare('INSERT INTO cities (name) VALUES(\''. $_POST['city'] .'\')')->execute();
        $city = $DB->query('SELECT * FROM cities c WHERE c.name ILIKE \''. $_POST['city'] .'\'')->fetch();
    }
    $cityID = $city['id'];
    
    // начинаем регистрировать
    if(empty($error)){
        $user_check = $DB->query('SELECT * FROM users WHERE email=\''. $_POST['email'] .'\'')->fetch();
        if(!empty($user_check)){
            $error .= 'Данный адрес уже зарегистрирован. ';
        }
        
        $registration_check = $DB->prepare('
            INSERT INTO users (name, surname, second_name, email, "cityID", "areaID", type_of_registration, type_of_kind, password, cpo, contact_person)
              VALUES(\''. $_POST['name'] .'\', \''. $_POST['surname'] .'\', \''. $_POST['second_name'] .'\', \''. $_POST['email'] .'\', \''. $cityID .'\', \''. $_POST['areaID'] .'\', \''. $_POST['type_of_registration'] .'\', \''. $_POST['type_of_kind'] .'\', \''. md5($_POST['password']) .'\', \''. $_POST['cpo'] .'\', \''. $_POST['contact_person'] .'\')');
        if($registration_check->execute() === true){
            
            $newUserID = $DB->lastInsertId('users_id_seq');
            
            if(!empty($_FILES['avatar']['tmp_name'])){
                if(!file_exists("images/users/". $newUserID)) mkdir("images/users/". $newUserID, 0777);
                if(copy($_FILES['avatar']['tmp_name'], "images/users/". $newUserID ."/". $_FILES['avatar']['name'])){
                    $update_avatar = $DB->prepare('UPDATE users SET "avatar"=\''. $_FILES['avatar']['name'] .'\' WHERE "id"='. $newUserID);
                    if($update_avatar->execute() === true){
                        $error = 'Фотография загружена.';
                        $_SESSION['user']['avatar'] = $_FILES['avatar']['name'];
                        $user['avatar'] = $_FILES['avatar']['name'];
                    }
                    else $error = 'Не удалось загрузить фотография.';
                }
            }
            
            // записываем виды сфер деятильностей пользователя
            if(!empty($_POST['areas_for_user'])){
                $DB->prepare('DELETE FROM users_kinds_of_jobs WHERE "userID"='. $newUserID)->execute();
                foreach($_POST['areas_for_user'] as $kind_of_job_id){
                    $DB->prepare('INSERT INTO users_kinds_of_jobs ("userID", "kind_of_job_id") VALUES('. $newUserID .', '. $kind_of_job_id .')')->execute();
                }
            }
            
            
            $user = $DB->query('
                SELECT u.*,
                       (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count,
                       c."name" as city_name,
                       a."name" as area_name
                  FROM users u
                  LEFT JOIN cities c ON u."cityID" = c."id"
                  LEFT JOIN areas a ON u."areaID" = a."id"
                    WHERE u."id"=\''. $newUserID .'\'
            ')->fetch();
            unset($_SESSION['user']);
            foreach($user as $key => $attribute) $_SESSION['user'][$key] = $attribute;
            $headers = "From: Stroyka\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            $theme = "Регистрация на сайте стройка завершена.";
            $text = "Здравствуйте ". $_SESSION['user']['name'] .". Регистрация на сайте стройка завершена.";
            mail($_SESSION['user']['email'] . ", pavel24071988@mail.ru", $theme, $text, $headers);
            echo '<div style="color: red; font-weight: bold;">Регистрация прошла успешно.</div>';
            echo '<meta http-equiv="refresh" content="1;URL=/users/'. $user['id'] .'/">';
        }else{
            $error .= 'Регистрация не удалась. Попробуйте позже. ';
        }
    }
}
?>
<!--
<h1>Регистрация</h1>

<h4>Ваши данные будут проверяться! Не указывайте недостоверную информацию!</h4>
<div style="color: red;"><?php echo $error; ?></div>
<br/>
<form action="/registration/" method="POST" enctype="form-data">
    <select name="type_of_registration">
        <option value="1">Физическое лицо</option>
        <option value="2">Юридическое лицо</option>
    </select>
    <br/>
    <select name="type_of_kind">
        <option value="1">Частный мастер</option>
        <option value="2">Бригада</option>
    </select>
    <br/>
    Почта (логин):<br/>
    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" />
    <br/>
    Пароль:<br/>
    <input type="password" name="password" />
    <br/>
    Повторите пароль:<br/>
    <input type="password" name="repeat_password" />
    <br/>
    <br/>
    Фамилия:<br/>
    <input type="text" name="surname" value="<?php if(!empty($_POST['surname'])) echo $_POST['surname']; ?>" />
    <br/>
    Имя:<br/>
    <input type="text" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>" />
    <br/>
    Отчество:<br/>
    <input type="text" name="second_name" value="<?php if(!empty($_POST['second_name'])) echo $_POST['second_name']; ?>" />
    <br/>
    Возраст:<br/>
    <input type="text" name="age" value="<?php if(!empty($_POST['age'])) echo $_POST['age']; ?>" />
    <br/><br/>
    Фотография
    <br/>
    <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif">
    <br/>
    - На фотографии должно быть видно лицо
    <br/>
    - На фотографии должен быть владелец анкеты
    <br/><br/>
    Основное место работы (город или область)<br/>
    <input type="text" name="work_city" value="<?php if(!empty($_POST['work_city'])) echo $_POST['work_city']; ?>" />
    <br/><br/>
    Выберите сферу деятельности:
    <br/>
    <?php echo $list_of_areas; ?>
    <br/><br/>
    <input type="checkbox" name="assignment">Я согласен с <a href="/assignment/" target="_blank">пользовательским соглашением</a>.
    <br/><br/><br/>
    <input type="submit" value="Зарегистрироваться" />
    <br/><br/><br/><br/><br/><br/>
</form>
-->
<div class="content">
    <div class="simple-headline">Регистрация</div>
    <div class="registration-holder">
        <div class="registration-breadcrumb clearfix">
            <a href="#" class="registration-breadcrumb-step active" data-id="step1">1</a>
            <div class="registration-breadcrumb-break">шаг</div>
            <a href="#" class="registration-breadcrumb-step" data-id="step2">2</a>
        </div>
        <form class="registration-form" action="/registration/" method="POST" enctype="multipart/form-data">
            <div style="color: red;"><?php echo $error; ?></div>
            <fieldset id="step1">
                <div class="registration-form-headline">Ваши данные будут проверяться! Не указывайте недостоверную информацию!</div>
                <div class="registration-form-columns clearfix">
                    <div class="registration-form-column1">
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <select id="facetype" name="type_of_registration">
                                    <option value="1" <?php if(!empty($_POST['type_of_registration']) && $_POST['type_of_registration'] === '1') echo 'selected'; ?>>Физическое лицо</option>
                                    <option value="2" <?php if(!empty($_POST['type_of_registration']) && $_POST['type_of_registration'] === '2') echo 'selected'; ?>>Юридическое лицо</option>
                                </select>    
                            </div>
                            <div class="registration-form-row-cell">
                                <select name="type_of_kind" class="fiz-facetype">
                                    <option value="1" <?php if(!empty($_POST['type_of_kind']) && $_POST['type_of_kind'] === '1') echo 'selected'; ?>>Частный мастер</option>
                                    <option value="2" <?php if(!empty($_POST['type_of_kind']) && $_POST['type_of_kind'] === '2') echo 'selected'; ?>>Бригада</option>
                                </select>
                                <select name="cpo" class="ur-facetype" style="display: none;">
                                    <option>Наличие СРО и лицензий</option>
                                    <option value="true" <?php if(!empty($_POST['cpo']) && $_POST['cpo'] === true) echo 'selected'; ?>>Да</option>
                                    <option value="false" <?php if(!empty($_POST['cpo']) && $_POST['cpo'] === false) echo 'selected'; ?>>Нет</option>
                                </select>
                            </div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" class="fiz-facetype" placeholder="Email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" />
                                <input type="text" class="ur-facetype" placeholder="Наименование организации" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>" style="display: none;" />
                            </div>
                            <div class="registration-form-row-cell">
                                <select name="experience" class="ur-facetype" style="display: none;">
                                    <option>Опыт работы (лет)</option>
                                    <option value="1" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '1') echo 'selected'; ?>>1</option>
                                    <option value="2" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '2') echo 'selected'; ?>>2</option>
                                    <option value="3" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '3') echo 'selected'; ?>>3</option>
                                    <option value="4" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '4') echo 'selected'; ?>>4</option>
                                    <option value="5" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '5') echo 'selected'; ?>>5</option>
                                    <option value="6" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '6') echo 'selected'; ?>>6</option>
                                    <option value="7" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '7') echo 'selected'; ?>>7</option>
                                    <option value="8" <?php if(!empty($_POST['experience']) && $_POST['experience'] === '8') echo 'selected'; ?>>8</option>
                                </select>
                            </div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" class="fiz-facetype" placeholder="Фамилия" name="surname" value="<?php if(!empty($_POST['surname'])) echo $_POST['surname']; ?>" />
                                <input type="text" class="ur-facetype" placeholder="Адрес организации" name="" value="" style="display: none;" />
                            </div>
                            <div class="registration-form-row-cell"></div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" class="fiz-facetype" placeholder="Имя" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>" />
                                <input type="text" class="ur-facetype" placeholder="Контактное лицо" name="<?php if(!empty($_POST['contact_person'])) echo $_POST['contact_person']; ?>" value="" style="display: none;" />
                            </div>
                            <div class="registration-form-row-cell"></div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" class="fiz-facetype" placeholder="Отчество" name="second_name" value="<?php if(!empty($_POST['second_name'])) echo $_POST['second_name']; ?>" />
                                <input type="text" class="ur-facetype" placeholder="Номер телефона" name="phone" value="<?php if(!empty($_POST['phone'])) echo $_POST['phone']; ?>" style="display: none;" />
                            </div>
                            <div class="registration-form-row-cell">
                                <label class="fiz-facetype" style="line-height: 35px;"><input type="checkbox" name="second_name_exist" <?php if(!empty($_POST['second_name_exist'])) echo 'checked'; ?>> Без отчества</label>
                            </div>
                        </div>
                    </div>
                    <div class="registration-form-column2 clearfix">
                        <a href="#" class="user-avatar"></a>
                        <div class="user-avatar-content">
                            <p><b>Фотография</b></p>
                            <p>- На фотографии должно быть видно лицо</p>
                            <p>- На фотографии должен быть владелец анкеты</p>
                            <a href="#" class="tipical-button">Сделать фото</a>
                            <div class="file_upload">
                                <button type="button" class="tipical-button">Загрузить с компьютера</button>
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif">
                            </div>
                        </div>
                    </div>
                </div>
                <a href="#" class="tipical-button forward" id="forward">Далее</a>
            </fieldset>
            <fieldset id="step2" style="display: none;">
                <div class="registration-form-columns clearfix">
                    <input type="hidden" name="areaID" value="1" />
                    <div class="main-place">
                        <p class="main-place-title">Основное место работы (город или область)</p>
                        <input type="text" name="city" value="<?php if(!empty($_POST['city'])) echo $_POST['city']; ?>" />
                    </div>
                    <div class="main-place">
                        <p class="main-place-title" style="margin-bottom: 15px;">Выберите сферу деятельности</p>
                        <ul class="searcher-categories registr-type">
                            <?php
                                $areas_for_user = [];
                                if(isset($_POST['areas_for_user'])) $areas_for_user = ['areas_for_user' => $_POST['areas_for_user']];
                                echo Application::getListOfAreas('user', null, $areas_for_user);
                            ?>
                        </ul>
                    </div>
                    <label class="agree"><input type="checkbox" name="assignment"> Я согласен с <a href="#">пользовательским соглашением</a></label>
                </div>
                <input class="tipical-button forward" style="width: 180px;" type="submit" value="Зарегистрироваться" />
            </fieldset>
        </form>
    </div>
</div>
<span id="ava-files"></span>
