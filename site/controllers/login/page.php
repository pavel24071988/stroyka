<?php
$error = '';
if(!empty($_POST)){
    // пытаемся зарегистрировать пользователя
    $DB = Application::$DB;
    if(empty($_POST['email']) || empty($_POST['password'])){
        $error = 'Не все обязательные поля заполнены.';
    }else{
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
        if(empty($user)){
            $error = 'Данного пользователя не существует.';
        }else{
            unset($_SESSION['user']);
            foreach($user as $key => $attribute) $_SESSION['user'][$key] = $attribute;
            echo '<meta http-equiv="refresh" content="1;URL=/">';
        }
        
    }
}
?>
<div class="content">
    <div class="simple-headline">Текст о регистрации</div>
    <div class="authorization-block">
        <p>Вселенная коаксиально сжимает векторный гамма-квант. Сверхпроводник ненаблюдаемо заряжает спиральный погранслой. При погружении в жидкий кислород гетерогенная структура индуцирует кристалл.</p>
        <form class="authorization-form" action="/login/" method="POST">
            <fieldset>
                <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo($_POST['email']); ?>" placeholder="Имя пользователя">
                <input type="password" name="password" placeholder="Пароль">
                <div class="authorization-links clearfix">
                    <div class="authorization-links-part">
                        <a href="/forgot/" class="authorization-link-forgot">Забыли пароль?</a>
                        <a href="/registration/" class="authorization-link-reg">Зарегистрироваться</a>
                    </div>
                    <button type="submit">Войти</button>
                </div>
                <div style="color: red; font-weight: bold;"><?php echo($error); ?></div>
            </fieldset>
        </form>
    </div>
</div>

<div class="footer">
    <div class="inner-wrapper clearfix">
        <div class="footer-left-column">
            <div class="footer-left-column-copyright">Copyright © 2016,  All Rights Reserved</div>
            <ul class="footer-social-list clearfix">
                <li class="footer-social-list-item">
                    <a href="#" class="social-list-item-link vk"></a>
                </li>
                <li class="footer-social-list-item">
                    <a href="#" class="social-list-item-link fb"></a>
                </li>
                <li class="footer-social-list-item">
                    <a href="#" class="social-list-item-link tw"></a>
                </li>
                <li class="footer-social-list-item">
                    <a href="#" class="social-list-item-link od"></a>
                </li>
            </ul>
        </div>
        <div class="footer-right-column clearfix">
            <div class="footer-links-column">
                <div class="footer-links-column-headline">Пользовательское соглашение</div>
                <ul class="footer-links">
                    <li>
                        <a href="#">Вход</a>
                    </li>
                    <li>
                        <a href="#">Регистрация</a>
                    </li>
                    <li>
                        <a href="#">Восстановление пароля</a>
                    </li>
                </ul>
            </div>
            <div class="footer-links-column">
                <div class="footer-links-column-headline">Пользовательское соглашение</div>
                <ul class="footer-links">
                    <li>
                        <a href="#">Вход</a>
                    </li>
                    <li>
                        <a href="#">Регистрация</a>
                    </li>
                    <li>
                        <a href="#">Восстановление пароля</a>
                    </li>
                </ul>
            </div>
            <div class="footer-links-column">
                <div class="footer-links-column-headline">Пользовательское соглашение</div>
                <ul class="footer-links">
                    <li>
                        <a href="#">Вход</a>
                    </li>
                    <li>
                        <a href="#">Регистрация</a>
                    </li>
                    <li>
                        <a href="#">Восстановление пароля</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
