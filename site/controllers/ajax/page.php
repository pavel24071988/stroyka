<?php
switch ($_POST['method']) {
    case 'getcitiesbyregion':
        echo getCitiesByRegion();
        exit;
        break;
    case 'getphotoesforobject':
        echo json_encode(getPhotoesForObject());
        exit;
        break;
    case 'setmainphotoe':
        echo json_encode(setMainPhotoe());
        exit;
        break;
    default:
        echo 'ничего не найдено';
        break;
}

function getCitiesByRegion(){
    $regionIDs = is_array($_POST['regionID']) ? '\''. implode('\',\'', $_POST['regionID']) .'\'' : $_POST['regionID'];
    $where = 'WHERE c."areaID" IN ('. $regionIDs .')';
    if(preg_match('/\'0\'/', $regionIDs)) $where = '';
    $cityes = Application::$DB->query('
        SELECT *
          FROM cities c
            '. $where)->fetchAll();
    $cityes_str = '';
    foreach($cityes as $city){
        $cityes_str .= '<option value="'. $city['id'] .'">'. $city['name'] .'</option>';
    }
    return $cityes_str;
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function getPhotoesForObject(){
    $imgs_query = Application::$DB->query('
    SELECT *
      FROM objects_imgs
        WHERE "objectID"='. $_POST['objectID'])->fetchAll();
    $imgs = [];
    foreach($imgs_query as $img) $imgs[] = $img;
    $object = Application::$DB->query('
    SELECT *
      FROM objects
        WHERE "id"='. $_POST['objectID'])->fetch();
    return ['imgs' => $imgs, 'data' => $object];
}

function setMainPhotoe(){
    $select_sql = Application::$DB->query('
        SELECT *
          FROM objects_imgs
            WHERE "id"='. $_POST['imgID'])->fetch();
    $update_sql = Application::$DB->prepare('
        UPDATE objects_imgs SET
            "main"=\'false\'
                WHERE "objectID"='. $select_sql['objectID'])->execute();
    $update_sql = Application::$DB->prepare('
        UPDATE objects_imgs SET
            "main"=\'true\'
                WHERE "id"='. $_POST['imgID'])->execute();
    return ['code' => 1, 'status' => 'ok'];
}