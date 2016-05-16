<?php
if(empty($_SESSION['user'])){
    echo 'Недостаточно прав для доступа к данной странице.';
    exit;
}

$DB = Application::$DB;
$applicationURL = Application::$URL;
$rows_to_check = [];

$cities = Application::$DB->query('SELECT * FROM cities')->fetchAll();
$areas = Application::$DB->query('SELECT * FROM areas')->fetchAll();

$cities_options = '';
$areas_options = '';
$areas_for_object = [];

if(!empty($_POST)){
    if(!empty($_POST['del_photo'])){
        $del_photo = $DB->prepare('
            DELETE FROM objects_imgs
                WHERE "objectID"='. $applicationURL[2] .' AND
                      "src" ILIKE \''. $_POST['del_photo'] .'\'');
        if($del_photo->execute()){
            var_dump($common_data['object']);
        }
    }
}

if($applicationURL['2'] === 'add'){
    $main_title = 'Формирование объекта';
    $button_name = 'ВЫСТАВИТЬ ОБЪЕКТ';
    
    foreach($cities as $general_city){
        if(!empty($_POST['cityID']) && (int) $_POST['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
    }
    foreach($areas as $general_area){
        if(!empty($_POST['areaID']) && (int) $_POST['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
        $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
    }
    
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = [/*'amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома',*/ 'name' => 'Название', /*'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'*/];
        $errors = [];
        if(empty($_POST['recomendations'])) $_POST['recomendations'] = '';
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $create_sql = $DB->prepare('
                INSERT INTO objects (amount, cpo, "createrUserID", "dateFrom", "dateTo", description, house, name, recomendations, require, street, type_of_kind, phone, email, "areaID", "cityID")
                  VALUES(\''. $_POST['amount'] .'\',
                         \''. $_POST['cpo'] .'\',
                         \''. $_SESSION['user']['id'] .'\',
                         \''. $_POST['dateFrom'] .'\',
                         \''. $_POST['dateTo'] .'\',
                         \''. $_POST['description'] .'\',
                         \''. $_POST['house'] .'\',
                         \''. $_POST['name'] .'\',
                         \''. $_POST['recomendations'] .'\',
                         \''. $_POST['require'] .'\',
                         \''. $_POST['street'] .'\',
                         \''. $_POST['type_of_kind'] .'\',
                         \''. $_POST['phone'] .'\',
                         \''. $_POST['email'] .'\',
                         \''. $_POST['areaID'] .'\',
                         \''. $_POST['cityID'] .'\')');
            if($create_sql->execute() === true){
                $lastInsertId = $DB->lastInsertId('objects_id_seq');
                if(!empty($_POST['areas_for_object'])){
                    foreach($_POST['areas_for_object'] as $area_for_job) $DB->prepare('INSERT INTO links_kinds_of_jobs_objects ("objectID", "kindOfJobID") VALUES ('. $lastInsertId .', '. $area_for_job .')')->execute();
                }
                
                if($_FILES['object_img']['error'][0] !== 4){
                    // обработаем картинку
                    if(!file_exists("images/objects/". $lastInsertId)) mkdir("images/objects/". $lastInsertId, 0777);
                    foreach($_FILES['object_img']['name'] as $key => $img){
                        $filename = iconv("UTF-8","WINDOWS-1251", $_FILES['object_img']['name'][$key]);
                        if($_FILES['object_img']['size'][$key] / 1000000 > 3){
                            $error .= '<div style="color: red;">Не удалось загрузить файл '. $filename .', размер больше 3 Мб.</div>';
                            continue;
                        }
                        if(copy($_FILES['object_img']['tmp_name'][$key], "images/objects/". $lastInsertId ."/". $filename)){
                            $create_sql = $DB->prepare('INSERT INTO objects_imgs ("objectID", "src") VALUES(\''. $lastInsertId .'\', \''. $_FILES['object_img']['name'][$key] .'\')');
                            $create_sql->execute();
                        }
                    }
                }
                
                if($_FILES['object_doc']['error'][0] !== 4){
                    // обработаем картинку
                    if(!file_exists("data/objects/". $lastInsertId)) mkdir("data/objects/". $lastInsertId, 0777);
                    foreach($_FILES['object_doc']['name'] as $key => $img){
                        $filename = iconv("UTF-8","WINDOWS-1251", $_FILES['object_doc']['name'][$key]);
                        if($_FILES['object_doc']['size'][$key] / 1000000 > 3){
                            $error .= '<div style="color: red;">Не удалось загрузить файл '. $filename .', размер больше 3 Мб.</div>';
                            continue;
                        }
                        if(copy($_FILES['object_doc']['tmp_name'][$key], "data/objects/". $lastInsertId ."/". $filename)){
                            $create_sql = $DB->prepare('INSERT INTO objects_docs ("objectID", "src", "name") VALUES(\''. $lastInsertId .'\', \''. $_FILES['object_doc']['name'][$key] .'\', \''. $_FILES['object_doc']['name'][$key] .'\')');
                            $create_sql->execute();
                        }
                    }
                }
                
                if(empty($error)) echo '<meta http-equiv="refresh" content="1;URL=/users/'. $_SESSION['user']['id'] .'/my_objects/">';
            }else{
                $error = '<div style="color: red;">Не удалось создать, попробуйте позже.</div>';
            }
        }
    }
}else{
    $main_title = 'Редактирование';
    $button_name = 'ОТРЕДАКТИРОВАТЬ';
    
    $common_data['object'] = $DB->query('SELECT o.* FROM objects o WHERE o."id"='. $applicationURL[2])->fetch();
    $kinds_of_jobs = $DB->query('SELECT * FROM links_kinds_of_jobs_objects lkj WHERE lkj."objectID"='. $applicationURL[2])->fetchAll();
    foreach($kinds_of_jobs as $kind_of_jobs) $areas_for_object['areas_for_object'][] = $kind_of_jobs['kindOfJobID'];
    
    foreach($cities as $general_city){
        if(!empty($_POST['cityID']) && (int) $_POST['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        elseif($common_data['object']['cityID'] === $general_city['id']) $cities_options .= '<option selected value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
        $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
    }
    foreach($areas as $general_area){
        if(!empty($_POST['areaID']) && (int) $_POST['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
        elseif($common_data['object']['areaID'] === $general_area['id']) $areas_options .= '<option selected value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
        $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
    }
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        
        $rows_to_check = [/*'amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома',*/ 'name' => 'Название', /*'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'*/];
        $errors = [];
        $error = '';
        if(empty($_POST['recomendations'])) $_POST['recomendations'] = '';
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $update_check = $DB->prepare('
                UPDATE objects SET
                    "amount"=\''. $_POST['amount'] .'\',
                    "cpo"=\''. $_POST['cpo'] .'\',
                    "dateFrom"=\''. $_POST['dateFrom'] .'\',
                    "dateTo"=\''. $_POST['dateTo'] .'\',
                    "description"=\''. $_POST['description'] .'\',
                    "house"=\''. $_POST['house'] .'\',
                    "name"=\''. $_POST['name'] .'\',
                    "recomendations"=\''. $_POST['recomendations'] .'\',
                    "require"=\''. $_POST['require'] .'\',
                    "street"=\''. $_POST['street'] .'\',
                    "type_of_kind"=\''. $_POST['type_of_kind'] .'\',
                    "phone"=\''. $_POST['phone'] .'\',
                    "email"=\''. $_POST['email'] .'\',
                    "areaID"=\''. $_POST['areaID'] .'\',
                    "cityID"=\''. $_POST['cityID'] .'\'
                        WHERE "id"='. $applicationURL[2]);
            if($update_check->execute() === true){
                if(!empty($_POST['areas_for_object'])){
                    $DB->prepare('DELETE FROM links_kinds_of_jobs_objects WHERE "objectID"='. $applicationURL[2])->execute();
                    foreach($_POST['areas_for_object'] as $areas_for_object){
                        $DB->prepare('INSERT INTO links_kinds_of_jobs_objects ("objectID", "kindOfJobID") VALUES ('. $applicationURL[2] .', '. $areas_for_object .')')->execute();
                    }
                }
                
                $common_data['object'] = $DB->query('SELECT o.* FROM objects o WHERE o."id"='. $applicationURL[2])->fetch();
                
                if($_FILES['object_img']['error'][0] !== 4){
                    // обработаем картинку
                    if(!file_exists("images/objects/". $applicationURL[2])) mkdir("images/objects/". $applicationURL[2], 0777);
                    foreach($_FILES['object_img']['name'] as $key => $img){
                        $filename = iconv("UTF-8","WINDOWS-1251", $_FILES['object_img']['name'][$key]);
                        if($_FILES['object_img']['size'][$key] / 1000000 > 3){
                            $error .= '<div style="color: red;">Не удалось загрузить файл '. $filename .', размер больше 3 Мб.</div>';
                            continue;
                        }
                        if(copy($_FILES['object_img']['tmp_name'][$key], "images/objects/". $applicationURL[2] ."/". $filename)){
                            $create_sql = $DB->prepare('INSERT INTO objects_imgs ("objectID", "src") VALUES(\''. $applicationURL[2] .'\', \''. $_FILES['object_img']['name'][$key] .'\')');
                            $create_sql->execute();
                        }
                    }
                }
                
                if($_FILES['object_doc']['error'][0] !== 4){
                    // обработаем картинку
                    if(!file_exists("data/objects/". $applicationURL[2])) mkdir("data/objects/". $applicationURL[2], 0777);
                    foreach($_FILES['object_doc']['name'] as $key => $img){
                        $filename = iconv("UTF-8","WINDOWS-1251", $_FILES['object_doc']['name'][$key]);
                        if($_FILES['object_doc']['size'][$key] / 1000000 > 3){
                            $error .= '<div style="color: red;">Не удалось загрузить файл '. $filename .', размер больше 3 Мб.</div>';
                            continue;
                        }
                        if(copy($_FILES['object_doc']['tmp_name'][$key], "data/objects/". $applicationURL[2] ."/". $filename)){
                            $create_sql = $DB->prepare('INSERT INTO objects_docs ("objectID", "src", "name") VALUES(\''. $applicationURL[2] .'\', \''. $_FILES['object_doc']['name'][$key] .'\', \''. $_FILES['object_doc']['name'][$key] .'\')');
                            $create_sql->execute();
                        }
                    }
                }
                
                if(empty($error)) echo '<meta http-equiv="refresh" content="1;URL=/users/'. $_SESSION['user']['id'] .'/my_objects/">';
                
                // отправляем откликнувшимся пользователям сообщения
                $submitUsers = $DB->query('SELECT uo.* FROM users_objects uo WHERE uo."objectID"='. $applicationURL[2])->fetchAll();
                foreach($submitUsers as $user)
                    $DB->prepare('
                        INSERT INTO messages ("fromUserID", "text", "toUserID", "type", "typeID") VALUES
                        ('. $_SESSION['user']['id'] .', \'Заказ № '. $applicationURL[2] .' был изменен. Просьба проверить.\', '. $user['fromUserID'] .', \'system_object\', '. $applicationURL[2] .')
                    ')->execute();
            }else{
                $error = '<div style="color: red;">Не удалось отредактировать, попробуйте позже.</div>';
            }
        }
    }
}
?>

<?php
$object = $common_data['object'];

$creater_user = [];
$kinds_of_jobs_arr = [];
$object_imgs_arr = [];
$object_docs_arr = [];

if(!empty($object)){
    $creater_user = $DB->query('
        SELECT u.*
          FROM users u
            WHERE u."id"='. $object['createrUserID'])->fetchAll();
    $kinds_of_jobs = $DB->query('
        SELECT *
          FROM links_kinds_of_jobs_objects lkj
          LEFT JOIN kinds_of_jobs kj ON lkj."kindOfJobID" = kj."id"
            WHERE lkj."objectID"='. $object['id'])->fetchAll();
    foreach($kinds_of_jobs as $kind_of_job){
        $kinds_of_jobs_arr[] = $kind_of_job['name'];
    }
    $object_imgs = $DB->query('
        SELECT *
          FROM objects_imgs oi
            WHERE oi."objectID"='. $object['id'])->fetchAll();
    foreach($object_imgs as $object_img){
        $object_imgs_arr[] = '<img width="200px" height="200px" src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'"/><form method="POST"><input type="hidden" name="del_photo" value="'. $object_img['src'] .'"/><input type="submit" value="Удалить изображение"/></form>';
    }
    $object_docs = $DB->query('
        SELECT *
          FROM objects_docs oi
            WHERE oi."objectID"='. $object['id'])->fetchAll();
    foreach($object_docs as $object_doc){
        $object_docs_arr[] = '<a href="/data/objects/'. $object_doc['objectID'] .'/'. $object_doc['src'] .'"/>'. $object_doc['name'] .'</a><br/>';
    }
}
?>
<!--
<?php if(!empty($error)) echo $error; ?>
<form method="POST" enctype="multipart/form-data" action="/objects/<?php if($applicationURL['2'] === 'add') echo 'add/'; else echo $object['id'] .'/edit/'; ?>">
<h1>Название: <input type="text" name="name" value="<?php echo $object['name']; ?>"/></h1>
<span>Номер объекта: <?php echo $object['id']; ?></span>
<span>Опубликованно: <?php echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></span>
<hr/>
<div>Заказчик: <?php if(!empty($creater_user)) echo $creater_user[0]['surname'] .' '. $creater_user[0]['name'] .' '. $creater_user[0]['second_name']; ?></div>
<div><strong>Бюджет</strong>: <input type="text" name="amount" value="<?php echo $object['amount']; ?>"/></div>
<hr/>
<br/>
<div><strong>Виды работ</strong>: <?php echo implode(', ', $kinds_of_jobs_arr);?></div>
<div><strong>Улица</strong>: <input type="text" name="street" value="<?php echo $object['street']; ?>"/></div>
<div><strong>Дом</strong>: <input type="text" name="house" value="<?php echo $object['house']; ?>"/></div>
<br/>
<br/>
<span>Сроки: с <input type="text" name="dateFrom" value="<?php echo date('j.m.Y', strtotime($object['dateFrom'])); ?>"/> по <input type="text" name="dateTo" value="<?php echo date('j.m.Y', strtotime($object['dateTo'])); ?>"/></span>
<div>Наличие СРО и лицензий: <input type="text" name="cpo" value="<?php echo $object['cpo']; ?>"/></div>
<div>Требуемый исполнитель: 
    <select name="type_of_kind">
        <option value="0">Частный мастер</option>
        <option value="1">Бригада</option>
    </select>
<div>Требуемый стаж: <input type="text" name="require" value="<?php echo $object['require']; ?>"/></div>
<br/>
<br/>
<div style="width: 400px;">Описание объекта: <textarea name="description"><?php echo $object['description']; ?></textarea></div>
<br/>
<?php
    if(!empty($object['object_img'])){
        echo '<img width="200px" src="/images/objects/'. $object['id'] .'/'. $object['object_img'] .'"/>';
    }
?>
<p>Загрузите файл с картинкой</p>
<p><input type="file" name="object_img"></p>
<br/>
<br/>
<?php echo implode(' ', $object_imgs_arr);?>
<hr/>
<br/>
<div>Приложенные файлы</div>
<?php if(!empty($object_docs_arr)){ ?>
<div style="width: 400px;">
<ol>
    <?php echo implode(' ', $object_docs_arr);?>
</ol>
</div>
<?php } ?>
<div>Рекомендации заказчику: <textarea name="recomendations"><?php echo $object['recomendations']; ?></textarea></div>
<input type="submit" value="<?php echo $button_name; ?>"/>
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
                        <a href=""><?php if($applicationURL['2'] === 'add') echo 'Добавить объект'; else echo 'Редактировать объект'; ?></a>
                    </li>
                    </ul>
                </div>
                <div class="my-page-wrapper-content">
                    <?php if(!empty($error)) echo $error; ?>
                    <div class="my-page-wrapper-headline">
                        <span class="edit-process"><?php echo $main_title; ?>:</span><br><?php echo $object['name']; ?>
                        <?php if($applicationURL['2'] !== 'add') echo '<a href="/objects/'. $applicationURL['2'] .'/close/" class="close-edit">(Закрыть)</a>'; ?>
                    </div>
                    <form class="personal-data-form" method="POST" enctype="multipart/form-data" action="/objects/<?php if($applicationURL['2'] === 'add') echo 'add/'; else echo $object['id'] .'/edit/'; ?>">
                        <input type="hidden" name="cpo" value="<?php echo 'нет'; ?>"/>
                        <input type="hidden" name="name" value="<?php echo $object['name']; ?>"/>
                        <input type="hidden" name="require" value="<?php echo $object['require']; ?>"/>
                        <!--<select name="type_of_kind">
                            <option value="0">Частный мастер</option>
                            <option value="1">Бригада</option>
                        </select>-->
                        <input type="hidden" name="type_of_kind" value="0"/>
                        <fieldset>
                            <div class="personal-data-form-text">
                                Лемма охватывает интеграл от функции, обращающейся в бесконечность вдоль линии. Векторное поле решительно уравновешивает Наибольший Общий Делитель (НОД). Дивергенция векторного поля переворачивает криволинейный интеграл. Бином Ньютона переворачивает эмпирический график функции многих переменных, при этом, вместо 13 можно взять любую другую константу.
                            </div>
                            <div class="personal-data-form-headline red">Название</div>
                            <div class="personal-form-snippet">Примечание к бюджету. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="personal-data-row clearfix">
                                <input type="text" name="name" value="<?php echo $object['name']; ?>"><label style="color: #010101; font-size: 14px;"></label>
                            </div>
                            <div class="personal-data-form-headline red">Адрес стройплощадки:</div>
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
                                <label>улица:</label><input type="text" name="street" value="<?php echo $object['street']; ?>">
                            </div>
                            <div class="personal-data-row clearfix">
                                <label>дом:</label><input type="text" name="house" value="<?php echo $object['house']; ?>">
                            </div>
                            <br>
                            <div class="personal-data-form-headline red">Бюджет проекта</div>
                            <div class="personal-form-snippet">Примечание к бюджету. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="personal-data-row clearfix">
                                <input type="text" name="amount" value="<?php echo $object['amount']; ?>"><label style="color: #010101; font-size: 14px;">&nbsp;&nbsp;рублей</label>
                            </div>
                            <div>
                                <label class="spec"><input type="checkbox"> по договоренности</label>
                            </div>
                            <br>
                            <div class="personal-data-form-headline red">Описание объекта</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <textarea class="personal-form-textarea" name="description"><?php echo $object['description']; ?></textarea>
                            <br>
                            <div class="personal-data-form-headline red">Сфера деятельности</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <ul class="searcher-categories"><?php echo Application::getListOfAreas('object', null, $areas_for_object); ?></ul>
                            <br>
                            <div class="personal-data-form-headline">Сроки выполнения:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="personal-data-row clearfix">
                                <div class="personal-data-row-cell calendar-cell clearfix">
                                    <label>с:</label><input id="from" type="text" name="dateFrom" value="<?php echo date('j.m.Y', strtotime($object['dateFrom'])); ?>"><span class="calendar"></span>
                                </div>
                                <div class="personal-data-row-cell calendar-cell clearfix">
                                    <label>до:</label><input id="to" type="text" name="dateTo" value="<?php echo date('j.m.Y', strtotime($object['dateTo'])); ?>"><span class="calendar"></span>
                                </div>
                            </div>
                            <!--
                            <br>
                            <div class="personal-data-form-headline">Требуемые рабочие:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="worker-row clearfix">
                                <div class="worker-row-cell">
                                    Маляр (1 чел.)
                                </div>
                                <a href="#" class="worker-row-control minus"></a>
                            </div>
                            <div class="worker-row clearfix">
                                <div class="worker-row-cell clearfix">
                                    <select>
                                        <option>Маляр</option>
                                        <option>Каменщик</option>
                                        <option>Плотнег</option>
                                        <option>Токарь</option>
                                        <option>Сис. админ</option>
                                    </select>
                                    <input type="text">
                                    <span>чел.</span>
                                </div>
                                <a href="#" class="worker-row-control plus"></a>
                            </div>
                            -->
                            <br>
                            <div class="personal-data-form-headline">Контактная информация:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="personal-data-row clearfix">
                                <label>телефон:</label><input name="phone" value="<?php echo $object['phone']; ?>" type="text">
                            </div>
                            <div class="personal-data-row clearfix">
                                <label>почта:</label><input name="email" value="<?php echo $object['email']; ?>" type="text">
                            </div>
                            <br>
                            <div class="personal-data-form-headline">Прикрепить изображение:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="file_upload">
                                <button type="button" style="width: 205px;" class="tipical-button">Загрузить с компьютера</button>
                                <input type="file" name="object_img[]"  multiple='true'>
                            </div>
                            <div><?php echo implode(' ', $object_imgs_arr);?></div>
                            <br>
                            <div class="personal-data-form-headline">Прикрепить документы:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="file_upload">
                                <button type="button" style="width: 205px;" class="tipical-button">Загрузить с компьютера</button>
                                <input type="file" name="object_doc[]"  multiple='true'>
                            </div>
                            <br>
                            <div><?php echo implode(' ', $object_docs_arr);?></div>
                            <br><br>
                            <div class="personal-form-recomendation">
                                <div class="personal-form-recomendation-headline">Рекомендации</div>
                                <div>Текст</div>
                                <!--<textarea class="personal-form-textarea" name="recomendations"><?php echo $object['recomendations']; ?></textarea>-->
                            </div>
                            <button class="personal-data-form-submit" style="width: 100%;" type="submit" value="<?php echo $button_name; ?>"><?php echo $button_name; ?></button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>

    </div>