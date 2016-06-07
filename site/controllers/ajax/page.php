<?php
switch ($_POST['method']) {
    case 'getcitiesbyregion':
        echo getCitiesByRegion();
        exit;
        break;
    default:
        echo 'ничего не найдено';
        break;
}

function getCitiesByRegion(){
    $cityes = Application::$DB->query('
        SELECT *
          FROM cities c
            WHERE c."areaID"='. $_POST['regionID'])->fetchAll();
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

