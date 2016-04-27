<?php
// начинаем выстраивать функционал поиска
if(isset($_GET['search']) && $_GET['search'] === 'true'){
    $dopSQL = [];
    if(!empty($_GET['cityID'])) $dopSQL[] = 'c."id"='. $_GET['cityID'];
    if(!empty($_GET['areaID'])) $dopSQL[] = 'o."areaID"='. $_GET['areaID'];
    if(!empty($_GET['search_str'])) $dopSQL[] = 'o."name" LIKE \'%'. $_GET['search_str'] .'%\'';
    $sql = 'SELECT o.*,
               (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = o.id AND c."type"=\'object_comment\') as comment_count,
               c.name as city_name
            FROM objects o
            LEFT JOIN cities c ON o."cityID" = c.id';
    if(!empty($dopSQL)) $sql .= ' WHERE '. implode(' AND ', $dopSQL);
    
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
    if(!empty($area) && $area['id'] === $general_area['id']) continue;
    $areas_options .= '<option value="'. $general_area['id'] .'">'. $general_area['name'] .'</option>';
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
            <div class="objects-holder">
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
                            <ul class="searcher-categories">
                                <?php echo Application::getListOfAreas('objects', null); ?>
                            </ul>
                            <input type="hidden" name="search" value="true" />
                            <button type="submit">показать</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
    <!--
    <div class="pagination-holder">
        <a href="#" class="pagination-left"></a>
        <ul class="pagination-pages">
            <li>
                <a href="#">1</a>
            </li>
            <li>
                <a href="#">2</a>
            </li>
            <li>
                <a href="#">3</a>
            </li>
            <li>
                <a href="#">4</a>
            </li>
            <li>
                <a href="#" class="active">5</a>
            </li>
            <li>
                <a href="#">6</a>
            </li>
            <li>
                <a href="#">7</a>
            </li>
            <li>
                <a href="#">8</a>
            </li>
             <li>
                <a href="#">9</a>
            </li>
            <li>
                <a href="#">...</a>
            </li>
            <li>
                <a href="#">103</a>
            </li>
            <li>
                <a href="#">104</a>
            </li>
        </ul>
        <a href="#" class="pagination-right"></a>
    </div>
    -->
</div>