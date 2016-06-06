<?php
$checkAdmin = Application::checkAdmin();
$DB = Application::$DB;

if(!empty($_POST['delete_user'])){
    // удаляем пользователя
    $DB->prepare('DELETE FROM users WHERE id='. (int) $_POST['userID'])->execute();
}

$users = $DB->query('SELECT * FROM users')->fetchAll();

echo '<table style="border: 1px solid black;">
    <tr style="border: 1px solid black;">
        <td style="width: 100px;">№</td>
        <td style="width: 100px;">Имя</td>
        <td style="width: 100px;">Фамилия</td>
        <td>Удалить</td>
    </tr>';
foreach($users as $user){
    echo '
    <tr style="border: 1px solid black;">
        <td style="border: 1px solid black;"><strong>'. $user['id'] .'</strong></td>
        <td style="border: 1px solid black;">'. $user['name'] .'</td>
        <td style="border: 1px solid black;">'. $user['surname'] .'</td>
        <td><form method="POST"><input type="hidden" value="'. $user['id'] .'" name="userID"/><input type="submit" value="Удалить" name="delete_user"></form></td>
    <tr/>';
}
echo '</table>';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

