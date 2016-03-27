<div><a href="/">Главная</a>-><a href="/orders/">Заказы</a>-><a href="/orders/">Воронежская область</a>-><a href="/orders/">Воронеж</a></div>
<?php
$DB = Application::$DB;
$applicationURL = Application::$URL;
if(!empty($applicationURL[2])){
    
    if($applicationURL[2] === 'add'){
        $common_data = [
            'type' => 'object_update',
            'object' => null
        ];
        get_object_page($common_data);
    }
    
    $object = $DB->query('SELECT o.* FROM objects o WHERE o."id"='. $applicationURL[2])->fetchAll();
    
    $type = 'object_page';
    $object = $object[0];
    
    if(!empty($applicationURL[3])){
        switch($applicationURL[3]){
            case 'edit':
                $type = 'object_update';
            break;
            case 'delete':
                $type = 'object_delete';
            break;
        }
    }

    $common_data = [
        'type' => $type,
        'object' => $object
    ];
    get_object_page($common_data);
}else{
    $objects = $DB->query('SELECT o.*, (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = o.id AND c."type"=\'object_comment\') as comment_count FROM objects o')->fetchAll();
    
    $common_data = [
        'type' => 'objects',
        'objects' => $objects
    ];
    
    get_object_page($common_data);
}

function get_object_page($common_data){
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/objects/'. $common_data['type'] .'.php';
    exit;
}