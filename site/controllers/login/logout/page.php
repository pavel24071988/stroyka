<?php
if(empty($_SESSION['user'])){
    echo '<meta http-equiv="refresh" content="0;URL=/">';
}else{
    unset($_SESSION['user']);
    echo '<meta http-equiv="refresh" content="0;URL="'. $_SERVER['HTTP_REFERER'] .'">';
}