<?php
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
    $opponent = $usersModel->getUser($applicationURL[5]);
    $usersModel->setReadableMessages($_SESSION['user']['id'], $applicationURL[5]);
?>
    <div class="content">

        <div class="my-page-content clearfix">
            <?php if($common_data['check_owner']) echo Application::getLeftMenu(); ?>
            <div class="my-page-wrapper">
                <div class="my-page-breadcrumb">
                    <ul>
                        <li>
                            <a href="#">Диалоги</a>
                        </li>
                        <li>
                            <a href="#"><?php echo $opponent[0]['name'] .' '. $opponent[0]['surname']; ?></a>
                        </li>
                    </ul>
                </div>

                <div class="dialogs-holder">
                    <a href="#" class="show-old-dialogs">Просмотреть старые сообщения</a>
                    <?php foreach($historyOfMessagesByUser as $historyOfMessageByUser){ ?>
                    <div class="speech-item clearfix">
                        <div class="speech-item-avatar">
                            <a href="#">
                                <img src="<?php echo '/images/users/'. $historyOfMessageByUser['id'] .'/'. $historyOfMessageByUser['avatar']; ?>" />
                            </a>
                        </div>
                        <div class="speech-item-content">
                            <div class="speech-item-top clearfix">
                                <a href="#" class="speech-item-name"><?php echo $historyOfMessageByUser['name'] .' '. $historyOfMessageByUser['surname']; ?></a>
                                <div class="speech-item-date"><?php echo date('j.m.Y H:i:s', strtotime($historyOfMessageByUser['created'])); ?></div>
                            </div>
                            <?php echo $historyOfMessageByUser['text']; ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php if((int) $applicationURL[5] !== $_SESSION['user']['id']){ ?>
                    <form class="speech-write-form clearfix" method="POST">
                        <fieldset>
                            <div class="speech-write-form-title">Написать сообщение</div>
                            <textarea name="text"></textarea>
                            <button type="submit">Отправить</button>
                        </fieldset>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    
<?php }else{ ?>
<div class="content">
    <div class="my-page-content clearfix">
        <?php if($common_data['check_owner']) echo Application::getLeftMenu(); ?>
        <div class="my-page-wrapper">
            <div class="my-page-breadcrumb">
                <ul>
                    <li>
                        <a href="#">Сообщения</a>
                    </li>
                </ul>
            </div>
            <div class="dialogs-holder">
                <?php foreach($incomeMessages as $message){ ?>
                <div class="dialog-item clearfix">
                    <div class="dialog-item-avatar">
                        <a href="#">
                            <img src="<?php echo $message['avatar']; ?>">
                        </a>
                    </div>
                    <div class="dialog-item-content">
                        <div class="dialog-item-name">
                            <a href="/users/<?php echo $_SESSION['user']['id']; ?>/my_messages/dialogs/<?php echo $message['id']; ?>/"><?php echo $message['name'] .' '. $message['surname']; ?></a> <span><?php if($message['read'] === false){ echo '(новое)'; } ?></span>
                        </div>
                        <div class="dialog-item-text">
                            <?php echo $message['text']; ?>
                        </div>
                        <div class="dialog-item-time">
                            <?php echo date('j.m.Y H:i:s', strtotime($message['created'])); ?>
                        </div>
                    </div>
                </div>
                <?php }; ?>
                <?php foreach($outcomeMessages as $message){ ?>
                <div class="dialog-item clearfix">
                    <div class="dialog-item-avatar">
                        <a href="#">
                            <img src="<?php echo $message['avatar']; ?>">
                        </a>
                    </div>
                    <div class="dialog-item-content">
                        <div class="dialog-item-name">
                            <a href="/users/<?php echo $_SESSION['user']['id']; ?>/my_messages/dialogs/<?php echo $message['id']; ?>/"><?php echo $message['name'] .' '. $message['surname']; ?></a> <span><?php // if($message['read'] === false){ echo '(новое)'; } ?></span>
                        </div>
                        <div class="dialog-item-text">
                            <?php echo $message['text']; ?>
                        </div>
                        <div class="dialog-item-time">
                            <?php echo date('j.m.Y H:i:s', strtotime($message['created'])); ?>
                        </div>
                    </div>
                </div>
                <?php }; ?>
                <a href="#" class="load-old-dialogs">Загрузить старые диалоги</a>
            </div>
        </div>
    </div>
</div>
<?php }; ?>