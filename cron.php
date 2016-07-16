<?php
header('Content-Type: text/html; charset=utf-8');

include($_SERVER['DOCUMENT_ROOT'] .'/functions/main.php');
$application = new Application;
$DB = Application::$DB;

$kinds_of_jobs = $DB->query('SELECT * FROM kinds_of_jobs')->fetchAll();
$users = [];
foreach($kinds_of_jobs as $kind_of_job){
	$kinds_of_jobs = $DB->query('
	SELECT u.id
      FROM users u
      LEFT JOIN users_kinds_of_jobs ukj ON u.id = ukj."userID"
	    WHERE ukj.kind_of_job_id = '. $kind_of_job['id'] .'
          ORDER BY u.sort DESC
		    LIMIT 10')->fetchAll();
	$users = array_merge($kinds_of_jobs, $users);
}
$users_sort = [];
foreach($users as $user){
	if(!empty($users_sort[$user['id']])) continue;
	$users_sort[$user['id']] = $user['id'];
}

$min_sort_value = $DB->query('SELECT MIN(sort) FROM users')->fetch();
$min_sort_value = $min_sort_value['min'] - 1;
$diff = $min_sort_value - count($users_sort);

foreach($users_sort as $key => $user){
	var_dump($key .' '. rand($min_sort_value, $diff));
	$DB->prepare('UPDATE users SET "sort"='. rand($min_sort_value, $diff) .' WHERE "id"='. $key)->execute();
}
/*
$names = ['Максим','Павел','Иван','Платон','Елисей','Денис','Сергей','Жопа','Цезарь','Марс','Марк','Энгельс','Роман','Владислав','Петюня','Жора','Жека','Кока'];
$surnames = ['Щербаков','Демидович','Мельников','Серов','Белов','Шульц','Осипов','Павлов','Сергеев','Иванов','Деревьев','Многов','Головач'];
$second_names = ['Андреевич','Сидорович','Сергеевич','Петрович','Владович','Чуйчич','Гуслич','Жуслич','Плохоч','Смешливович','Романович'];

for($i=0; $i<300; $i++){
    $names_rand = $names[rand(1, 17)];
	$surnames_rand = $surnames[rand(1, 12)];
	$second_names_rand = $second_names[rand(1, 10)];
	
	var_dump($names_rand .' '. $surnames_rand .' '. $second_names_rand);
	
	$sql = $DB->prepare('
        INSERT INTO users ("age", "areaID", "cityID", email, name, experience, status, password, second_name, surname, sort)
          VALUES('. rand(18, 105) .', '. rand(1, 4) .', '. rand(1, 13) .', \'test_'. rand(1, 6000) .'@mail.ru\', \''. $names_rand .'\', '. rand(1, 105) .', 0, \''. (rand(1, 100000)) .'\', \''. $second_names_rand .'\', \''. $surnames_rand .'\', '. $i .')
    ');
	var_dump(1);
    $sql->execute();
	$lastInsertId = $DB->lastInsertId('users_id_seq');
	var_dump(2);
	
	$kinds = rand(1, 5);
	
	for($ii=0; $ii<=$kinds; $ii++){
		$sql = $DB->prepare('
			INSERT INTO users_kinds_of_jobs (kind_of_job_id, "userID")
			  VALUES('. rand(1, 6) .', '. (int) $lastInsertId .')
		');
		var_dump(3);
		$sql->execute();
	}
}
*/