<?php
if(empty($_SESSION['user'])){
    echo 'Недостаточно прав для доступа к данной странице.';
    exit;
}

$DB = Application::$DB;
$applicationURL = Application::$URL;
$rows_to_check = [];
if($applicationURL['2'] === 'add'){
    $main_title = 'Формирование объекта';
    $button_name = 'ВЫСТАВИТЬ ОБЪЕКТ';
    
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        //$rows_to_check = ['amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома', 'name' => 'Название', 'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'];
        $errors = [];
        foreach($rows_to_check as $key => $row_to_check){
            if(empty($_POST[$key])) $errors[] = 'Не заполнено поле: '. $row_to_check;
        }
        if(!empty($errors)){
            $error = '<div style="color: red;">'. implode('<br/>', $errors) .'</div>';
        }else{
            $create_sql = $DB->prepare('
                INSERT INTO objects (amount, cpo, "createrUserID", "dateFrom", "dateTo", description, house, name, recomendations, require, street, type_of_kind)
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
                         \''. $_POST['type_of_kind'] .'\')');
            if($create_sql->execute() === true){
                $error = '<div style="color: red;">Объект создан.</div>';
            }else{
                $error = '<div style="color: red;">Не удалось создать, попробуйте позже.</div>';
            }
        }
    }
}else{
    $main_title = 'Редактирование';
    $button_name = 'ОТРЕДАКТИРОВАТЬ';
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        //$rows_to_check = ['amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома', 'name' => 'Название', 'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'];
        $errors = [];
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
                    "type_of_kind"=\''. $_POST['type_of_kind'] .'\'
                        WHERE "id"='. $applicationURL[2]);
            if($update_check->execute() === true){
                $common_data['object'] = $DB->query('SELECT o.* FROM objects o WHERE o."id"='. $applicationURL[2])->fetchAll();
                $common_data['object'] = $common_data['object'][0];
                $error = '<div style="color: red;">Объект отредактирован.</div>';
            }else{
                $error = '<div style="color: red;">Не удалось отредактировать, попробуйте позже.</div>';
            }
        }
    }
}
if(!empty($_FILES['object_img'])){
    // обработаем картинку
    if(!empty($_FILES['object_img']['tmp_name'])){
        if(!file_exists("images/objects/". $common_data['object']['id'])) mkdir("images/objects/". $common_data['object']['id'], 0777);
        if(copy($_FILES['object_img']['tmp_name'], "images/objects/". $common_data['object']['id'] ."/". $_FILES['object_img']['name'])){
            $create_sql = $DB->prepare('INSERT INTO objects_imgs ("objectID", "src") VALUES(\''. $common_data['object']['id'] .'\', \''. $_FILES['object_img']['name'] .'\')');
            $create_sql->execute();
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
        $object_imgs_arr[] = '<img width="200px" src="/images/objects/'. $object_img['objectID'] .'/'. $object_img['src'] .'"/>';
    }
    $object_docs = $DB->query('
        SELECT *
          FROM objects_docs oi
            WHERE oi."objectID"='. $object['id'])->fetchAll();
    foreach($object_docs as $object_doc){
        $object_docs_arr[] = '<a href="'. $object_doc['src'] .'"/>'. $object_doc['name'] .'</a>';
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
<?php if(!empty($error)) echo $error; ?>
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
                            <a href="#">Выставить объект</a>
                        </li>
                    </ul>
                </div>
                <div class="my-page-wrapper-content">
                    <div class="my-page-wrapper-headline">
                        <span class="edit-process"><?php echo $main_title; ?>:</span><br><?php echo $object['name']; ?>
                        <a href="#" class="close-edit"><?php if($applicationURL['2'] !== 'add') echo '(Закрыть)'; ?></a>
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
                            <div class="personal-data-form-headline red">Адрес стройплощадки:</div>
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
                            <?php if($applicationURL['2'] === 'add'){ ?>
                            <div class="personal-data-form-headline red">Сфера деятельности</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <ul class="searcher-categories"><?php echo Application::getListOfAreas('object', null); ?></ul>
                            <br>
                            <?php } ?>
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
                            <br>
                            <div class="personal-data-form-headline">Контактная информация:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="personal-data-row clearfix">
                                <label>телефон:</label><input type="text">
                            </div>
                            <div class="personal-data-row clearfix">
                                <label>почта:</label><input type="text">
                            </div>
                            <br>
                            <div class="personal-data-form-headline">Прикрепить изображение:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="file_upload">
                                <button type="button" style="width: 205px;" class="tipical-button">Загрузить с компьютера</button>
                                <input type="file" name="object_img">
                            </div>
                            <div><?php echo implode(' ', $object_imgs_arr);?></div>
                            <br>
                            <div class="personal-data-form-headline">Прикрепить документы:</div>
                            <div class="personal-form-snippet">Примечание. Подынтегральное выражение синхронизирует положительный криволинейный интеграл.</div>
                            <div class="file_upload">
                                <button type="button" style="width: 205px;" class="tipical-button">Загрузить с компьютера</button>
                                <input type="file">
                            </div>
                            <div><?php echo implode('<br/>', $object_docs_arr);?></div>
                            <br><br>
                            <div class="personal-form-recomendation">
                                <div class="personal-form-recomendation-headline">Рекомендации</div>
                                <textarea class="personal-form-textarea" name="recomendations"><?php echo $object['recomendations']; ?></textarea>
                            </div>
                            <button class="personal-data-form-submit" style="width: 100%;" type="submit" value="<?php echo $button_name; ?>"><?php echo $button_name; ?></button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>

    </div>