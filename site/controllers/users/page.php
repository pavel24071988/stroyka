<?php
$DB = Application::$DB;
$applicationURL = Application::$URL;
$user = $DB->query('
    SELECT u.*,
           (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count
      FROM users u
        WHERE u."id"='. $applicationURL[2])->fetchAll();
if(!empty($_SESSION['user'])){
    $user = $_SESSION['user'];
    if($user['id'] == $applicationURL[2]){
        $left_menu = '<div>
            <div><a href="/users/'. $user['id'] .'/">Мой паспорт</a></div>
            <div><a href="/users/'. $user['id'] .'/my_objects/">Объекты и вакансии</a></div>
            <div><a href="/users/'. $user['id'] .'/my_messages/">Сообщения</a></div>
            <div><a href="/users/'. $user['id'] .'/my_settings/">Настройки</a></div>
            <div><a href="/users/'. $user['id'] .'/my_works/">Мои работы</a></div>
            <div><a href="/users/'. $user['id'] .'/my_insurance/">Страхование</a></div>
            <div><a href="/users/'. $user['id'] .'/my_low/">Юридические услуги</a></div>
        </div>';
        
        if(empty($applicationURL[3])) $applicationURL[3] = 'my';
        
        $common_data = [
            'left_menu' => $left_menu,
            'type' => $applicationURL[3],
            'check_owner' => true,
            'user' => $user[0]
        ];
        
        get_my_page($common_data);
    }
}else{
    $common_data = [
        'left_menu' => '',
        'type' => 'my',
        'check_owner' => false,
        'user' => $user[0]
    ];
    require_once '/site/veiws/users/'. $common_data['type'] .'.php';
}

function get_my_page($common_data){
    require_once '/site/veiws/users/'. $common_data['type'] .'.php';
}