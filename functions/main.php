<?php

class Application
{    
    public static $URL;
    public static $DB;

    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] .'/db/db.php';
        self::$URL = explode('/', $_SERVER['QUERY_STRING']);
        self::$DB = $DB;
    }

    public static function get_content(){
        $newURL = self::$URL;
        if($newURL[0] === '' && empty($newURL[1])){
            $curURL = [];
            $path_to_site = 'site/controllers/main';
        }else{
            $path_to_site = 'site/controllers';
            $curURL = [];
            foreach($newURL as $key => $path){
                $check = preg_match('/^\d+$/', $path);
                if($key === 0) continue;
                if(empty($check)) $curURL[] = $path;
                else break;
            }
        }
        
        // здесь будут наши исключения
        if(!empty($curURL[1]) && $curURL[1] === 'add'){
            unset($curURL[1]);
            sort($curURL);
        }
        
        $path_to_site = $path_to_site .'/'. implode('/',$curURL);
        
        if(!isset($newURL[3])) $path_to_site .= 'page.php';
        else $path_to_site .= '/page.php';
        
        if(!file_exists($path_to_site)){
            echo 'Не удалось подключить файл.';
            exit;
        }
        
        // логируем заходы пользователей
        try{
            $DB = Application::$DB;
            $userID = 0;
            if(!empty($_SESSION['user'])) $userID = $_SESSION['user']['id'];
            $log_entrance = $DB->prepare('
              INSERT INTO logs (userid, url)
                VALUES('. $userID .', \''. $path_to_site .'\')');
            $log_entrance->execute();
        }catch(Exception $ex){}
        
        require_once $path_to_site;
    }
    
    public static function getLeftMenu(){
        $user = $_SESSION['user'];
        $left_menu = '<div>
            <div><a href="/users/'. $user['id'] .'/">Мой паспорт</a></div>
            <div><a href="/users/'. $user['id'] .'/my_objects/">Объекты и вакансии</a></div>
            <div><a href="/users/'. $user['id'] .'/my_messages/">Сообщения</a></div>
            <div><a href="/users/'. $user['id'] .'/my_settings/">Настройки</a></div>
            <div><a href="/users/'. $user['id'] .'/my_works/">Мои работы</a></div>
            <div><a href="/users/'. $user['id'] .'/my_insurance/">Страхование</a></div>
            <div><a href="/users/'. $user['id'] .'/my_low/">Юридические услуги</a></div>
        </div>';
        return $left_menu;
    }
}