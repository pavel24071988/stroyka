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
        $registration_check = $DB->prepare('
            INSERT INTO users (name, surname, second_name, email, "cityID", "areaID", type_of_registration, type_of_kind, password, age)
              VALUES(\''. $_POST['name'] .'\', \''. $_POST['surname'] .'\', \''. $_POST['second_name'] .'\', \''. $_POST['email'] .'\', \''. $cityID .'\', \''. $_POST['areaID'] .'\', \''. $_POST['type_of_registration'] .'\', \''. $_POST['type_of_kind'] .'\', \''. md5($_POST['password']) .'\', \''. $_POST['age'] .'\')');
        if($registration_check->execute() === true){
            $user = $DB->query('
                SELECT u.*,
                       (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count,
                       c."name" as city_name,
                       a."name" as area_name
                  FROM users u
                  LEFT JOIN cities c ON u."cityID" = c."id"
                  LEFT JOIN areas a ON u."areaID" = a."id"
                    WHERE u."email"=\''. $_POST['email'] .'\' AND u."password"=\''. md5($_POST['password']) .'\'
            ')->fetch();
            unset($_SESSION['user']);
            foreach($user as $key => $attribute) $_SESSION['user'][$key] = $attribute;
            $headers = "From: Stroyka\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            $theme = "Регистрация на сайте стройка завершена.";
            $text = "Здравсвуйте ". $_SESSION['user']['name'] .". Регистрация на сайте стройка завершена.";
            mail($_SESSION['user']['email'] . ", pavel24071988@mail.ru", $theme, $text, $headers);
            echo '<div style="color: red; font-weight: bold;">Регистрация прошла успешно.</div>';
            echo '<meta http-equiv="refresh" content="1;URL=/users/'. $user['id'] .'/">';
        }else{
            $error .= 'Регистрация не удалась. Попробуйте позже.';
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
        <form class="registration-form" action="/registration/" method="POST" enctype="form-data">
            <div style="color: red;"><?php echo $error; ?></div>
            <fieldset id="step1">
                <div class="registration-form-headline">Ваши данные будут проверяться! Не указывайте недостоверную информацию!</div>
                <div class="registration-form-columns clearfix">
                    <div class="registration-form-column1">
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <select name="type_of_registration">
                                    <option value="1">Физическое лицо</option>
                                    <option value="2">Юридическое лицо</option>
                                </select>    
                            </div>
                            <div class="registration-form-row-cell">
                                <select name="type_of_kind">
                                    <option value="1">Частный мастер</option>
                                    <option value="2">Бригада</option>
                                </select>
                            </div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" placeholder="Email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" />
                            </div>
                            <div class="registration-form-row-cell"></div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" placeholder="Фамилия" name="surname" value="<?php if(!empty($_POST['surname'])) echo $_POST['surname']; ?>" />
                            </div>
                            <div class="registration-form-row-cell"></div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" placeholder="Имя" name="name" value="<?php if(!empty($_POST['name'])) echo $_POST['name']; ?>" />
                            </div>
                            <div class="registration-form-row-cell"></div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" placeholder="Отчество" name="second_name" value="<?php if(!empty($_POST['second_name'])) echo $_POST['second_name']; ?>" />
                            </div>
                            <div class="registration-form-row-cell">
                                <label style="line-height: 35px;"><input type="checkbox"> Без отчества</label>
                            </div>
                        </div>
                        <div class="registration-form-row clearfix">
                            <div class="registration-form-row-cell">
                                <input type="text" placeholder="Возраст" name="age" value="<?php if(!empty($_POST['age'])) echo $_POST['age']; ?>" />
                            </div>
                            <div class="registration-form-row-cell"></div>
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
                <button class="tipical-button forward">Далее</button>
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
                            <?php echo Application::getListOfAreas('user', null); ?>
                        </ul>
                    </div>
                    <label class="agree"><input type="checkbox" name="assignment"> Я согласен с <a href="#">пользовательским соглашением</a></label>
                </div>
                <input class="tipical-button forward" style="width: 180px;" type="submit" value="Зарегистрироваться" />
            </fieldset>
        </form>
    </div>
</div>