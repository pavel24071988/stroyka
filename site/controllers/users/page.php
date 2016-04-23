<?php
$DB = Application::$DB;
$applicationURL = Application::$URL;
$user = $DB->query('
    SELECT u.*,
           (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count,
           c."name" as city_name,
           a."name" as area_name
      FROM users u
      LEFT JOIN cities c ON u."cityID" = c."id"
      LEFT JOIN areas a ON u."areaID" = a."id"
        WHERE u."id"='. $applicationURL[2])->fetch();
$type = 'my';
$check_owner = false;
if(!empty($_SESSION['user'])){
    if($_SESSION['user']['id'] == $applicationURL[2]){
        $type = empty($applicationURL[3]) ? 'my' : $applicationURL[3];
        $check_owner = true;
        $user = $_SESSION['user'];
    }
        
    $common_data = [
        'type' => $type,
        'check_owner' => $check_owner,
        'user' => $user,
        'user_from_db' => $user
    ];

    get_my_page($common_data);
}else{
    $common_data = [
        'left_menu' => '',
        'type' => 'my',
        'check_owner' => $check_owner,
        'user' => $user
    ];
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/users/'. $common_data['type'] .'.php';
}

function get_my_page($common_data){
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/users/'. $common_data['type'] .'.php';
}