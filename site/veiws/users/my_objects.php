<?php
$usersModel = new usersModel;
$myObjects = $usersModel->getMyOwnerObjects($_SESSION['user']['id']);
$responseObjects = $usersModel->getMyResponseObjects($_SESSION['user']['id']);
$myJobs = $usersModel->getMyOwnerJobs($_SESSION['user']['id']);
$responseJobs = $usersModel->getMyResponseJobs($_SESSION['user']['id']);
?>
<!--
<h1>Объекты:</h1>
<h4>Я выставил:</h4>
<?php /*foreach($myObjects as $myObject){
     echo '<div style="border: 1px solid black;">№'. $myObject['id'] .' "'. $myObject['name'] .'" ('. $myObject['amount'] .')
            от '. date('j.m.Y', strtotime($myObject['created'])) .'
            | '. $myObject['responses'] .' отклика(ов) | <a href="http://stroyka/objects/'. $myObject['id'] .'/edit/">Редактировать</a> <a href="http://stroyka/objects/'. $myObject['id'] .'/close/">Закрыть</a>
           </div>';
}*/
?>

<h4>Я откликнулся:</h4>
<?php /*foreach($responseObjects as $responseObject){
     echo '<div style="border: 1px solid black;">№'. $responseObject['id'] .' "'. $responseObject['name'] .'" ('. $responseObject['amount'] .')
            от '. date('j.m.Y', strtotime($responseObject['created'])) .'
            | Ваша зявка рассмотрена заказчиком, напишите ему, чтобы договориться об условиях! | <a href="http://stroyka/objects/'. $myObject['id'] .'/edit/">Снять заявку</a>
           </div>';
}*/
?>
<br/>
<div><a href="/objects/add/">Добавить оъект</a></div>
<hr/>
<h1>Вакансии:</h1>
<h4>Я добавил:</h4>
<?php /*foreach($myJobs as $myJob){
     echo '<div style="border: 1px solid black;">№'. $myJob['id'] .' "'. $myJob['name'] .'" ('. $myJob['amount'] .')
            от '. date('j.m.Y', strtotime($myJob['created'])) .'
            | '. $myJob['responses'] .' отклика(ов) | <a href="http://stroyka/jobs/'. $myJob['id'] .'/edit/">Редактировать</a> <a href="http://stroyka/jobs/'. $myJob['id'] .'/close/">Закрыть</a>
           </div>';
}*/
?>

