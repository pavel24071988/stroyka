<?php
$DB = Application::$DB;

// начинаем выстраивать функционал поиска
if(isset($_GET['search']) && $_GET['search'] === 'true'){    
    if(!empty($_GET['cityID'])) $city = Application::$DB->query('SELECT * FROM cities WHERE id='. $_GET['cityID'])->fetch();
    if(!empty($_GET['areaID'])) $area = Application::$DB->query('SELECT * FROM areas WHERE id='. $_GET['areaID'])->fetch();
}

$cities = Application::$DB->query('SELECT * FROM cities')->fetchAll();
$areas = Application::$DB->query('SELECT * FROM areas')->fetchAll();

$cities_options = '';
$areas_options = '';

foreach($cities as $general_city){
    if(!empty($city) && $city['id'] === $general_city['id']) continue;
    $cities_options .= '<option value="'. $general_city['id'] .'">'. $general_city['name'] .'</option>';
}
foreach($areas as $general_area){
    if(!empty($area) && $area['id'] === $general_area['id']) continue;
    $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
}

$dopSQL = [];

$areaSelect = '';
$areaLeftJoin = '';
$areaWhere = '';
if(!empty($_GET['areas_for_job'])){
    $areaSelect = ' ukj.kind_of_job_id,';
    $areaLeftJoin = ' LEFT JOIN users_kinds_of_jobs ukj ON u.id = ukj."userID"';
    $dopSQL[] = 'r.kind_of_job_id IN (\''. implode('\', \'', $_GET['areas_for_job']) .'\')';
}

