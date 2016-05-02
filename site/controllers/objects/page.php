<?php
$DB = Application::$DB;
$applicationURL = Application::$URL;
if(!empty($applicationURL[2])){
    
    if($applicationURL[2] === 'add'){
        $type = 'object_update';
        $object = null;
    }else{
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
    }
    
    $common_data = [
        'type' => $type,
        'object' => $object
    ];
    get_page($common_data);
}else{
    $sql = '
        SELECT o.*,
               (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = o.id AND c."type"=\'object_comment\') as comment_count,
               c.name as city_name
          FROM objects o
          LEFT JOIN cities c ON o."cityID" = c.id
            WHERE o.type_of_kind<>2';
    $allObjects = $DB->query($sql)->fetchAll();
    $offset = 0;
    if(!empty($_GET['pagination'])) $offset = ($_GET['pagination'] * 10) - 10;
    $sql .= ' LIMIT 10 OFFSET '. $offset;
    
    $objects = $DB->query($sql)->fetchAll();
    
    $common_data = [
        'type' => 'objects',
        'objects' => $objects,
        'allObjects' => $allObjects
    ];
    
    get_page($common_data);
}

function get_page($common_data){
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/objects/'. $common_data['type'] .'.php';
}