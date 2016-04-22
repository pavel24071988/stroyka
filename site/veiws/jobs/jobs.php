<div class="content">
        <div class="breadcrumb">
            <ul class="clearfix">
                <li>
                    <a href="#">Главная</a>
                </li>
                <li>
                    <a href="#">Вакансии</a>
                </li>
            </ul>
        </div>
        <div class="columns-holder clearfix">
            <div class="column-left">
                <?php foreach($common_data['jobs'] as $job){ ?>
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
                                <ul class="searcher-categories"><?php echo Application::getListOfAreas('job', null); ?></ul>
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