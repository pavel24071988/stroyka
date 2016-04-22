<?php
if(empty($_SESSION['user'])){
    echo 'Недостаточно прав для доступа к данной странице.';
    exit;
}

$DB = Application::$DB;
$applicationURL = Application::$URL;
$job = $common_data['job'];

if($applicationURL['2'] === 'add'){
    $main_title = 'Добавить вакансию';
    $button_name = 'Создать';
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['description' => 'Обязанности', 'house' => 'Номер дома', 'name' => 'Название', 'require' => 'Требования'];
        $errors = [];
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $create_sql = $DB->prepare('
                INSERT INTO jobs (amount, bargain, "createrUserID", conditions, description, house, name, recomendations, require, street, "scheduleID")
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
                         \''. $_POST['scheduleID'] .'\')');
            if($create_sql->execute() === true){
                $error = '<div style="color: red;">Вакансия создана.</div>';
            }else{
                $error = '<div style="color: red;">Не удалось создать, попробуйте позже.</div>';
            }
        }
    }
}else{
    $main_title = '<span class="edit-process">Редактирование:</span><br>'. $job['name'] .'<a href="#" class="close-edit">(Закрыть)</a>';
    $button_name = 'Обновить';
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['description' => 'Обязанности', 'house' => 'Номер дома', 'name' => 'Название', 'require' => 'Требования'];
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
                    "scheduleID"=\''. $_POST['type_of_kind'] .'\'
                        WHERE "id"='. $applicationURL[2]);
            if($update_check->execute() === true){
                $common_data['job'] = $DB->query('SELECT j.* FROM jobs j WHERE j."id"='. $applicationURL[2])->fetchAll();
                $common_data['job'] = $common_data['job'][0];
                $error = '<div style="color: red;">Вакансия отредактирована.</div>';
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
                        <a href="#">Объекты и вакансии</a>
                    </li>
                    <li>
                        <a href="#">Добавить вакансию</a>
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
                        <div class="personal-data-form-headline red">Место работы</div>
                        <div class="personal-form-snippet">Примечание к адресу. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                        <div class="personal-data-row clearfix">
                            <label class="red">область:</label>
                            <select>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                            </select>
                        </div>
                        <div class="personal-data-row clearfix">
                            <label class="red">город:</label>
                            <select>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
                                <option>Воронежская обл.</option>
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
                        <ul class="searcher-categories"><?php echo Application::getListOfAreas('job', null); ?></ul>
                        <div class="personal-form-recomendation">
                            <div class="personal-form-recomendation-headline">Рекомендации</div><textarea name="recomendations" class="personal-form-textarea"><?php echo $job['recomendations']; ?></textarea></div>
                        <button class="personal-data-form-submit" style="width: 100%;" type="submit">Добавить вакансию</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>

</div>

