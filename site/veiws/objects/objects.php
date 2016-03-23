<h1>Объекты и стоимости</h1>
<?php
foreach($common_data['objects'] as $object){

    $div = '<div style="border: 1px solid black;">';
    $div .= '<a href="/objects/'. $object['id'] .'/">'. $object['name'] .'</a> '. $object['amount'] .' руб.<br/>';
    $div .= $object['description'] .'<br/>';
    $div .= date('j.m.Y H:i:s', strtotime($object['created'])) .'<br/>';

    $div .= '<div>'. $object['comment_count'] .' ответов</div>';

    $div .= '</div>';
    
    echo $div;
};
?>