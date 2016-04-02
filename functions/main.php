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
        $path_to_site_model = str_replace('controllers', 'models', $path_to_site);
        
        if(!file_exists($path_to_site)){
            echo 'Не удалось подключить контроллер.';
            exit;
        }
        if(!file_exists($path_to_site_model)){
            echo 'Не удалось подключить модель.';
            exit;
        }
        
        // логируем заходы пользователей
        try{
            $userID = 0;
            if(!empty($_SESSION['user'])) $userID = $_SESSION['user']['id'];
            $log_entrance = self::$DB->prepare('
              INSERT INTO logs (userid, url)
                VALUES('. $userID .', \''. $path_to_site .'\')');
            $log_entrance->execute();
        }catch(Exception $ex){}
        
        // подключаем базовую модель
        require_once $path_to_site_model;
        // подключаем базовый контроллер
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
    
    public static function getCountsUserMessages($userID){
        // новые сообщения - это не прочитанные сообщения - т.е на страницу которых мы не переходили
        $messages = self::$DB->query('
            SELECT
              (SELECT COUNT(id) FROM messages WHERE "toUserID"=\''. $userID .'\') as count_all,
              (SELECT COUNT(id) FROM messages WHERE read = \'off\' AND "toUserID"=\''. $userID .'\') as count_new
        ')->fetchAll();
        return $messages;
    }
    
    public static function getListOfAreas($type, $id){
        // получаем сферы деятильности с подвидами
        $area_of_jobs = self::$DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
        // найдем все сферы деятельности по id и type, например по пользователю
        $kinds_of_jobs_user = self::$DB->query('
            SELECT *
              FROM users_kinds_of_jobs ukj
              LEFT JOIN kinds_of_jobs kj ON ukj.kind_of_job_id = kj.id
                WHERE ukj."userID"='. $id .'
        ')->fetchAll();
        $kinds_of_jobs_user_arr = [];
        foreach($kinds_of_jobs_user as $kind_of_job_user){
            $kinds_of_jobs_user_arr[] = $kind_of_job_user['areaID']  .'_'. $kind_of_job_user['kind_of_job_id'];
        }
        
        $list_of_areas = '<ul>';
        foreach($area_of_jobs as $key => $area_of_job){
            $list_of_areas .= '<li>'. $area_of_job['name'];
            $kinds_of_jobs = self::$DB->query('SELECT * FROM kinds_of_jobs kj WHERE kj."areaID"='. $area_of_job['id'])->fetchAll();
            if(!empty($kinds_of_jobs)) $list_of_areas .= '<ul ">';
            foreach($kinds_of_jobs as $key => $kind_of_job){
                $identificator = $area_of_job['id'] .'_'. $kind_of_job['id'];
                $checked = in_array($identificator, $kinds_of_jobs_user_arr) ? 'checked' : '';
                $list_of_areas .= '<li><input type="checkbox" name="areas_for_'. $type .'[]" value="'. $kind_of_job['id'] .'" '. $checked .'>'. $kind_of_job['name'] .'</li>';
            }
            if(!empty($kinds_of_jobs)) $list_of_areas .= '</ul>';
            $list_of_areas .= '</li>';
        }
        $list_of_areas .= '</ul>';
        return $list_of_areas;
    }
}