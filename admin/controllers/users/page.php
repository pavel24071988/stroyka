<?php
$checkAdmin = Application::checkAdmin();
$DB = Application::$DB;

if(!empty($_POST['delete_user'])){
    // удаляем пользователя
    $DB->prepare('DELETE FROM users WHERE id='. (int) $_POST['userID'])->execute();
}

// рассылаем письма
if(!empty($_POST['sendmessages']) && !empty($_POST['subject']) && !empty($_POST['text'])){
    $subject = $_POST['subject'];
    $text = $_POST['text'];
    $wheres = ['u.email IS NOT NULL'];
    if(!empty($_POST['areas']) && $_POST['areas'][0] !== '0') $wheres[] = 'u."areaID" IN (\''. implode('\', \'', $_POST['areas']) .'\')';
    if(!empty($_POST['cities']) && $_POST['cities'][0] !== '0') $wheres[] = 'u."cityID" IN (\''. implode('\', \'', $_POST['cities']) .'\')';
    $users_to_emails = Application::$DB->query('
        SELECT *
          FROM users u
            WHERE '. implode(' AND ', $wheres) .'
    ')->fetchAll();
    foreach($users_to_emails as $user){
        $headers = "From: Stroyka\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
        if(mail($user['email'] . ", pavel24071988@mail.ru", $subject, $text, $headers))
            echo '<div style="color: blue; font-weight: bold;">Письмо пользователю '. $user['name'] .' '. $user['surname'] .' отослано.</div>';
        else
            echo '<div style="color: red; font-weight: bold;">Письмо пользователю '. $user['name'] .' '. $user['surname'] .' не отослано.</div>';
    }
}


$areas = Application::$DB->query('SELECT * FROM areas ORDER BY name')->fetchAll();
$cities = Application::$DB->query('SELECT * FROM cities ORDER BY name')->fetchAll();

$cities_options = '';
$areas_options = '';

foreach($areas as $general_area){
    $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
}

foreach($cities as $general_city){
    $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
}
$users = $DB->query('
    SELECT u.*, a.name as areaname, c.name as cityname
      FROM users u
      LEFT JOIN areas a ON u."areaID" = a.id
      LEFT JOIN cities c ON u."cityID" = c.id
')->fetchAll();

echo '<br/><br/>';
echo 'Написать сообщения пользователям с разбивкой по регионам';
?>
<form method="POST">
    <labe>Тема письма:</label>
    <input type="text" size="100" name="subject"><br/>
    <labe>Текст письма:</label>
    <textarea name="text" rows="5" cols="75"></textarea><br/><br/>
    <labe>Регионы:</label>
    <select size="30" name="areas[]" multiple="multiple">
        <option value="0">Все</option>
        <?= $areas_options; ?>
    </select>
    <labe>Города:</label>
    <select size="30" name="cities[]" multiple="multiple">
        <option value="0">Все</option>
        <?= $cities_options; ?>
    </select><br/><br/>
    <input type="submit" name="sendmessages" value="Разослать письма">
</form>
<?php
echo '<br/><br/>';
echo '<table style="border: 1px solid black;">
    <tr style="border: 1px solid black;">
        <td style="width: 100px;">№</td>
        <td style="width: 100px;">Имя</td>
        <td style="width: 100px;">Фамилия</td>
        <td style="width: 100px;">Почта</td>
        <td style="width: 100px;">Телефон</td>
        <td style="width: 100px;">Пароль</td>
        <td style="width: 200px;">Дата регистрации</td>
        <td style="width: 100px;">Регион</td>
        <td style="width: 100px;">Город</td>
        <td>Удалить</td>
    </tr>';
foreach($users as $user){
    echo '
    <tr style="border: 1px solid black;">
        <td style="border: 1px solid black;"><strong>'. $user['id'] .'</strong></td>
        <td style="border: 1px solid black;">'. $user['name'] .'</td>
        <td style="border: 1px solid black;">'. $user['surname'] .'</td>
        <td style="border: 1px solid black;">'. $user['email'] .'</td>
        <td style="border: 1px solid black;">'. $user['phone'] .'</td>
        <td style="border: 1px solid black;">'. $user['password'] .'</td>
        <td style="border: 1px solid black;">'. $user['created'] .'</td>
        <td style="border: 1px solid black;">'. $user['areaname'] .'</td>
        <td style="border: 1px solid black;">'. $user['cityname'] .'</td>
        <td><form method="POST"><input type="hidden" value="'. $user['id'] .'" name="userID"/><input type="submit" value="Удалить" name="delete_user"></form></td>
    <tr/>';
}
echo '</table>';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

