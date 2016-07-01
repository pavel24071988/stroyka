<?php
$checkAdmin = Application::checkAdmin();
$DB = Application::$DB;

if(!empty($_POST['delete_object'])){
    // удаляем пользователя
    $DB->prepare('DELETE FROM objects WHERE id='. (int) $_POST['objectID'])->execute();
}

$objects = $DB->query('
    SELECT o.*, (u.name ||\' \'|| u.surname) as user
      FROM objects o
      LEFT JOIN users u ON o."createrUserID" = u.id
        ORDER BY o.id
')->fetchAll();

echo '<table style="border: 1px solid black;">
    <tr style="border: 1px solid black;">
        <td style="width: 100px;">№</td>
        <td style="width: 100px;">Имя</td>
        <td style="width: 100px;">Стоимость</td>
        <td style="width: 100px;">Кто создал</td>
        <td style="width: 100px;">Описание</td>
        <td>Удалить</td>
    </tr>';
foreach($objects as $object){
    echo '
    <tr style="border: 1px solid black;">
        <td style="border: 1px solid black;"><strong>'. $object['id'] .'</strong></td>
        <td style="border: 1px solid black;">'. $object['name'] .'</td>
        <td style="border: 1px solid black;">'. $object['amount'] .'</td>
        <td style="border: 1px solid black;">'. $object['user'] .'</td>
            <td style="border: 1px solid black;">'. $object['description'] .'</td>
        <td><form method="POST"><input type="hidden" value="'. $object['id'] .'" name="objectID"/><input type="submit" value="Удалить" name="delete_object"></form></td>
    <tr/>';
}
echo '</table>';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

