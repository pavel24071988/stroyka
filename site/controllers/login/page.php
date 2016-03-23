<?php
$error = '';
if(!empty($_POST)){
    // пытаемся зарегистрировать пользователя
    $DB = Application::$DB;
    if(empty($_POST['email']) || empty($_POST['password'])){
        $error = 'Не все обязательные поля заполнены.';
    }else{
        $user = $DB->query('SELECT * FROM users u WHERE u."email"=\''. $_POST['email'] .'\' AND u."password"=\''. md5($_POST['password']) .'\'')->fetchAll();
        if(empty($user[0])){
            $error = 'Данного пользователя не существует.';
        }else{
            unset($_SESSION['user']);
            foreach($user[0] as $key => $attribute) $_SESSION['user'][$key] = $attribute;
            echo '<meta http-equiv="refresh" content="1;URL=/">';
        }
        
    }
}
?>

<h1>Текст о регистрации.</h1>
<br/><br/>
<div style="width:250px;">Вселенная коаксиально сжимает векторный гамма-квант. Сверхпроводник ненаблюдаемо заряжает спиральный погранслой. При погружении в жидкий кислород гетерогенная структура индуцирует кристалл.</div>
<br/>
<form action="/login/" method="POST">
    Имя пользователя(почта):<br/>
    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo($_POST['email']); ?>"/><br/>
    Пароль:<br/>
    <input type="password" name="password" />
    <br/><br/>
    <input type="submit" value="Войти" />
</form>
<div style="color: red; font-weight: bold;"><?php echo($error); ?></div>
<br/><br/>
<?php
if(empty($_SESSION['user'])){
    echo '<a href="/forgot/">Забыли пароль?</a><br/>';
    echo '<a href="/registration/">Зарегистрироваться</a>';
};
?>
<br/><br/><br/>
