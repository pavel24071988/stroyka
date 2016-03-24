<h1>Вакансии и зарплаты</h1>
<?php
foreach($common_data['jobs'] as $job){
    
    $div = '<div style="border: 1px solid black;">';
    $div .= '<a href="/jobs/'. $job['id'] .'/">'. $job['name'] .'</a> '. $job['amount'] .' руб.<br/>';
    $div .= $job['description'] .'<br/>';
    $div .= date('j.m.Y H:i:s', strtotime($job['created'])) .'<br/>';
    
    $div .= '<div>'. $job['comment_count'] .' ответов</div>';
    
    $div .= '</div>';
    echo $div;
};
?>