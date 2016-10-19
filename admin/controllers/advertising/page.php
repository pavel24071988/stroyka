<?php
$checkAdmin = Application::checkAdmin();
$URL = Application::$URL;
$DB = Application::$DB;

if(empty($_GET['update'])){
    if(!empty($_POST['delete_advertising'])){
        // удаляем рекламу
        $DB->prepare('DELETE FROM advertising WHERE id='. (int) $_POST['advertisingID'])->execute();
    }
    $advertisings = $DB->query('SELECT * FROM advertising')->fetchAll();
    echo '<a href="/admin/advertising/?update=new">Добавить</a>';
    echo '<table style="border: 1px solid black;">
        <tr style="border: 1px solid black;">
            <td style="width: 100px;">№</td>
            <td style="width: 100px;">Описание</td>
            <td style="width: 50px;">Картинка</td>
            <td style="width: 100px;">Время включения</td>
            <td style="width: 100px;">Время выключения</td>
            <td style="width: 100px;">Правка</td>
            <td style="width: 100px;">Удалить</td>
        </tr>';
    foreach($advertisings as $advertising){
        echo '
        <tr style="border: 1px solid black;">
            <td style="border: 1px solid black;"><strong>'. $advertising['id'] .'</strong></td>
            <td style="border: 1px solid black;">'. $advertising['description'] .'</td>
            <td style="border: 1px solid black;"><img src="/images/advertisings/'. $advertising['id'] .'/'. $advertising['src'] .'"></td>
            <td style="border: 1px solid black;">'. $advertising['switchon'] .'</td>
            <td style="border: 1px solid black;">'. $advertising['switchoff'] .'</td>
            <td><a href="?update='. $advertising['id'] .'">править</a></td>
            <td><form method="POST"><input type="hidden" value="'. $advertising['id'] .'" name="advertisingID"/><input type="submit" value="Удалить" name="delete_advertising"></form></td>
        <tr/>';
    }
    echo '</table>';
}else{
    
    // пришёл пост добавляем либо обновляем
    if(!empty($_POST['update'])){
        if(empty($_POST['repeat'])) $_POST['repeat'] = 'off';
        if(empty($_POST['repeat_minutes'])) $_POST['repeat_minutes'] = 0;
        $_POST['switchon'] = explode('/', $_POST['switchon']);
        $_POST['switchon'] = $_POST['switchon'][1] .'.'. $_POST['switchon'][0] .'.'. $_POST['switchon'][2];
        $_POST['switchoff'] = explode('/', $_POST['switchoff']);
        $_POST['switchoff'] = $_POST['switchoff'][1] .'.'. $_POST['switchoff'][0] .'.'. $_POST['switchoff'][2];

        if($_POST['id'] === 'new'){
            $sql = $DB->prepare('
                INSERT INTO advertising (description, repeat, repeat_minutes, switchon, switchoff, type, img_banner_link)
                  VALUES(\''. $_POST['description'] .'\', \''. $_POST['repeat'] .'\', \''. $_POST['repeat_minutes'] .'\', \''. $_POST['switchon'] .'\', \''. $_POST['switchoff'] .'\', \''. $_POST['type'] .'\', \''. $_POST['img_banner_link'] .'\')
            ');
            var_dump('INSERT INTO advertising (description, repeat, repeat_minutes, switchon, switchoff, type, img_banner_link) VALUES(\''. $_POST['description'] .'\', \''. $_POST['repeat'] .'\', \''. $_POST['repeat_minutes'] .'\', \''. $_POST['switchon'] .'\', \''. $_POST['switchoff'] .'\', \''. $_POST['type'] .'\', \''. $_POST['img_banner_link'] .'\')');
            exit;
            if($sql->execute()) $error[] = 'Баннер добавлен.';
            else  $error[] = 'Произошла ошибка добавления.';
            $advirtisingID = $DB->lastInsertId('advertising_id_seq');
        }else{
            $advirtisingID = $_POST['id'];
            $sql = $DB->prepare('
                UPDATE advertising
                  SET description=\''. $_POST['description'] .'\',
                      repeat=\''. $_POST['repeat'] .'\',
                      repeat_minutes=\''. $_POST['repeat_minutes'] .'\',
                      switchon=\''. $_POST['switchon'] .'\',
                      switchoff=\''. $_POST['switchoff'] .'\',
                      type=\''. $_POST['type'] .'\',
                      img_banner_link=\''. $_POST['img_banner_link'] .'\'
                    WHERE id='. $advirtisingID
            );
            if($sql->execute()) $error[] = 'Баннер обновлен.';
            else  $error[] = 'Произошла ошибка обновления.';
        }
        
        // области
        if(!empty($_POST['areas'])){
            $sql = $DB->prepare('DELETE FROM links_areas_advertising WHERE advertising_id='. $advirtisingID)->execute();
            foreach($_POST['areas'] as $area){
                $sql = $DB->prepare('INSERT INTO links_areas_advertising (advertising_id, area_id) VALUES('. $advirtisingID .', '. $area .')')->execute();
            }
        }

        // города
        if(!empty($_POST['cities'])){
            $sql = $DB->prepare('DELETE FROM links_cities_advertising WHERE advertising_id='. $advirtisingID)->execute();
            foreach($_POST['cities'] as $city){
                $sql = $DB->prepare('INSERT INTO links_cities_advertising (advertising_id, city_id) VALUES('. $advirtisingID .', '. $city .')')->execute();
            }
        }
        
        if(!empty($_FILES['banner_img']['tmp_name'])){
            if(!file_exists("images/advertisings/". $advirtisingID)) mkdir("images/advertisings/". $advirtisingID, 0777);
            if(Application::resize($_FILES['banner_img']['tmp_name'], "images/advertisings/". $advirtisingID ."/". $_FILES['banner_img']['name'], 500, 0)){
                $update_banner_img = $DB->prepare('UPDATE advertising SET "src"=\''. $_FILES['banner_img']['name'] .'\' WHERE "id"='. $advirtisingID);
                if($update_banner_img->execute() === true) $error[] = 'Фотография загружена.';
                else $error[] = 'Не удалось загрузить фотография.';
            }
        }
    }
    
    if(!empty($error)) echo '<div style="color: red;">'. implode('<br/>', $error) .'</div>';
    
    $advertisingID = $_GET['update'];
    if($_GET['update'] === 'new') $advertisingID = 0;
    
    $advertising = $DB->query('SELECT * FROM advertising WHERE id='. $advertisingID)->fetch();
    $areas = $DB->query('SELECT * FROM areas')->fetchAll();
    $areasChoose = $DB->query('SELECT * FROM links_areas_advertising WHERE advertising_id='. $advertisingID)->fetchAll();
    $areasOptions = [];
    $selected = '';
    foreach($areasChoose as $_area){ if($_area['area_id'] === -1) $selected = 'selected'; }
    $areasOptions[] = '<option value="-1" '. $selected .'>Все</option>';
    foreach($areas as $key => $area){
        $selected = '';
        foreach($areasChoose as $_area){ if($_area['area_id'] === $area['id']) $selected = 'selected'; }
        $areasOptions[] = '<option value="'. $area['id'] .'" '. $selected .'>'. $area['name'] .'</option>';
    }
    $cities = $DB->query('SELECT * FROM cities')->fetchAll();
    $citiesChoose = $DB->query('SELECT * FROM links_cities_advertising WHERE advertising_id='. $advertisingID)->fetchAll();
    $citiesOptions = [];
    $selected = '';
    foreach($citiesChoose as $_city){ if($_city['city_id'] === -1) $selected = 'selected'; }
    $citiesOptions[] = '<option value="-1" '. $selected .'>Все</option>';
    foreach($cities as $key => $city){
        $selected = '';
        foreach($citiesChoose as $_city){ if($_city['city_id'] === $city['id']) $selected = 'selected'; }
        $citiesOptions[] = '<option value="'. $city['id'] .'" '. $selected .'>'. $city['name'] .'</option>';
    }
?>
<br/>
<form action="?update=<?= $_GET['update']; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $_GET['update']; ?>">
    <p>Тип: 
        <select name="type">
            <option value="banner"<?php if(!empty($_POST['type']) && $_POST['type'] === 'banner') echo 'selected'; ?>>Баннер</option>
            <option value="text"<?php if(!empty($_POST['type']) && $_POST['type'] === 'text') echo 'selected'; ?>>Текст</option>
        </select>
    </p>
    <p>Описание/Текст: <textarea name="description"><?= $advertising['description'] ?></textarea></p>
    <p>Время включения: <input type="text" id="switchon" name="switchon" value="<?= date('m/d/Y', strtotime($advertising['switchon'])); ?>"></p>
    <p>Время выключения: <input type="text" id="switchoff" name="switchoff" value="<?= date('m/d/Y', strtotime($advertising['switchoff'])); ?>"></p>
    <p>Области: <br/><select size="15" multiple="multiple" name="areas[]"><?= implode('', $areasOptions); ?></select></p>
    <p>Города: <br/><select size="15" multiple="multiple" name="cities[]"><?= implode('', $citiesOptions); ?></select></p>
    <p>Повторять: <input type="checkbox" <?php if($advertising['repeat']) echo 'checked'; ?> name="repeat"></p>
    <p>Каждые: <input type="text" name="repeat_minutes"> минут</p>
    <p><img src="/images/advertisings/<?= $advertising['id'] .'/'. $advertising['src'] ?>"></p>
    <p>Ссылка с изображения: <input type="text" name="img_banner_link"></p>
    <p>
        Картинка:
        <div class="file_upload">
            <button type="button" class="tipical-button">Загрузить с компьютера</button>
            <input type="file" id="add_photo_save" name="banner_img">
        </div>
    </p>
    <input type="submit" name="update" value="<?php if($_GET['update'] === 'new') echo 'Создать'; else echo 'Обновить'; ?>">
</form>
<?php } ?>
