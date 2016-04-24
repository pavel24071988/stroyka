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

$sql = 'SELECT j.*, (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = j.id AND c."type"=\'job_comment\') as comment_count FROM jobs j';
$dopSQL = [];
if(!empty($_GET['cityID'])) $dopSQL[] = 'j."cityID"='. $_GET['cityID'];
if(!empty($_GET['areaID'])) $dopSQL[] = 'j."areaID"='. $_GET['areaID'];
if(!empty($dopSQL)) $sql .= ' WHERE '. implode(' AND ', $dopSQL);

$jobs = Application::$DB->query($sql)->fetchAll();
?>

<div class="content">
        <div class="breadcrumb">
            <ul class="clearfix">
                <li>
                    <a href="/">Главная</a>
                </li>
                <li>
                    <a href="/jobs/">Вакансии</a>
                </li>
                <?php if(!empty($area)) echo '<li><a href="#">'. $area['name'] .'</a></li>'; ?>
            </ul>
        </div>
        <div class="columns-holder clearfix">
            <div class="column-left">
                <?php foreach($jobs as $job){ ?>
                <div class="column-product-item">
                    <div class="column-product-item-top clearfix">
                        <a href="<?php echo '/jobs/'. $job['id'] .'/'; ?>" class="column-product-title"><?php echo $job['name']; ?></a>
                        <div class="column-product-price"><?php echo $job['amount']; ?></div>
                    </div>
                    <div class="column-product-item-description"><?php echo $job['description']; ?></div>
                    <div class="column-product-item-date"><?php echo date('j.m.Y H:i:s', strtotime($job['created'])); ?></div>
                </div>
                <?php }; ?>
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
                                    <?php echo Application::getListOfAreas('user', null); ?>
                                </ul>
                                <input type="hidden" name="search" value="true" />
                                <button type="submit">показать</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>


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



    </div>