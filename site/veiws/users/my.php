<?php
$user = $common_data['user'];
if($common_data['check_owner']) echo '<h1>Мой поспорт</h1>';
else echo '<h1>Страница пользователя</h1>';
echo $common_data['left_menu'];
?>
<div style="width: 200px; height: 300px; border: 1px solid black;">
    werwqr
</div>
<a href="#">Загрузить фотографию</a>
<br/>
<a href="#">Написать сообщение</a>
<br/>
<br/>
<div style="width: 1000px; height: 300px; border: 1px solid black;">
    <span><?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></span>
    <br/>
    <span><?php echo $user['status']; ?></span><a href="#">поменять статус</a>
    <br/><br/>
    <a href="#">изменить личные данные</a>
    <br/><br/>
    <div><?php echo $user['age']; ?>  года</div>
    <div>Стаж работы: <?php echo $user['experience']; ?> лет</div>
    <div>Место работы: <?php echo $user['work_city']; ?></div>
    <br/>
    <?php
        $DB = Application::$DB;
        $professions = $DB->query('
            SELECT *
              FROM users_professions up
              JOIN professions p ON up."professionID" = p."id"
                WHERE up."userID"='. $user['id'])->fetchAll();
        $professions_str = [];
        foreach($professions as $profession){
            $professions_str[] = $profession['name'];
        }
    ?>
    
    <div>Специализации: <?php echo implode(', ', $professions_str); ?></div>
</div>