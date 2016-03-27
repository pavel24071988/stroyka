<?php

class Application
{    
    public static $URL;
    public static $DB;

    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] .'/functions/db.php';
        self::$URL = explode('/', $_SERVER['QUERY_STRING']);
        self::$DB = $DB;
    }

    public static function get_content(){
        $newURL = self::$URL;
        if($newURL[0] === '' && empty($newURL[1])){
            $curURL = [];
            $path_to_site = 'site/controllers/main';
        }else{
            $path_to_site = 'site/controllers/';
            $curURL = [];
            foreach($newURL as $key => $path){
                $check = preg_match('/^\d+$/', $path);
                if($key === 0) continue;
                if(empty($check)) $curURL[] = $path;
                else break;
            }
        }
        $path_to_site = $path_to_site .'/'. implode('/',$curURL) .'/page.php';
        if(!file_exists($path_to_site)){
            echo 'Не удалось подключить файл.';
            exit;
        }
        require_once $path_to_site;
    }
}