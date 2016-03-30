<?php
echo '<h1>Мои сообщения</h1>';
echo $common_data['left_menu'];
$usersModel = new usersModel;
$applicationURL = Application::$URL;
$error_write = '';

if($applicationURL[4] === 'dialogs' && isset($_POST['text'])){
    $error_write = $usersModel->setMessage($_SESSION['user']['id'], $applicationURL[5], $_POST['text']);
}

$incomeMessages = $usersModel->getIncomeMessages($_SESSION['user']['id']);
$outcomeMessages = $usersModel->getOutcomeMessages($_SESSION['user']['id']);
?>

<?php
// распараллелим функционал диалогов и общих сообщений
if($applicationURL[4] === 'dialogs'){
    $historyOfMessagesByUser = $usersModel->getHistoryOfMessagesByUser($_SESSION['user']['id'], $applicationURL[5]);
    if((int) $applicationURL[5] !== $_SESSION['user']['id']){
        echo '<br/>';
        echo '
            <form method="POST">
            Написать сообщение:<br/>
            <textarea name="text"></textarea><br/>
            <input type="submit" value="Ответить" />
            </form>
            <br/>
        ';
    }
    echo '<br/><hr/>';
    foreach($historyOfMessagesByUser as $historyOfMessageByUser){
        echo '
            <div>
                <img width="100px" src="'. $historyOfMessageByUser['avatar'] .'" />
                <div style="color: blue;">'. $historyOfMessageByUser['name'] .' '. $historyOfMessageByUser['surname'] .'</div>
                <div>'. $historyOfMessageByUser['text'] .'</div>
                <div>'. date('j.m.Y H:i:s', strtotime($historyOfMessageByUser['created'])) .'</div>
            </div>
        ';
    }
}else{
?>

<br/>
Полученные:
<?php
foreach($incomeMessages as $incomeMessage){
    echo '
        <div>
            <img width="100px" src="'. $incomeMessage['avatar'] .'" />
            <div style="color: blue;">'. $incomeMessage['name'] .' '. $incomeMessage['surname'] .'</div>
            <div>'. $incomeMessage['text'] .'</div>
            <div>'. date('j.m.Y H:i:s', strtotime($incomeMessage['created'])) .'</div>
            <a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $incomeMessage['id'] .'">Перейти к диалогу</a>
            <br/><br/>
        </div>
    ';
}
?>
<hr/>
Отправленные:
<?php
if(!empty($error_write)){
    echo '<div style="color: red;">'. $error_write .'</div>';
}
foreach($outcomeMessages as $outcomeMessage){
    echo '
        <div>
            <img width="100px" src="'. $outcomeMessage['avatar'] .'" />
            <div style="color: blue;">'. $outcomeMessage['name'] .' '. $outcomeMessage['surname'] .'</div>
            <div>'. $outcomeMessage['text'] .'</div>
            <div>'. date('j.m.Y H:i:s', strtotime($outcomeMessage['created'])) .'</div>
            <a href="/users/'. $_SESSION['user']['id'] .'/my_messages/dialogs/'. $outcomeMessage['id'] .'">Перейти к диалогу</a>
            <br/><br/>
        </div>
    ';
}
?>
<hr/>
<?php } ?>