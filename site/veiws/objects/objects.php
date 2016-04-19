<div class="content">
        <div class="breadcrumb">
            <ul class="clearfix">
                <li>
                    <a href="#">Главная</a>
                </li>
                <li>
                    <a href="#">Заказы</a>
                </li>
                <li>
                    <a href="#">Воронежская область</a>
                </li>
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
                                <select>
                                    <option>Воронежская обл.</option>
                                    <option>Московская обл.</option>
                                    <option>Ленинградская обл.</option>
                                    <option>Мурманская обл.</option>
                                    <option>Липецкая обл.</option>
                                </select>
                                <div class="column-searcher-select-label">Мой город</div>
                                <select>
                                    <option>Воронежск</option>
                                    <option>Московск</option>
                                    <option>Ленинградск</option>
                                    <option>Мурманск</option>
                                    <option>Липецк</option>
                                </select>
                            </div>
                            <div class="column-searcher-categories">
                                <div class="column-searcher-categories-headline">Виды работ</div>
                                <ul class="searcher-categories">
                                    <li>
                                        <div class="searcher-categories-item">
                                            <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                        </div>
                                        <ul class="searcher-sub-categories">
                                            <li>
                                                <div class="searcher-categories-item">
                                                    <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="searcher-categories-item">
                                                    <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="searcher-categories-item">
                                                    <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                                </div>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <div class="searcher-categories-item">
                                            <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="searcher-categories-item">
                                            <label><input type='checkbox'> Мелкие бытовые услуги</label>
                                        </div>
                                    </li>
                                </ul>
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