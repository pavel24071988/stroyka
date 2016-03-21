<?php

class Application
{    
    public static $URL;
    public static $DB;

    public function __construct() {
        require_once '/functions/db.php';
        self::$URL = explode('/', $_SERVER['REQUEST_URI']);
        self::$DB = $DB;
    }

    public static function get_content(){
        $newURL = self::$URL;
        if($newURL[0] === '') $path_to_site = 'site/controllers';
        unset($newURL[0]);
        $path_to_site = $path_to_site .'/'. implode('/',$newURL) .'page.php';
        if(!file_exists($path_to_site)){
            echo 'Не удалось подключить файл.';
            exit;
        }
        require_once $path_to_site;
    }
}