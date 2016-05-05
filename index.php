<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] .'/functions/main.php');
$application = new Application;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>na_objekte | index</title>

	<link rel="stylesheet" type="text/css" href="/site/css/style.css" media="all" />
        <link rel="stylesheet" type="text/css" href="/site/css/jquery.fancybox-1.3.4.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/site/css/jquery-ui.min.css">

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
        <script type="text/javascript" src="/site/js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="/site/js/jquery.fancybox-1.3.4.pack.js"></script>
        <script type="text/javascript" src="/site/js/jquery.main.js"></script>
	<script type="text/javascript" src="/site/js/main.js"></script>

</head>
<body>

<div class="wrapper">
    <div class="header">
        <div class="inner-wrapper clearfix">
            <a href="/" class="logotype"></a>
            <ul class="navigation clearfix">
                <li>
                    <a <?php if($application::$URL[1] === 'masters') echo 'class="active"'; ?> href="/masters/">Мастера</a>
                </li>
                <li>
                    <a <?php if($application::$URL[1] === 'objects') echo 'class="active"'; ?> href="/objects/">Заказы</a>
                </li>
                <li>
                    <a <?php if($application::$URL[1] === 'jobs') echo 'class="active"'; ?> href="/jobs/">Вакансии</a>
                </li>
            </ul>
            <?php
            if(!empty($_SESSION['user'])){
                $userMessages = $application::getCountsUserMessages($_SESSION['user']['id']);
                echo '<a href="/login/logout/" class="login no-lock">Выйти</a> <a href="/users/'. $_SESSION['user']['id'] .'/" class="login no-lock">'. $_SESSION['user']['name'] .'</a>';
            }else{
                echo '<a href="/login/" class="login">Авторизация</a>';
            }
            ?>
        </div>
    </div>
    <?php $application::get_content(); ?>
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
</div>

</body>
</html>