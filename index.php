<?php
session_start();
include('/functions/main.php')
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" href="/css/style.css" media="all" />
	<link rel="stylesheet" href="/css/jquery.fancybox-1.3.4.css" media="all" />

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
	<script type="text/javascript" src="/js/jquery.main.js"></script>
</head>
<body>
    <div class="header">
        <span><a href="/">Логотип</a></span>
        <a href="/masters/">Мастера</a>
        <a href="/objects/">Заказы</a>
        <a href="/jobs/">Вакансии</a>
        <?php if(!empty($_SESSION['user'])) echo 'Привуэт <a href="/users/'. $_SESSION['user']['id'] .'/">'. $_SESSION['user']['name'] .'</a> <a href="/login/logout/">Выйти</a>'; else echo '<a href="/login/">Войти</a>'; ?>
    </div>
    
    <div class="content">
        <?php
            $application = new Application;
            $application::get_content();
        ?>
    </div>
    
    <div class="footer">
        <a href="#">На объекте</a>
        <a href="/assignment/">Пользовательское соглашение</a>
        <a href="/login/">Вход</a>
        <a href="/registration/">Регистрация</a>
        <a href="/recover/">Восстановление пароля</a>
        <a href="/about/">О сайте</a>
        <a href="#">Рекомендации заказчикам</a>
        <a href="#">Рекомендации строителям</a>
        <a href="#">Сервисы</a>
        <a href="#">Поиск по сайту</a>
    </div>
    
</body>
</html>