<?php
$DB = Application::$DB;
$applicationURL = Application::$URL;
$user = $DB->query('
    SELECT u.*,
           (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count
      FROM users u
        WHERE u."id"='. $applicationURL[2])->fetchAll();
$left_menu = '';
$type = 'my';
$check_owner = false;
if(!empty($_SESSION['user'])){
    $user_from_db = $user;
    $user = $_SESSION['user'];
    if($user['id'] == $applicationURL[2]){
        $left_menu = Application::getLeftMenu();
        $type = empty($applicationURL[3]) ? 'my' : $applicationURL[3];
        $check_owner = true;
        $user_from_db[0] = $user;
    }
        
    $common_data = [
        'left_menu' => $left_menu,
        'type' => $type,
        'check_owner' => $check_owner,
        'user' => $user,
        'user_from_db' => $user_from_db[0]
    ];

    get_my_page($common_data);
}else{
    $common_data = [
        'left_menu' => '',
        'type' => 'my',
        'check_owner' => $check_owner,
        'user' => $user[0]
    ];
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/users/'. $common_data['type'] .'.php';
}

function get_my_page($common_data){
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/users/'. $common_data['type'] .'.php';
}