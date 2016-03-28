<div><a href="/">Главная</a>-><a href="/masters/">Исполнители</a>-><a href="/masters/">Воронежская область</a></div>
<h1>Мастера и компании</h1>
<?php
$DB = Application::$DB;
$users = $DB->query('SELECT u.*, (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count FROM users u')->fetchAll();
foreach($users as $user){
    
    $users_professions = $DB->query('
        SELECT *
            FROM users_professions up
            JOIN professions p ON up."professionID" = p."id"
              WHERE up."userID"='. $user['id'])->fetchAll();
    $profession_arr = [];
    foreach($users_professions as $profession){
        $profession_arr[] = $profession['name'];
    }
    
    $objects_images = $DB->query('
        SELECT *
            FROM objects o
            LEFT JOIN objects_imgs oi ON o."id" = oi."objectID"
              WHERE o."createrUserID"='. $user['id'])->fetchAll();
    
    $img_div = '<div>';
    foreach($objects_images as $image){
        $img_div .= '<img src="'. $image['src'] .'" />';
    }
    $img_div .= '</div>';
    
    $div = '<div style="border: 1px solid black;">';
    $div .= '<a href="/users/'. $user['id'] .'/">'. $user['name'] .' '. $user['surname'] .'</a><br/>';
    $div .= '<img width=100px src="'. $user['avatar'] .'"><br/>';
    $div .= $user['work_city'] .' '. implode(', ', $profession_arr) .'<br/>';
    $div .= 'На сайте: '. floor((strtotime("now") - strtotime($user['created'])) / (60*60*24)) .' дней(я)<br/>';
    $div .= 'Стаж работы: '. $user['experience'] .'<br/>';
    $div .= $user['comment_count'] .' отзывов<br/><br/>';
    
    $div .= 'Фото работ'. $img_div .'<br/><br/>';
    
    $div .= 'Цены на услуги';
    $div .= '<div>'. $user['price_description'] .'</div>';
    
    $div .= '</div>';
    echo($div);
};