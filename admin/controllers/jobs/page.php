<?php
$checkAdmin = Application::checkAdmin();
$DB = Application::$DB;

if(!empty($_POST['delete_job'])){
    // удаляем вакансию
    $DB->prepare('DELETE FROM jobs WHERE id='. (int) $_POST['jobID'])->execute();
}

$jobs = $DB->query('
    SELECT j.*, (u.name ||\' \'|| u.surname) as user
      FROM jobs j
      LEFT JOIN users u ON j."createrUserID" = u.id
        ORDER BY j.id
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
foreach($jobs as $job){
    echo '
    <tr style="border: 1px solid black;">
        <td style="border: 1px solid black;"><strong>'. $job['id'] .'</strong></td>
        <td style="border: 1px solid black;">'. $job['name'] .'</td>
        <td style="border: 1px solid black;">'. $job['amount'] .'</td>
        <td style="border: 1px solid black;">'. $job['user'] .'</td>
            <td style="border: 1px solid black;">'. $job['description'] .'</td>
        <td><form method="POST"><input type="hidden" value="'. $job['id'] .'" name="jobID"/><input type="submit" value="Удалить" name="delete_job"></form></td>
    <tr/>';
}
echo '</table>';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */