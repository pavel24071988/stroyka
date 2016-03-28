<?php
// получаем сферы деятильности с подвидами
$DB = Application::$DB;
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

// регистрируем пользователя
$error = '';
if(!empty($_POST)){
    $array_of_check = ['email' => 'Почта(логин)', 'surname' => 'Фамилия', 'name' => 'Имя', 'work_city' => 'Основное место работы (город или область)'];
    foreach($_POST as $key => $row){
        if(isset($array_of_check[$key])){
            if(empty($row)) $error .= 'Не заполнено поле: '. $array_of_check[$key] .'<br/>';
        }
    }
    if(empty($_POST['assignment'])) $error .= 'Не заполнено поле: Я согласен с пользовательским соглашением<br/>';
    if(empty($_POST['password'])) $error .= 'Не заполнено поле: Пароль<br/>';
    elseif($_POST['password'] !== $_POST['repeat_password']) $error .= 'Поля пароль и повторите пароль не совпадают<br/>';
    
    // начинаем регистрировать
    if(empty($error)){        
        $registration_check = $DB->prepare('
            INSERT INTO users (name, surname, second_name, email, work_city, type_of_registration, type_of_kind, password, age)
              VALUES(\''. $_POST['name'] .'\', \''. $_POST['surname'] .'\', \''. $_POST['second_name'] .'\', \''. $_POST['email'] .'\', \''. $_POST['work_city'] .'\', \''. $_POST['type_of_registration'] .'\', \''. $_POST['type_of_kind'] .'\', \''. md5($_POST['password']) .'\', \''. $_POST['age'] .'\')');
        if($registration_check->execute() === true){
            $user = $DB->query('SELECT * FROM users u WHERE u."email"=\''. $_POST['email'] .'\' AND u."password"=\''. md5($_POST['password']) .'\'')->fetchAll();
            unset($_SESSION['user']);
            foreach($user[0] as $key => $attribute) $_SESSION['user'][$key] = $attribute;
            
            echo '<div style="color: red; font-weight: bold;">Регистрация прошла успешно.</div>';
            echo '<meta http-equiv="refresh" content="1;URL=/users/'. $user[0]['id'] .'/">';
        }else{
            $error .= 'Регистрация не удалась. Попробуйте позже.';
        }
    }
}
?>
    
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
