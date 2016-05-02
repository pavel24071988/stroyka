<?php
$DB = Application::$DB;
if(isset($_POST['uploadObject'])){    
    // добавляем объект
    $object = $DB->prepare('INSERT INTO objects (name, amount, term, "createrUserID", description, type_of_kind)
                  VALUES(\''. $_POST['name'] .'\', \''. $_POST['amount'] .'\', \''. $_POST['term'] .'\', \''. $_SESSION['user']['id'] .'\', \''. $_POST['description'] .'\', \'2\')');
    if(!$object->execute()) $error = 'Произошел сбой добавления объекта';
    $objectID = $DB->lastInsertId('objects_id_seq');
    
    if(!empty($_FILES['object_img'])){
        // обработаем картинку
        if(!empty($_FILES['object_img']['tmp_name'])){
            if(!file_exists("images/objects/". $objectID)) mkdir("images/objects/". $objectID, 0777);
            if(copy($_FILES['object_img']['tmp_name'], "images/objects/". $objectID ."/". $_FILES['object_img']['name'])){
                $create_sql = $DB->prepare('INSERT INTO objects_imgs ("objectID", "src") VALUES(\''. $objectID .'\', \''. $_FILES['object_img']['name'] .'\')');
                if(!$create_sql->execute()) $error = 'Произошел сбой добавления изображения';
            }
        }
    }
}

$my_works = $DB->query('
    SELECT DISTINCT ON (r.id) id,
           r.*
      FROM (
        SELECT o.*,
               oi.src
          FROM objects o
          LEFT JOIN objects_imgs oi ON o.id = oi."objectID"
           ) as r
        WHERE r."createrUserID"='. $_SESSION['user']['id'] .'')->fetchAll();
?>
<div class="content">
    <div class="my-page-content clearfix">
        <?php if($common_data['check_owner']) echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Мои работы</a>
                    </li>
                </ul>
            </div>
            <div class="my-page-wrapper-content">
                <div class="my-works-top">
                    <p><b>Здесь вы можете показать свои работы.</b></p>
                    <br>
                    <p>Заполните поля и загрузите изображение. Далее краткий текст об ограничениях.</p>
                    <br>
                    <p>Достаточное условие сходимости, не вдаваясь в подробности, однородно создает нормальный бином Ньютона. Контрпример уравновешивает Наибольший Общий Делитель (НОД).</p>
                </div>
                <form method="POST" class="add-myworks-form clearfix" enctype="multipart/form-data">
                    <fieldset>
                        <div class="add-myworks clearfix">
                            <div class="add-myworks-left">
                                <div class="add-myworks-form-item">
                                    <div>Название объекта</div>
                                    <input type="text" name="name" class="tipical-input">
                                </div>
                                <div class="add-myworks-form-item">
                                    <div>Год сдачи</div>
                                    <input type="text" name="year" class="tipical-input">
                                </div>
                                <div class="add-myworks-form-item">
                                    <div>Стоимость</div>
                                    <input type="text" name="amount" class="tipical-input">
                                </div>
                                <div class="add-myworks-form-item">
                                    <div>Сроки в месяцах</div>
                                    <input type="text" name="term" class="tipical-input">
                                </div>
                            </div>
                            <div class="add-myworks-right">
                                <div class="file_upload">
                                    <button type="button" class="my-works-button">Загрузить изображение</button>
                                    <input type="file" name="object_img">
                                </div>
                            </div>
                        </div>
                        <div class="myworks-description clearfix">
                            <div>Ваш комментарий</div>
                            <textarea class="tipical-textarea" name="description"></textarea>
                            <button class="tipical-button" type="submit" name="uploadObject">Загрузить работу</button>
                        </div>
                    </fieldset>
                </form>
                <div class="specialist-meta-block myworks-type">
                    <div class="specialist-block-title">
                        <span>Мои работы</span>
                    </div>
                    <div class="myworks-year-holder">
                        <div class="myworks-year-title">2016</div>
                        <div class="myworks-year-items clearfix">
                            <?php foreach($my_works as $work){ ?>
                            <div class="myworks-year-item">
                                <div class="myworks-item-name"><?php echo $work['name']; ?></div>
                                <a href="<?php echo '/objects/'. $work['id'] .'/'; ?>" class="myworks-item-photo">
                                    <img width="300px" height="145px" src="<?php echo '/images/objects/'. $work['id'] .'/'. $work['src']; ?>" />
                                </a>
                                <div class="myworks-item-meta">
                                    <p><b>Стоимость:</b> <?php echo $work['amount']; ?> руб.</p>
                                    <p><b>Сроки:</b> <?php echo $work['term']; ?> месяцев</p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>