$sql = '
    SELECT r.*
      FROM (SELECT u.*, '. $areaSelect .'
                   (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\') as comment_count,
                   (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = u.id AND c."type"=\'user_comment\' AND c."positive_negative"=\'on\') as plus_comment_count,
                   c."name" as city_name,
                   a."name" as area_name
              FROM users u
              LEFT JOIN cities c ON u."cityID" = c."id"
              LEFT JOIN areas a ON u."areaID" = a."id"
              '. $areaLeftJoin .'
            ) as r
';

$busy = 'r."status"=\'0\'';
if(!empty($_GET['busy'])) $busy ='';
if(!empty($_GET['cityID'])) $dopSQL[] = 'r."cityID"=\''. $_GET['cityID'] .'\'';
if(!empty($_GET['areaID'])) $dopSQL[] = 'r."areaID"=\''. $_GET['areaID'] .'\'';
if(!empty($_GET['comments'])) $dopSQL[] = 'r."comment_count">0';
if(!empty($_GET['plus_comments'])) $dopSQL[] = 'r."plus_comment_count">0';
if(!empty($_GET['search_str'])) $dopSQL[] = 'r."name" LIKE \'%'. $_GET['search_str'] .'%\'';
if(!empty($_GET['type']) && $_GET['type'] === 'companies') $dopSQL[] = 'r."type_of_registration"=2';
if(!empty($busy)) $dopSQL[] = $busy;
if(!empty($dopSQL)) $sql .= ' WHERE '. implode(' AND ', $dopSQL);

$sql .= ' ORDER BY sort DESC';
$allUsers = Application::$DB->query($sql)->fetchAll();
$offset = 0;
if(!empty($_GET['pagination'])) $offset = ($_GET['pagination'] * 10) - 10;
$sql .= ' LIMIT 10 OFFSET '. $offset;
$users = Application::$DB->query($sql)->fetchAll();
/*
foreach($users as $user){
    
    $users_professions = $DB->query('
        SELECT *
            FROM users_professions up
            JOIN professions p ON up."professionID" = p."id"
              WHERE up."userID"='. $user['id'])->fetchAll();
    $profession_arr = [];
    foreach($users_professions as $profession){
        $profession_arr[] = $profession['name'];
    }
    
    $objects_images = $DB->query('
        SELECT *
            FROM objects o
            LEFT JOIN objects_imgs oi ON o."id" = oi."objectID"
              WHERE o."createrUserID"='. $user['id'])->fetchAll();
    /*
    $img_div = '<div>';
    foreach($objects_images as $image){
        $img_div .= '<img width="100px" src="/images/objects/'. $image['objectID'] .'/'. $image['src'] .'" />';
    }
    $img_div .= '</div>';
    
    $div = '<div style="border: 1px solid black;">';
    $div .= '<a href="/users/'. $user['id'] .'/">'. $user['name'] .' '. $user['surname'] .'</a><br/>';
    $div .= '<img width=100px src="/images/users/'. $user['id'] .'/'. $user['avatar'] .'" /><br/>';
    $div .= $user['work_city'] .' '. implode(', ', $profession_arr) .'<br/>';
    $div .= 'На сайте: '. floor((strtotime("now") - strtotime($user['created'])) / (60*60*24)) .' дней(я)<br/>';
    $div .= 'Стаж работы: '. $user['experience'] .'<br/>';
    $div .= $user['comment_count'] .' отзывов<br/><br/>';
    
    $div .= 'Фото работ'. $img_div .'<br/><br/>';
    
    $div .= 'Цены на услуги';
    $div .= '<div>'. $user['price_description'] .'</div>';
    
    $div .= '</div>';
    echo($div);
    */
/*};*/
?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="/">Главная</a>
            </li>
            <li>
                <a href="/masters/">Исполнители</a>
            </li>
            <?php if(!empty($area)) echo '<li><a href="#">'. $area['name'] .'</a></li>'; ?>
        </ul>
    </div>
    <div class="tipical-content-headline">Мастера</div>
    <div class="columns-holder clearfix">
        <div class="column-left" <?php if(empty($users)) echo 'style="text-align: center;"'; ?>>
            <?php if(empty($users)) echo '<h4>По выбранным параметра ничего не найдено.</h4>'; ?>
            <?php foreach($users as $user){
                $users_professions = $DB->query('
                SELECT *
                    FROM users_kinds_of_jobs ukj
                    LEFT JOIN kinds_of_jobs kj ON ukj.kind_of_job_id = kj.id
                      WHERE ukj."userID"='. $user['id'])->fetchAll();
                $profession_arr = [];
                foreach($users_professions as $profession){
                    $profession_arr[] = $profession['name'];
                }

                $objects_images = $DB->query('
                    SELECT *
                        FROM objects o
                        LEFT JOIN objects_imgs oi ON o."id" = oi."objectID"
                          WHERE o."createrUserID"='. $user['id'] .' AND
                                o."status" <> \'archive\' AND
                                oi."src" IS NOT NULL')->fetchAll();

                $imgs = [];
                foreach($objects_images as $image){
                    $imgs[] = '<img width="100px" src="/images/objects/'. $image['objectID'] .'/'. $image['src'] .'" />';
                }
                
                $prices_services = $DB->query('
                    SELECT *
                      FROM users_prices up
                        WHERE up."userID"='. $user['id'])->fetchAll();
            ?>            
            <div class="column-product-item">
                <div class="specialist-holder clearfix">
                    <a href="/users/<?php echo $user['id']; ?>/" class="specialist-avatar">
                        <img src="<?php if(!empty($user['avatar'])){ ?>/images/users/<?php echo $user['id']; ?>/<?php echo $user['avatar']; ?><?php }else{ ?>/images/img1.jpg<?php } ?>" />
                    </a>
                    <div class="specialist-meta">
                        <a href="/users/<?php echo $user['id']; ?>/" class="specialist-name">
                            <?php echo $user['name'] .' '. $user['surname']; ?>
                            <span class="valid">(проверено)</span>
                        </a>
                        <?php if(!empty($user['city_name'])){ ?><p><b>Место работы:</b> <?php echo $user['city_name']; ?></p><?php } ?>
                        <p><b>На сайте:</b> <?php echo floor((strtotime("now") - strtotime($user['created'])) / (60*60*24)) .' дней(я)'; ?></p>
                        <?php if(!empty($user['experience'])){ ?><p><b>Стаж работы:</b> <?php echo $user['experience']; ?></p><?php } ?>
                        <br>
                        <p><b>Виды деятельности:</b></p>
                        <?php if(!empty($profession_arr)){
                            echo '<p>'. implode('</p><p>', $profession_arr) .'</p>';
                        }else{
                            echo '<p><strong style="color: red;">Виды деятельности не могут быть пустыми!</strong></p>';
                        } ?>
                        <br>
                        <a href="/users/<?php echo $user['id']; ?>/" class="specialist-feedbacks"><?php echo $user['comment_count']; ?> отзывов</a>
                    </div>
                    <span class="star-master <!--active-->"></span>
                </div>
                <?php if(!empty($imgs)){ ?>
                    <div class="product-sub-headline">Фото работ</div>
                    <?php echo implode(' ', $imgs); ?>
                <?php } ?>

                <?php if(!empty($prices_services)){ ?>
                <div class="product-sub-headline">Цены на услуги</div>
                <?php foreach($prices_services as $price_service){ ?>
                <p><?php echo $price_service['name']; ?>......................от <?php echo $price_service['amount']; ?> р/<?php echo $price_service['value']; ?></p>
                <?php } ?>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <div class="column-right">
            <div class="column-searcher-holder">
                <form class="column-searcher">
                    <fieldset>
                        <div class="column-searcher-selects">
                            <div class="column-searcher-select-label">Регион</div>
                            <select name="areaID">
                                <?php if(!empty($area)) echo '<option value="'. $area['id'] .'">'. $area['name'] .'</option>'; ?>
                                <?php echo $areas_options; ?>
                            </select>
                            <div class="column-searcher-select-label">Мой город</div>
                            <select name="cityID">
                                <?php if(!empty($city)) echo '<option value="'. $city['id'] .'">'. $city['name'] .'</option>'; ?>
                                <?php echo $cities_options; ?>
                            </select>
                        </div>
                        <div class="column-searcher-categories">
                            <div class="column-searcher-categories-headline">Виды работ</div>
                            <ul class="searcher-categories specialists-searcher">
                                <?php echo Application::getListOfAreas('job', null, $_GET); ?>
                            </ul>
                            <div class="specialists-advantages">
                                <p><label><input type="checkbox" <?php if(!empty($_GET['busy'])) echo 'checked'; ?> name="busy"> С занятыми</label></p>
                                <p><label><input type="checkbox" <?php if(!empty($_GET['comments'])) echo 'checked'; ?> name="comments"> С отзывами</label></p>
                                <p><label><input type="checkbox" <?php if(!empty($_GET['plus_comments'])) echo 'checked'; ?> name="plus_comments"> Только с положительными отзывами</label></p>
                            </div>
                            <input type="hidden" name="search" value="true" />
                            <button type="submit">показать</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <div class="pagination-holder">
        <?php echo Application::getPagePagination('masters', count($allUsers), $_GET); ?>
    </div>
</div>