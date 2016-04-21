<?php
echo '<h1>Объекты и вакансии</h1>';
echo Application::getLeftMenu();
$usersModel = new usersModel;
$myObjects = $usersModel->getMyOwnerObjects($_SESSION['user']['id']);
$responseObjects = $usersModel->getMyResponseObjects($_SESSION['user']['id']);
$myJobs = $usersModel->getMyOwnerJobs($_SESSION['user']['id']);
$responseJobs = $usersModel->getMyResponseJobs($_SESSION['user']['id']);
?>

<h1>Объекты:</h1>
<h4>Я выставил:</h4>
<?php foreach($myObjects as $myObject){
     echo '<div style="border: 1px solid black;">№'. $myObject['id'] .' "'. $myObject['name'] .'" ('. $myObject['amount'] .')
            от '. date('j.m.Y', strtotime($myObject['created'])) .'
            | '. $myObject['responses'] .' отклика(ов) | <a href="http://stroyka/objects/'. $myObject['id'] .'/edit/">Редактировать</a> <a href="http://stroyka/objects/'. $myObject['id'] .'/close/">Закрыть</a>
           </div>';
}
?>

<h4>Я откликнулся:</h4>
<?php foreach($responseObjects as $responseObject){
     echo '<div style="border: 1px solid black;">№'. $responseObject['id'] .' "'. $responseObject['name'] .'" ('. $responseObject['amount'] .')
            от '. date('j.m.Y', strtotime($responseObject['created'])) .'
            | Ваша зявка рассмотрена заказчиком, напишите ему, чтобы договориться об условиях! | <a href="http://stroyka/objects/'. $myObject['id'] .'/edit/">Снять заявку</a>
           </div>';
}
?>
<br/>
<div><a href="/objects/add/">Добавить оъект</a></div>
<hr/>
<h1>Вакансии:</h1>
<h4>Я добавил:</h4>
<?php foreach($myJobs as $myJob){
     echo '<div style="border: 1px solid black;">№'. $myJob['id'] .' "'. $myJob['name'] .'" ('. $myJob['amount'] .')
            от '. date('j.m.Y', strtotime($myJob['created'])) .'
            | '. $myJob['responses'] .' отклика(ов) | <a href="http://stroyka/jobs/'. $myJob['id'] .'/edit/">Редактировать</a> <a href="http://stroyka/jobs/'. $myJob['id'] .'/close/">Закрыть</a>
           </div>';
}
?>

<h4>Я откликнулся:</h4>
<?php foreach($responseJobs as $responseJob){
     echo '<div style="border: 1px solid black;">№'. $responseJob['id'] .' "'. $responseJob['name'] .'" ('. $responseJob['amount'] .')
            от '. date('j.m.Y', strtotime($responseJob['created'])) .'
            | Ваша зявка рассмотрена заказчиком, напишите ему, чтобы договориться об условиях! | <a href="http://stroyka/objects/'. $responseJob['id'] .'/edit/">Снять заявку</a>
           </div>';
}
?>
<br/>
<div><a href="/jobs/add/">Добавить вакансию</a></div>
<hr/>
<br/>