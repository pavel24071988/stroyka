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
        if(!empty(implode('/',$curURL))) $path_to_site = $path_to_site .'/'. implode('/',$curURL) .'page.php';
        else $path_to_site = $path_to_site .'/page.php';
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
}