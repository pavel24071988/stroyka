<?php
if(empty($_SESSION['user'])){
    echo 'Недостаточно прав для доступа к данной странице.';
    exit;
}

$DB = Application::$DB;
$applicationURL = Application::$URL;
if($applicationURL['2'] === 'add'){
    $main_title = 'Создать объект';
    $button_name = 'Создать';
    
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома', 'name' => 'Название', 'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'];
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
    $main_title = 'Редактировать объект';
    $button_name = 'Обновить';
    
    // обрабатываем пост здесь
    if(!empty($_POST)){
        $rows_to_check = ['amount' => 'Сумма', 'description' => 'Описание', 'house' => 'Номер дома', 'name' => 'Название', 'recomendations' => 'Рекомендации заказчику', 'require' => 'Требования'];
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

echo '<h1>'. $main_title .'</h1>';
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
        $object_imgs_arr[] = '<img src="'. $object_img['src'] .'"/>';
    }
    $object_docs = $DB->query('
        SELECT *
          FROM objects_docs oi
            WHERE oi."objectID"='. $object['id'])->fetchAll();
    foreach($object_docs as $object_doc){
        $object_docs_arr[] = '<li><a href="'. $object_doc['src'] .'"/>'. $object_doc['name'] .'</a></li>';
    }
}
?>

<?php echo Application::getLeftMenu(); ?>
<?php if(!empty($error)) echo $error; ?>
<form method="POST" action="/objects/<?php if($applicationURL['2'] === 'add') echo 'add/'; else echo $object['id'] .'/edit/'; ?>">
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
<br/>
<div style="width: 400px;"><?php echo implode(' ', $object_imgs_arr);?></div>
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