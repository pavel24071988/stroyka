<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] .'/functions/main.php');
$application = new Application;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" href="/site/css/style.css" media="all" />
	<link rel="stylesheet" href="/css/jquery.fancybox-1.3.4.css" media="all" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script type="text/javascript" src="/site/js/main.js"></script>
</head>
<body>
    <div class="header">
        <span><a href="/">Логотип</a></span>
        <a href="/masters/">Мастера</a>
        <a href="/objects/">Заказы</a>
        <a href="/jobs/">Вакансии</a>
        <?php 
        if(!empty($_SESSION['user'])){
            $userMessages = $application::getCountsUserMessages($_SESSION['user']['id']);
            echo 'Привуэт <a href="/users/'. $_SESSION['user']['id'] .'/">'. $_SESSION['user']['name'] .'</a> <strong>(+'. $userMessages[0]['count_new'] .')</strong> из ('. $userMessages[0]['count_all'] .') <a href="/login/logout/">Выйти</a>';
        }else{
            echo '<a href="/login/">Войти</a>';
        }
        ?>
    </div>
    
    <div class="content">
        <?php $application::get_content(); ?>
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

﻿<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>na_objekte | index</title>

	<link rel="stylesheet" href="css/style.css" media="all" />

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.main.js"></script>

</head>
<body>

<div class="wrapper">
    <div class="header">
        <div class="inner-wrapper clearfix">
            <a href="#" class="logotype"></a>
            <ul class="navigation clearfix">
                <li>
                    <a href="#">Мастера</a>
                </li>
                <li>
                    <a href="#">Заказы</a>
                </li>
                <li>
                    <a href="#">Вакансии</a>
                </li>
            </ul>
            <a href="#" class="login">Авторизация</a>
        </div>
    </div>
    <div class="sub-header">
        <div class="inner-wrapper">
           <div class="tipical-headline">Строительный портал «На объекте»</div>
           <ul class="scores-block clearfix">
               <li class="scores-block-item">Мастеров онлайн: <span>293</span></li>
               <li class="scores-block-item">Вакансий на сайте: <span>2154</span></li>
               <li class="scores-block-item">Компаний: <span>1045</span></li>
           </ul>
           <div class="sub-header-headline">Здесь вводный текст кратко описывает суть и функционал сайта.</div>
           <p>Психологическая среда, как принято считать, все еще интересна для многих. Интересно отметить, что рейтинг разнородно отражает культурный показ баннера. Практика однозначно показывает, что создание приверженного покупателя парадоксально переворачивает креативный рейтинг. Опрос, пренебрегая деталями, отталкивает коллективный медийный канал, опираясь на опыт западных коллег.</p>
           <p>Согласно предыдущему, внутрифирменная реклама программирует фирменный стиль. Комплексный анализ ситуации регулярно детерминирует нишевый проект. Формат события усиливает из ряда воноснован на тщательном анализе данных.</p>
           <ul class="sub-header-slogan">
               <li class="sub-header-slogan-item">
                   У вас есть заказ?<br><span>Создайте заявку и найдите<br>исполнителей!</span>
               </li>
               <li class="sub-header-slogan-item">
                   Ищете сотрудника на<br>постоянную работу?<br><span>Создайте вакансию здесь!</span>
               </li>
               <li class="sub-header-slogan-item">
                   Вы - мастер?<br><span>Сотни предложений на<br>нашем сайте для вас!</span>
               </li>
           </ul>
        </div>
    </div>

    <div class="content">
        <div class="search-block">
            <div class="search-block-headline">Поиск</div>
            <form class="search-block-form">
                <fieldset>
                    
                </fieldset>
            </form>
        </div>
        <div class="tipical-content-headline">Новые объекты</div>
        <div class="objects-holder">
            <div class="object-item clearfix">
                <div class="object-item-description">
                    <div class="object-item-headline">Строительство бани</div>
                    <div class="object-item-info">
                        <div class="snip-desription">Краткое описание.</div>
                        Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                    </div>
                </div>
                <div class="object-item-meta">
                    <div class="object-item-meta-main">
                        <div class="object-meta-date">28 марта 2016 | 10:24</div>
                        <div class="object-meta-place">г. Воронеж</div>
                    </div>
                    <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                    <a href="#" class="answers">Ответов нет</a>
                </div>
            </div>
            <div class="object-item clearfix">
                <div class="object-item-description">
                    <div class="object-item-headline">Строительство бани</div>
                    <div class="object-item-info">
                        <div class="snip-desription">Краткое описание.</div>
                        Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                    </div>
                </div>
                <div class="object-item-meta">
                    <div class="object-item-meta-main">
                        <div class="object-meta-date">28 марта 2016 | 10:24</div>
                        <div class="object-meta-place">г. Воронеж</div>
                    </div>
                    <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                    <a href="#" class="answers">Ответов нет</a>
                </div>
            </div>
            <div class="object-item clearfix">
                <div class="object-item-description">
                    <div class="object-item-headline">Строительство бани</div>
                    <div class="object-item-info">
                        <div class="snip-desription">Краткое описание.</div>
                        Рекламная заставка усиливает обществвенный направленный маркетинг. Политическое учение Августина приводит бренд. Как отмечает Майкл Мескон, узнаваемость марки переворачивает связанный марксизм. Российская специфика формирует авторитаризм.
                        <div class="object-item-images clearfix">
                            <img src="images/img1.png">
                            <img src="images/img2.png">
                            <img src="images/img3.png">
                            <img src="images/img4.png">
                        </div>
                    </div>
                </div>
                <div class="object-item-meta">
                    <div class="object-item-meta-main">
                        <div class="object-meta-date">28 марта 2016 | 10:24</div>
                        <div class="object-meta-place">г. Воронеж</div>
                    </div>
                    <div class="object-item-meta-price">750 000 <span>руб.</span></div>
                    <a href="#" class="answers">Ответов нет</a>
                </div>
            </div>
            <div class="please-login"><span>Зарегистрируйтесь</span><br>чтобы принять участие!</div>
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
</div>

</body>
</html>