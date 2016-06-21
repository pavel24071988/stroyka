<?php
// начинаем выстраивать функционал поиска
$allObjects = $common_data['allObjects'];
if(isset($_GET['search']) && $_GET['search'] === 'true'){
    $dopSQL = [];
    if(!empty($_GET['cityID'])) $dopSQL[] = 'c."id"='. $_GET['cityID'];
    if(!empty($_GET['areaID'])) $dopSQL[] = 'o."areaID"='. $_GET['areaID'];
    if(!empty($_GET['search_str'])) $dopSQL[] = 'o."name" LIKE \'%'. $_GET['search_str'] .'%\'';
    
    $kindSelect = '';
    $kindLeftJoin = '';
    if(!empty($_GET['areas_for_object'])){
        $kindSelect = ' lkjo."kindOfJobID",';
        $kindLeftJoin = ' LEFT JOIN links_kinds_of_jobs_objects lkjo ON o.id = lkjo."objectID"';
        $dopSQL[] = 'lkjo."kindOfJobID" IN (\''. implode('\', \'', $_GET['areas_for_object']) .'\')';
    }
    
    $sql = '
        SELECT DISTINCT ON (o.id) o.id,
               o.*,
               '. $kindSelect .'
               (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = o.id AND c."type"=\'object_comment\') as comment_count,
               c.name as city_name
            FROM objects o
            LEFT JOIN cities c ON o."cityID" = c.id
            '. $kindLeftJoin .'';
    $dopSQL[] = 'o.type_of_kind<>2';
    $dopSQL[] = 'o.status<>\'archive\'';
    if(!empty($dopSQL)) $sql .= ' WHERE '. implode(' AND ', $dopSQL);
    $sql .= ' ORDER BY o.id, o.created';
    $allObjects = Application::$DB->query($sql)->fetchAll();
    $offset = 0;
    if(!empty($_GET['pagination'])) $offset = ($_GET['pagination'] * 10) - 10;
    $sql .= ' LIMIT 10 OFFSET '. $offset;
    $objects = Application::$DB->query($sql)->fetchAll();

    $common_data['objects'] = $objects;
    
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
    $selected = (!empty($city) && $city['areaID'] === $general_area['id']) ? ' selected' : '';
    $areas_options .= '<option value="'. $general_area['id'] .'" '. $selected .'>'. $general_area['name'] .'</option>';
}

?>
<div class="content">
    <div class="breadcrumb">
        <ul class="clearfix">
            <li>
                <a href="/">Главная</a>
            </li>
            <li>
                <a href="/objects/">Заказы</a>
            </li>
            <?php if(!empty($area)) echo '<li><a href="#">'. $area['name'] .'</a></li>'; ?>
        </ul>
    </div>

    <div class="columns-holder clearfix">
        <div class="column-left">
            <div class="objects-holder" <?php if(empty($common_data['objects'])) echo 'style="text-align: center;"'; ?>>
                <?php if(empty($common_data['objects'])) echo '<h4>По выбранным параметра ничего не найдено.</h4>'; ?>
                <?php foreach($common_data['objects'] as $object){ ?>
                <div class="object-item clearfix">
                    <div class="object-item-description">
                        <div class="column-product-item-top clearfix">
                            <a href="<?php echo '/objects/'. $object['id'] .'/'; ?>" class="column-product-title"><?php echo $object['name']; ?></a>
                        </div>
                        <div class="column-product-item-description">
                            Краткое описание.<br>
                            <?php echo $object['description']; ?>
                        </div>
                    </div>
                    <div class="object-item-meta">
                        <div class="object-item-meta-main">
                            <div class="object-meta-date"><?php echo date('j.m.Y H:i:s', strtotime($object['created'])); ?></div>
                            <div class="object-meta-place">г. <?php echo $object['city_name']; ?></div>
                        </div>
                        <div class="object-item-meta-price"><?php echo $object['amount']; ?> <span>руб.</span></div>
                        <a href="#" class="answers"><?php echo $object['comment_count']; ?> ответов</a>
                    </div>
                </div>
                <?php }; ?>
            </div> 
        </div>
        <div class="column-right">
            <div class="column-searcher-holder">
                <form class="column-searcher">
                    <fieldset>
                        <div class="column-searcher-selects">
                            <div class="column-searcher-select-label">Регион</div>
                            <select class="region" >
                                <?php echo $areas_options; ?>
                            </select>
                            <div class="column-searcher-select-label">Мой город</div>
                            <select class="city"  name="cityID">
                                <?php if(!empty($city)) echo '<option value="'. $city['id'] .'">'. $city['name'] .'</option>'; ?>
                                <?php echo $cities_options; ?>
                            </select>
                        </div>
                        <div class="column-searcher-categories">
                            <div class="column-searcher-categories-headline">Виды работ</div>
                            <ul class="searcher-categories">
                                <?php echo Application::getListOfAreas('object', null, $_GET); ?>
                            </ul>
                            <input type="hidden" name="search" value="true" />
                            <button type="submit">показать</button>
                        </div>
                    </fieldset>
                </form>
                <br><br>
                <div style="padding: 0 18px;"><?= Application::findBanner($_GET); ?></div>
            </div>
        </div>
    </div>
    <div class="pagination-holder">
        <?php echo Application::getPagePagination('objects', count($allObjects), $_GET); ?>
    </div>
</div>