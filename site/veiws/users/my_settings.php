<?php
$DB = Application::$DB;
$user = $common_data['user_from_db'];


// обработка поста
if(!empty($_POST)){
    $error = 'Что - то пошло не так.';
    if(!empty($_POST['password_data'])){
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
    }elseif(!empty($_POST['personal_data'])){
        // обработаем картинку
        $avatar_update_str = '';
        if(!empty($_FILES['avatar']['tmp_name'])){
            if(!file_exists("images/users/". $user['id'])) mkdir("images/users/". $user['id'], 0777);
            if(copy($_FILES['avatar']['tmp_name'], "images/users/". $user['id'] ."/". $_FILES['avatar']['name'])){
                $avatar_update_str = ', "avatar"=\''. $_FILES['avatar']['name'] .'\'';
            }
        }
        
        $update_check = $DB->prepare('
            UPDATE users
              SET "name"=\''. $_POST['name'] .'\',
                  "surname"=\''. $_POST['surname'] .'\',
                  "second_name"=\''. $_POST['second_name'] .'\',
                  "experience"=\''. $_POST['experience'] .'\',
                  "work_city"=\''. $_POST['work_city'] .'\'
                  '. $avatar_update_str .'
                WHERE "id"='. $user['id']);
        if($update_check->execute() === true){
            $error = 'Данные отредактированы.';
        }else{
            $error = 'Не заполнены обязательные поля.';
        }
    }
    $user = $DB->query('
        SELECT u.*,
               (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count
          FROM users u
            WHERE u."id"='. $user['id'])->fetchAll();
    $user = $user[0];
    $_SESSION['user'] = $user;
}


// получаем сферы деятильности с подвидами
$area_of_jobs = $DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
$list_of_areas = '<ul>';
foreach($area_of_jobs as $key => $area_of_job){
    $list_of_areas .= '<li data-area_id="'. $area_of_job['id'] .'">'. $area_of_job['name'];
    $kinds_of_jobs = $DB->query('SELECT * FROM kinds_of_jobs kj WHERE kj."areaID"='. $area_of_job['id'])->fetchAll();
    if(!empty($kinds_of_jobs)) $list_of_areas .= '<ul ">';
    foreach($kinds_of_jobs as $key => $kind_of_job){
        $list_of_areas .= '<li data-kind_id="'. $kind_of_job['id'] .'"><input type="checkbox" name="areas[]" value="'. $area_of_job['id'] .'_'. $kind_of_job['id'] .'">'. $kind_of_job['name'] .'</li>';
    }
    if(!empty($kinds_of_jobs)) $list_of_areas .= '</ul>';
    $list_of_areas .= '</li>';
}
$list_of_areas .= '</ul>';
echo $common_data['left_menu'];
?>
<h1>Учетные данные</h1>
<?php if(!empty($error)) echo '<div style="color: red;">'. $error .'</div><br/>'; ?>
<div>Ваш логин: <strong><?php echo $user['email']; ?></strong></div>
<div>Ваша почта: <strong><?php echo $user['email']; ?></strong></div>
<br/>
<form method="POST">
<strong>Изменить пароль:</strong>
<br/>
Введите текущий пароль:
<input type="password" name="current_password" />
<br/>
Введите новый пароль:
<input type="password" name="new_password" />
<br/>
Подтверждение:
<input type="password" name="repeat_new_password" />
<br/>
<br/>
<input type="submit"  name="password_data" value="Изменить пароль">
</form>
<h1>Личные данные</h1>
<form method="POST" enctype="multipart/form-data">
<?php
    if(!empty($user['avatar'])){
        echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>';
    }
?>
<p>Загрузите файл с картинкой</p>
<p><input type="file" name="avatar"></p>
Фамилия:
<input type="text" value='<?php echo $user['surname']; ?>' name="surname" />
<br/>
Имя:
<input type="text" value='<?php echo $user['name']; ?>' name="name" />
<br/>
Отчество:
<input type="text" value='<?php echo $user['second_name']; ?>' name="second_name" />
<br/>
<br/>
Стаж работы:
<input type="text" value='<?php echo $user['experience']; ?>' name="experience" />
<br/>
Место работы:
<input type="text" value='<?php echo $user['work_city']; ?>' name="work_city" />
<br/>
Специализации:
<?php echo $list_of_areas; ?>
<br/>
<br/>
<input type="submit" name="personal_data" value="Сохранить">
</form>