<h4>Я откликнулся:</h4>
<?php /*foreach($responseJobs as $responseJob){
     echo '<div style="border: 1px solid black;">№'. $responseJob['id'] .' "'. $responseJob['name'] .'" ('. $responseJob['amount'] .')
            от '. date('j.m.Y', strtotime($responseJob['created'])) .'
            | Ваша зявка рассмотрена заказчиком, напишите ему, чтобы договориться об условиях! | <a href="http://stroyka/objects/'. $responseJob['id'] .'/edit/">Снять заявку</a>
           </div>';
}*/
?>
<br/>
<div><a href="/jobs/add/">Добавить вакансию</a></div>
<hr/>
<br/>
-->
<div class="content">
    <div class="my-page-content clearfix">
        <?php echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Объекты и вакансии</a>
                    </li>
                </ul>
            </div>
            <div class="my-page-wrapper-content">
                <div class="my-page-wrapper-headline">Объекты</div>
                <div class="objects-tabel-holder">
                    <div class="objects-tabel-headline">Я выставил:</div>
                    <?php foreach($myObjects as $myObject){ ?>
                        <div class="objects-tabel clearfix">
                            <div class="objects-tabel-row">
                                <div class="objects-tabel-cell exposed-name">
                                    <?php echo '№'. $myObject['id'] .' '. $myObject['name'] .' ('. $myObject['amount'] .' руб) от '. date('j.m.Y', strtotime($myObject['created'])); ?>
                                </div>
                                <div class="objects-tabel-cell exposed-small cntr">
                                    <?php echo $myObject['responses']; ?> откликов<br><a href="/objects/<?php echo $myObject['id']; ?>/"><b>(10 новых)</b></a>
                                </div>
                                <div class="objects-tabel-cell exposed-small">
                                    <a href="/objects/<?php echo $myObject['id']; ?>/edit/">редактировать</a>
                                    <br>
                                    <a href="/objects/<?php echo $myObject['id']; ?>/close/">закрыть</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <a href="/objects/add/" class="tipical-button">Добавить объект</a>
                </div>
                <div class="objects-tabel-holder">
                    <div class="objects-tabel-headline">Я откликнулся:</div>
                    <?php foreach($responseObjects as $responseObject){ ?>
                        <div class="objects-tabel clearfix">
                            <div class="objects-tabel-row">
                                <div class="objects-tabel-cell feedback-name">
                                    <?php echo '№'. $responseObject['id'] .' "'. $responseObject['name'] .'" ('. $responseObject['amount'] .' руб) от '. date('j.m.Y', strtotime($responseObject['created'])); ?>
                                    <div class="edit-snippet">Последняя правка 24.12.2015 (17:55)</div>
                                </div>
                                <div class="objects-tabel-cell feedback-mid">
                                    <div class="feedback-info alert">
                                        На рассмотрении.<br><b>Внимание! Заказчик отредактировал<br>заявку 24.12.2015 (17:55)</b>
                                    </div>
                                </div>
                                <div class="objects-tabel-cell feedback-small">
                                    <a href="/objects/<?php echo $myObject['id']; ?>/edit/">снять заявку</a>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="my-page-wrapper-headline">Вакансии</div>
                <div class="objects-tabel-holder">
                    <div class="objects-tabel-headline">Я добавил:</div>
                    <?php foreach($myJobs as $myJob){ ?>
                    <div class="objects-tabel clearfix">
                        <div class="objects-tabel-row">
                            <div class="objects-tabel-cell exposed-name">
                                <?php echo '№'. $myJob['id'] .' "'. $myJob['name'] .'" ('. $myJob['amount'] .' руб) от '. date('j.m.Y', strtotime($myJob['created'])); ?>
                            </div>
                            <div class="objects-tabel-cell exposed-small cntr">
                                <?php echo $myJob['responses']; ?> откликов
                            </div>
                            <div class="objects-tabel-cell exposed-small">
                                <a href="/jobs/<?php echo $myJob['id']; ?>/edit/">редактировать</a>
                                <br>
                                <a href="/jobs/<?php echo $myJob['id']; ?>/close/">закрыть</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <a href="/jobs/add/" class="tipical-button">Добавить вакансию</a>
                </div>
                <div class="objects-tabel-holder">
                    <div class="objects-tabel-headline">Я откликнулся:</div>
                    <?php foreach($responseJobs as $responseJob){ ?>
                    <div class="objects-tabel clearfix">
                        <div class="objects-tabel-row">
                            <div class="objects-tabel-cell feedback-name">
                                <?php echo '№'. $responseJob['id'] .' "'. $responseJob['name'] .'" ('. $responseJob['amount'] .' руб) от '. date('j.m.Y', strtotime($responseJob['created'])); ?>
                            </div>
                            <div class="objects-tabel-cell feedback-mid">
                                <div class="feedback-info ok">
                                    Ваша зявка рассмотрена заказчиком,<br><a href="/users/<?php echo $_SESSION['user']['id']; ?>/my_messages/dialogs/<?php echo $responseJob['createrUserID']; ?>/">напишите ему</a>, чтобы<br>договориться об условиях!
                                </div>
                            </div>
                            <div class="objects-tabel-cell feedback-small">
                                <a href="#">снять заявку</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!--<div class="my-page-wrapper-headline">Архив</div>
                <div class="archive-subheadline">Объекты:</div>
                <table class="archive-table" >
                    <colgroup>
                        <col width="16%">
                        <col width="63%">
                        <col width="21%">
                    </colgroup>
                    <tr>
                        <th>Номер</th>
                        <th>Название</th>
                        <th>Отправлено в архив</th>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                </table>
                <div class="archive-subheadline">Вакансии:</div>
                <table class="archive-table" >
                    <colgroup>
                        <col width="16%">
                        <col width="63%">
                        <col width="21%">
                    </colgroup>
                    <tr>
                        <th>Номер</th>
                        <th>Название</th>
                        <th>Отправлено в архив</th>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                    <tr>
                        <td>1775</td>
                        <td>Ремонт санузла</td>
                        <td>10.05.2015</td>
                    </tr>
                </table>
                -->
            </div>
        </div>
    </div>
</div>