<h1>мастера и компании</h1>
<?php
$DB = Application::$DB;
$users = $DB->query('SELECT u.*, (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'comment\') as comment_count FROM users u')->fetchAll();
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
    
    $users_professions = $DB->query('
        SELECT *
            FROM users_professions up
            JOIN professions p ON up."professionID" = p."id"
              WHERE up."userID"='. $user['id'])->fetchAll();
    
    $div = '<div style="border: 1px solid black;">';
    $div .= $user['name'] .' '. $user['surname'] .'<br/>';
    $div .= '<img width=100px src="'. $user['avatar'] .'"><br/>';
    $div .= $user['work_city'] .' '. implode(', ', $profession_arr) .'<br/>';
    $div .= 'На сайте: '. floor((strtotime("now") - strtotime($user['created'])) / (60*60*24)) .' дней(я)<br/>';
    $div .= 'Стаж работы: '. $user['experience'] .'<br/>';
    $div .= $user['comment_count'] .' отзывов';
    $div .= '</div>';
    echo($div);
};