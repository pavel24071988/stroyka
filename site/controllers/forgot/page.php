<?php
$DB = Application::$DB;
if(!empty($_POST['email'])){
    $checkEmail = $DB->query('SELECT * FROM users u WHERE u."email"=\''. $_POST['email'] .'\'')->fetchAll();
    $error = 'Указанного адреса у нас нет.';
    if(!empty($checkEmail)){
        $error = 'Не получилось восстановить пароль.';
        $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max=10;
        $size=StrLen($chars)-1;
        $newPassword=null;
        while($max--) $newPassword.=$chars[rand(0,$size)];
        
        $changePassword = $DB->prepare('UPDATE users SET "password"=\''. ($newPassword) .'\' WHERE "id"='. $checkEmail[0]['id']);
        if($changePassword->execute() === true){
            $headers = "From: Stroyka\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            $theme = "Восстановление пароля на сайте стройка.";
            $text = "Здравствуйте ". $checkEmail[0]['name'] .". Восстановление пароля на сайте стройка завершено.<br /> Новый пароль ". $newPassword ."<br />Пароль вы сможете сменить в Личном Кабинете";
            if(mail($_POST['email'] . ", pavel24071988@mail.ru", $theme, $text, $headers)){
                $error = 'Пароль восстановлен. Проверте email.';
                unset($_POST);
            }
        }
    }
    echo '<div style="color: red; font-weight: normal;">'. $error .'</div>';
}
?>
<!--<h1>Форма восстановления пароля</h1>
<form method="POST">
    Укажите адрес электронный почты аккаунта:<br/>
    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>"/><br />
    <input type="submit" name="Восстановить"/>
</form>-->


<div class="content">
    <div class="simple-headline">Форма восстановления пароля</div>
    <div class="authorization-block">
        <p style="color: #1c2f3b;">Для восстановления доступа к аккаунту заполните форму ниже. Пароль будет выслан на почту.</p>
        <form class="authorization-form" method="POST">
            <fieldset>
                <label class="frgt-label">Введите почту</label>
                <input type="email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>"/><br />
                <input class="tipical-button frgt" value="Отправить" type="submit" name="Восстановить"/>
            </fieldset>
        </form>
    </div>
</div>