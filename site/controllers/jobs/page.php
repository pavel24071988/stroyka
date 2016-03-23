<div><a href="/">Главная</a>-><a href="/masters/">Исполнители</a>-><a href="/masters/">Воронежская область</a></div>
<h1>Вакансии и зарплаты</h1>
<?php
$DB = Application::$DB;
$jobs = $DB->query('SELECT j.*, (SELECT COUNT(c.id) FROM comments c WHERE c."typeID" = j.id AND c."type"=\'job_comment\') as comment_count FROM jobs j')->fetchAll();
foreach($jobs as $job){
    
    $div = '<div style="border: 1px solid black;">';
    $div .= '<a href="/jobs/'. $object['id'] .'/">'. $job['name'] .'</a> '. $job['amount'] .' руб.<br/>';
    $div .= $job['description'] .'<br/>';
    $div .= date('j.m.Y H:i:s', strtotime($job['created'])) .'<br/>';
    
    $div .= '<div>'. $job['comment_count'] .' ответов</div>';
    
    $div .= '</div>';
    echo($div);
};