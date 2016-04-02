<?php
$user = $common_data['user'];
if($common_data['check_owner']) echo '<h1>Мой поспорт</h1>';
else echo '<h1>Страница пользователя</h1>';
echo $common_data['left_menu'];
?>
<div style="width: 200px; height: 200px; border: 1px solid black;">
    <?php if(!empty($user['avatar'])) echo '<img width="200px" src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'"/>' ?>
</div>
<?php if($common_data['check_owner']){ ?>
<br/>
<a href="/users/<?php echo $user['id']; ?>/my_settings/">Загрузить фотографию</a>
<br/>
<a href="#">Написать сообщение</a>
<br/>
<a href="/users/<?php echo $user['id']; ?>/my_settings">изменить личные данные</a>
<br/>
<?php } ?>
<br/>
<div style="width: 1000px; height: 300px; border: 1px solid black;">
    <span><?php echo $user['surname'] .' '. $user['name'] .' '. $user['second_name']; ?></span>
    <?php if($common_data['check_owner']){ ?>
    <br/>
    <span><?php echo $user['status']; ?></span><a href="#">поменять статус</a>
    <?php } ?>
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