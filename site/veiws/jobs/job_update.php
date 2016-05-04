<?php
if(empty($_SESSION['user'])){
    echo 'Недостаточно прав для доступа к данной странице.';
    exit;
}

$DB = Application::$DB;
$applicationURL = Application::$URL;
$job = $common_data['job'];

$cities = Application::$DB->query('SELECT * FROM cities')->fetchAll();
$areas = Application::$DB->query('SELECT * FROM areas')->fetchAll();

$cities_options = '';
$areas_options = '';
$areas_for_job = [];

if($applicationURL['2'] === 'add'){
    $main_title = 'Добавить вакансию';
    $button_name = 'Создать';
    
    foreach($cities as $general_city){
        if(!empty($_POST['cityID']) && (int) $_POST['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
    }
    foreach($areas as $general_area){
        if(!empty($_POST['areaID']) && (int) $_POST['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';;
        $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
    }
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['description' => 'Обязанности', 'house' => 'Номер дома', 'name' => 'Название',/* 'require' => 'Требования'*/];
        if(empty($_POST['bargain'])) $_POST['bargain'] = 'off';
        if(empty($_POST['require'])) $_POST['require'] = '';
        if(empty($_POST['conditions'])) $_POST['conditions'] = '';
        $errors = [];
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $create_sql = $DB->prepare('
                INSERT INTO jobs (amount, bargain, "createrUserID", conditions, description, house, name, recomendations, require, street, "scheduleID", "areaID", "cityID")
                  VALUES(\''. $_POST['amount'] .'\',
                         \''. $_POST['bargain'] .'\',
                         \''. $_SESSION['user']['id'] .'\',
                         \''. $_POST['conditions'] .'\',
                         \''. $_POST['description'] .'\',
                         \''. $_POST['house'] .'\',
                         \''. $_POST['name'] .'\',
                         \''. $_POST['recomendations'] .'\',
                         \''. $_POST['require'] .'\',
                         \''. $_POST['street'] .'\',
                         \''. $_POST['scheduleID'] .'\',
                         \''. $_POST['areaID'] .'\',
                         \''. $_POST['cityID'] .'\')');
            if($create_sql->execute() === true){
                $lastInsertId = $DB->lastInsertId('jobs_id_seq');
                if(!empty($_POST['areas_for_job'])){
                    foreach($_POST['areas_for_job'] as $area_for_job) $DB->prepare('INSERT INTO links_kinds_of_jobs_jobs ("jobID", "kindOfJobID") VALUES ('. $lastInsertId .', '. $area_for_job .')')->execute();
                }
                $error = '<div style="color: red;">Вакансия создана.</div>';
                echo '<meta http-equiv="refresh" content="1;URL=/jobs/'. $lastInsertId .'/">';
            }else{
                $error = '<div style="color: red;">Не удалось создать, попробуйте позже.</div>';
            }
        }
    }
}else{
    $main_title = '<span class="edit-process">Редактирование:</span><br>'. $job['name'] .'<a href="/jobs/'. $job['id'] .'/close/" class="close-edit">(Закрыть)</a>';
    $button_name = 'Обновить';
    
    $common_data['job'] = $DB->query('SELECT j.* FROM jobs j WHERE j."id"='. $applicationURL[2])->fetch();
    $kinds_of_jobs = $DB->query('SELECT * FROM links_kinds_of_jobs_jobs lkj WHERE lkj."jobID"='. $applicationURL[2])->fetchAll();
    foreach($kinds_of_jobs as $kind_of_jobs) $areas_for_job['areas_for_job'][] = $kind_of_jobs['kindOfJobID'];
    
    foreach($cities as $general_city){
        if(!empty($_POST['cityID']) && (int) $_POST['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        elseif($common_data['job']['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
    }
    foreach($areas as $general_area){
        if(!empty($_POST['areaID']) && (int) $_POST['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
        elseif($common_data['job']['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
        $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
    }
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['description' => 'Обязанности', 'house' => 'Номер дома', 'name' => 'Название',/* 'require' => 'Требования'*/];
        if(empty($_POST['require'])) $_POST['require'] = '';
        if(empty($_POST['cpo'])) $_POST['cpo'] = 'off';
        if(empty($_POST['dateTo'])) $_POST['dateTo'] = '';
        $errors = [];
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $update_check = $DB->prepare('
                UPDATE jobs SET
                    "amount"=\''. $_POST['amount'] .'\',
                    "bargain"=\''. $_POST['cpo'] .'\',
                    "conditions"=\''. $_POST['dateTo'] .'\',
                    "description"=\''. $_POST['description'] .'\',
                    "house"=\''. $_POST['house'] .'\',
                    "name"=\''. $_POST['name'] .'\',
                    "recomendations"=\''. $_POST['recomendations'] .'\',
                    "require"=\''. $_POST['require'] .'\',
                    "street"=\''. $_POST['street'] .'\',
                    "scheduleID"=\''. $_POST['scheduleID'] .'\',
                    "areaID"=\''. $_POST['areaID'] .'\',
                    "cityID"=\''. $_POST['cityID'] .'\'
                        WHERE "id"='. $applicationURL[2]);
            if($update_check->execute() === true){
                if(!empty($_POST['areas_for_job'])){
                    $DB->prepare('DELETE FROM links_kinds_of_jobs_jobs WHERE "jobID"='. $applicationURL[2])->execute();
                    foreach($_POST['areas_for_job'] as $area_for_job) $DB->prepare('INSERT INTO links_kinds_of_jobs_jobs ("jobID", "kindOfJobID") VALUES ('. $applicationURL[2] .', '. $area_for_job .')')->execute();
                }
                
                $job = $DB->query('SELECT j.* FROM jobs j WHERE j."id"='. $applicationURL[2])->fetch();
                $main_title = '<span class="edit-process">Редактирование:</span><br>'. $job['name'] .'<a href="/jobs/'. $job['id'] .'/close/" class="close-edit">(Закрыть)</a>';
                $error = '<div style="color: red;">Вакансия отредактирована.</div>';
                
                // отправляем откликнувшимся пользователям сообщения
                $submitUsers = $DB->query('SELECT uj.* FROM users_jobs uj WHERE uj."jobID"='. $applicationURL[2])->fetchAll();
                foreach($submitUsers as $user)
                    $DB->prepare('
                        INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
                        ('. $_SESSION['user']['id'] .', \'Вакансия № '. $applicationURL[2] .' была изменена. Просьба проверить.\', '. $user['fromUserID'] .', \'system_job\', '. $applicationURL[2] .')
                    ')->execute();
            }else{
                $error = '<div style="color: red;">Не удалось отредактировать, попробуйте позже.</div>';
            }
        }
    }
}?>

<?php
$creater_user = [];
$kinds_of_jobs_arr = [];
$schedules_arr = [];
$schedules = $DB->query('
    SELECT *
      FROM schedules s
')->fetchAll();
foreach($schedules as $schedule){
    $schedules_arr[] = '<option value="'. $schedule['id'] .'">'. $schedule['name'] .'</option>';
}
if(!empty($job)){
    $creater_user = $DB->query('
        SELECT u.*
          FROM users u
            WHERE u."id"='. $job['createrUserID'])->fetchAll();
    $kinds_of_jobs = $DB->query('
    SELECT *
      FROM links_kinds_of_jobs_objects lkj
      LEFT JOIN kinds_of_jobs kj ON lkj."kindOfJobID" = kj."id"
        WHERE lkj."objectID"='. $job['id'])->fetchAll();
    foreach($kinds_of_jobs as $kind_of_job){
        $kinds_of_jobs_arr[] = $kind_of_job['name'];
    }
}
?>

<!--
<h1>Название: <input type="text" name="name" value="<?php //echo $job['name']; ?>"/></h1>
<span>Номер вакансии: <?php //echo $job['id']; ?></span>
<span>Опубликованно: <?php //echo date('j.m.Y H:i:s', strtotime($job['created'])); ?></span>
<hr/>
<div>Заказчик: <?php //if(!empty($creater_user)) echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<hr/>
<br/>
<div><strong>Виды работ</strong>: <?php //echo implode(', ', $kinds_of_jobs_arr);?></div>
<div><strong>Улица</strong>: <input type="text" name="street" value="<?php //echo $job['street']; ?>"/></div>
<div><strong>Дом</strong>: <input type="text" name="house" value="<?php //echo $job['house']; ?>"/></div>
<br/>
Зарплата:
<input type="checkbox" name="bargain" <?php //if($job['bargain']) echo 'checked'; ?>/> По договоренности
<br/>
Оклад:
<input type="text" name="amount" value="<?php //echo $job['amount']; ?>"/>
<br/>
График работы:
<select name="scheduleID">
<?php //echo implode(', ', $schedules_arr); ?>
</select>
<br/>
<div>Требования: <textarea name="require"><?php //echo $job['require']; ?></textarea></div>
<div>Обязанности: <textarea name="description"><?php //echo $job['description']; ?></textarea></div>
<div>Условия: <textarea name="conditions"><?php //echo $job['conditions']; ?></textarea></div>
<div>Рекомендации заказчику: <textarea name="recomendations"><?php //echo $job['recomendations']; ?></textarea></div>
<input type="submit" value="<?php //echo $button_name; ?>"/>
</form>
-->
<div class="content">

    <div class="my-page-content clearfix">
        <?php echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="/users/<?php echo $_SESSION['user']['id']; ?>/my_objects/">Объекты и вакансии</a>
                    </li>
                    <li>
                        <a href=""><?php if($applicationURL['2'] === 'add') echo 'Добавить вакансию'; else echo 'Редактировать вакансию'; ?></a>
                    </li>
                </ul>
            </div>
            <div class="my-page-wrapper-content">
                <div class="my-page-wrapper-headline"><?php echo $main_title; ?></div>
                <form class="personal-data-form" method="POST" action="/jobs/<?php if($applicationURL['2'] === 'add') echo 'add/'; else echo $job['id'] .'/edit/'; ?>">
                    <?php if(!empty($error)) echo $error; ?>
                    <fieldset>
                        <div class="personal-data-form-headline red">Обязательные параметры выделены красным</div>
                        <div class="personal-data-form-text">
                            Лемма охватывает интеграл от функции, обращающейся в бесконечность вдоль линии. Векторное поле решительно уравновешивает Наибольший Общий Делитель (НОД). Дивергенция векторного поля переворачивает криволинейный интеграл. Бином Ньютона переворачивает эмпирический график функции многих переменных, при этом, вместо 13 можно взять любую другую константу.
                        </div>
                        <div class="personal-data-form-headline red">Название</div>
                        <div class="personal-form-snippet">Примечание к бюджету. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <div class="personal-data-row clearfix">
                            <input type="text" name="name" value="<?php echo $job['name']; ?>"><label style="color: #010101; font-size: 14px;"></label>
                        </div>
                        <div class="personal-data-form-headline red">Место работы</div>
                        <div class="personal-form-snippet">Примечание к адресу. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <div class="personal-data-row clearfix">
                            <label class="red">область:</label>
                            <select name="areaID">
                                <?php if(!empty($area)) echo '<option value="'. $area['id'] .'">'. $area['name'] .'</option>'; ?>
                                <?php echo $areas_options; ?>
                            </select>
                        </div>
                        <div class="personal-data-row clearfix">
                            <label class="red">город:</label>
                            <select name="cityID">
                                <?php if(!empty($city)) echo '<option value="'. $city['id'] .'">'. $city['name'] .'</option>'; ?>
                                <?php echo $cities_options; ?>
                            </select>
                        </div>
                        <div class="personal-data-row clearfix">
                            <label>улица:</label><input type="text" name="street" value="<?php echo $job['street']; ?>">
                        </div>
                        <div class="personal-data-row clearfix">
                            <label>дом:</label><input type="text" name="house" value="<?php echo $job['house']; ?>">
                        </div>
                        <br>
                        <div class="personal-data-form-headline red">Зарпата</div>
                        <div class="personal-form-snippet">Примечание к бюджету. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <div class="personal-data-row clearfix">
                            <input type="text" name="amount" value="<?php echo $job['amount']; ?>"><label style="color: #010101; font-size: 14px;">&nbsp;&nbsp;рублей</label>
                        </div>
                        <div>
                            <label class="spec"><input type="checkbox" name="bargain" <?php if($job['bargain']) echo 'checked'; ?>> по договоренности</label>
                        </div>
                        <br>
                        <div class="personal-data-form-headline red">График работы</div>
                        <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <div class="personal-data-row clearfix">
                            <select name="scheduleID">
                            <?php echo implode(', ', $schedules_arr); ?>
                            </select>
                        </div>
                        <br>
                        <div class="personal-data-form-headline red">Описание вакансии</div>
                        <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <textarea class="personal-form-textarea" name="description"><?php echo $job['description']; ?></textarea>
                        <br>
                        <div class="personal-data-form-headline red">Сфера деятельности</div>
                        <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <ul class="searcher-categories"><?php echo Application::getListOfAreas('job', null, $areas_for_job); ?></ul>
                        <div class="personal-form-recomendation">
                            <div class="personal-form-recomendation-headline">Рекомендации</div><textarea name="recomendations" class="personal-form-textarea"><?php echo $job['recomendations']; ?></textarea></div>
                        <button class="personal-data-form-submit" style="width: 100%;" type="submit"><?php if($applicationURL['2'] === 'add') echo 'Добавить вакансию'; else echo 'Редактировать вакансию'; ?></button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

</div>

