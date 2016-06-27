<?php

class Application
{
    public static $URL;
    public static $DB;

    public function __construct() {
        require_once $_SERVER['DOCUMENT_ROOT'] .'/db/db.php';
        self::$URL = explode('/', $_SERVER['REQUEST_URI']);
        self::$URL[count(self::$URL)-1] = '';
        self::$DB = $DB;
    }

    public static function get_content(){
        $newURL = self::$URL;
        if($newURL[0] === '' && empty($newURL[1])){
            $curURL = [];
            $path_to_site = 'site/controllers/main';
        }elseif(!empty($newURL[1]) && $newURL[1] === 'admin'){
            $path_to_site = 'admin/controllers';
            $curURL = [];
            foreach($newURL as $key => $path){
                $check = preg_match('/^\d+$/', $path);
                if($key === 0 || $path === 'admin') continue;
                if(empty($check)) $curURL[] = $path;
                else break;
            }
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
              INSERT INTO logs (userid, url, session_id)
                VALUES('. $userID .', \''. implode('/', $newURL) .'\', \''. session_id() .'\')');
            $log_entrance->execute();
        }catch(Exception $ex){}
        
        // подключаем базовую модель
        require_once $path_to_site_model;
        // подключаем базовый контроллер
        require_once $path_to_site;
    }
    
    public static function get_ajax(){
        if(!empty($_POST['ajax'])){
            require_once 'site/controllers/ajax/page.php';
        }
    }
    
    public static function getLeftMenu(){
        $user = $_SESSION['user'];
        $userMessages = self::getCountsUserMessages($_SESSION['user']['id']);
        if((int) $userMessages[0]['count_new'] > 0) $msgStr = '<li><a href="/users/'. $user['id'] .'/my_messages/" class="active">Сообщения |'. $userMessages[0]['count_new'] .'|</a></li>';
        else $msgStr = '<li><a href="/users/'. $user['id'] .'/my_messages/">Сообщения</a></li>';
        $left_menu = '
        <div class="my-page-navbar">
            <ul class="my-page-navbar-links">
                <li><a href="/users/'. $user['id'] .'/"">Мой паспорт</a></li>
                <li><a href="/users/'. $user['id'] .'/my_objects/">Объекты и вакансии</a></li>
                '. $msgStr .'
                <li><a href="/users/'. $user['id'] .'/my_settings/">Настройки</a></li>
                <li><a href="/users/'. $user['id'] .'/my_works/">Мои работы</a></li>
                <!--<li><a href="/users/'. $user['id'] .'/my_insurance/">Страхование</a></li>
                <li><a href="/users/'. $user['id'] .'/my_low/">Юридические услуги</a></li>-->
            </ul>
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
    
    public static function getListOfAreas($type, $id, $GET = []){
        // получаем сферы деятильности с подвидами
        $area_of_jobs = self::$DB->query('SELECT * FROM area_of_jobs aj')->fetchAll();
        $kinds_of_jobs_user_arr = [];
        if($type === 'user' && !is_null($id)){
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
        }
        
        $list_of_areas = '';
        foreach($area_of_jobs as $key => $area_of_job){
            $list_of_areas .= '<li><div class="searcher-categories-item"><label><input type=\'checkbox\'> '. $area_of_job['name'] .'</label></div>';
            $kinds_of_jobs = self::$DB->query('SELECT * FROM kinds_of_jobs kj WHERE kj."areaID"='. $area_of_job['id'])->fetchAll();
            if(!empty($kinds_of_jobs)) $list_of_areas .= '<ul class="searcher-sub-categories">';
            foreach($kinds_of_jobs as $key => $kind_of_job){
                $identificator = $area_of_job['id'] .'_'. $kind_of_job['id'];
                $liclass = '';
                $cheched = '';
                if(!empty($GET['areas_for_job'])) $kinds = $GET['areas_for_job'];
                elseif(!empty($GET['areas_for_object'])) $kinds = $GET['areas_for_object'];
                elseif(!empty($GET['areas_for_user'])) $kinds = $GET['areas_for_user'];
                
                if(!empty($kinds)){
                    $liclass = in_array((string) $kind_of_job['id'], $kinds) ? ' active' : '';
                    $cheched = in_array((string) $kind_of_job['id'], $kinds) ? 'checked' : '';
                }
                $list_of_areas .= '<li class=\''. $liclass .'\'><div class="searcher-categories-item"><label for="'. $kind_of_job['id'] .'"><input type=\'checkbox\' '. $cheched .' name="areas_for_'. $type .'[]" value="'. $kind_of_job['id'] .'" id="'. $kind_of_job['id'] .'" />'. $kind_of_job['name'] .'</label></div></li>';
            }
            if(!empty($kinds_of_jobs)) $list_of_areas .= '</ul>';
            $list_of_areas .= '</li>';
        }
        return $list_of_areas;
    }
    
    public static function getListOfProfessions($type, $id){
        // получаем сферы деятильности с подвидами
        $professions = self::$DB->query('SELECT * FROM professions p')->fetchAll();

        foreach($professions as $key => $profession){
            $list_of_areas .= '<li><div class="searcher-categories-item"><label><input type=\'checkbox\'> '. $area_of_job['name'] .'</label></div>';
            $kinds_of_jobs = self::$DB->query('SELECT * FROM kinds_of_jobs kj WHERE kj."areaID"='. $area_of_job['id'])->fetchAll();
            if(!empty($kinds_of_jobs)) $list_of_areas .= '<ul class="searcher-sub-categories">';
            foreach($kinds_of_jobs as $key => $kind_of_job){
                $identificator = $area_of_job['id'] .'_'. $kind_of_job['id'];
                $liclass = in_array($identificator, $kinds_of_jobs_user_arr) ? ' active' : '';
                $list_of_areas .= '<li class=\''. $liclass .'\'><div class="searcher-categories-item"><label for="'. $kind_of_job['id'] .'"><input type=\'checkbox\' name="areas_for_'. $type .'[]" value="'. $kind_of_job['id'] .'" id="'. $kind_of_job['id'] .'" />'. $kind_of_job['name'] .'</label></div></li>';
            }
            if(!empty($kinds_of_jobs)) $list_of_areas .= '</ul>';
            $list_of_areas .= '</li>';
        }
        return $list_of_professions;
    }
    
    public static function getPagePagination($type, $count, $GET){
        $lis = '';
        $dopUrl = [];
        if(empty($GET['pagination'])) $GET['pagination'] = 1;
        foreach($GET as $key => $param){
            if($key === 'pagination') continue;
            if($key === 'areas_for_job'){
                foreach($param as $area) $dopUrl[] = 'areas_for_job[]='. $area;
            }elseif($key === 'areas_for_user'){
                foreach($param as $area) $dopUrl[] = 'areas_for_user[]='. $area;
            }elseif($key === 'areas_for_object'){
                foreach($param as $area) $dopUrl[] = 'areas_for_object[]='. $area;
            }else{
                $dopUrl[] = $key .'='. $param;
            }
        }

        $dopUrl = implode('&', $dopUrl);

        $cysles = (int) ceil($count / 10);
        $class = '';
        $GET['pagination'] = (int) $GET['pagination'];
        $left = $GET['pagination'] - 8;
        $right = $GET['pagination'] + 8;
        if($left < 0) $right = 16;
        if($right < 0) $left = 16;
        for($i=1; $i<=$cysles; $i++){
            if($i < $left || $i > $right) continue;
            $class = $GET['pagination'] === $i ? 'active' : '';
            $lis .= '<li><a href="/'. $type .'/?pagination='. $i .'&'. $dopUrl .'" class="'. $class .'">'. $i .'</a></li>';
        }
        
        $paginationleft = '<a href="/'. $type .'/?pagination='. ($GET['pagination'] - 1) .'&'. $dopUrl .'" class="pagination-left"></a>';
        $paginationright = '<a href="/'. $type .'/?pagination='. ($GET['pagination'] + 1) .'&'. $dopUrl .'" class="pagination-right"></a>';
        
        if($GET['pagination'] === 1) $paginationleft = '';
        if($GET['pagination'] === $cysles) $paginationright = '';
        
        echo $paginationleft .'<ul class="pagination-pages">'. $lis .'</ul>'. $paginationright;
    }
    
    public static function checkAdmin(){
        if(!empty($_POST['Login']) && !empty($_POST['Password']) && $_POST['Login'] === 'admin' && $_POST['Password'] === 'admin'){
            $_SESSION['admin']['Login'] = $_POST['Login'];
            $_SESSION['admin']['Password'] = $_POST['Password'];
        }
        if(empty($_SESSION['admin'])){
            echo '<div style="color: red;">Необходимо авторизоваться под админом.</div>
                <form method="POST">
                    <input type="text" name="Login"><br/>
                    <input type="text" name="Password"><br/>
                    <input type="submit" name="admin_auth">
                </form>
            ';
            exit;
        }
    }
    
    /**
    * Масштабирование изображения
    *
    * Функция работает с PNG, GIF и JPEG изображениями.
    * Масштабирование возможно как с указаниями одной стороны, так и двух, в процентах или пикселях.
    *
    * @param string Расположение исходного файла
    * @param string Расположение конечного файла
    * @param integer Ширина конечного файла
    * @param integer Высота конечного файла
    * @param bool Размеры даны в пискелях или в процентах
    * @return bool
    */
    public static function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
            list($w_i, $h_i, $type) = getimagesize($file_input);
            if (!$w_i || !$h_i) {
                    echo 'Невозможно получить длину и ширину изображения';
                    return;
        }
        $types = array('','gif','jpeg','png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img = $func($file_input);
        } else {
            echo 'Некорректный формат файла';
                    return;
        }
            if ($percent) {
                    $w_o *= $w_i / 100;
                    $h_o *= $h_i / 100;
            }
            if (!$h_o) $h_o = $w_o/($w_i/$h_i);
            if (!$w_o) $w_o = $h_o/($h_i/$w_i);
            $img_o = imagecreatetruecolor($w_o, $h_o);
            imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);
            if ($type == 2) {
                    return imagejpeg($img_o,$file_output,100);
            } else {
                    $func = 'image'.$ext;
                    return $func($img_o,$file_output);
            }
    }

    /**
    * Обрезка изображения
    *
    * Функция работает с PNG, GIF и JPEG изображениями.
    * Обрезка идёт как с указанием абсоютной длины, так и относительной (отрицательной).
    *
    * @param string Расположение исходного файла
    * @param string Расположение конечного файла
    * @param array Координаты обрезки
    * @param bool Размеры даны в пискелях или в процентах
    * @return bool
    */
    function crop($file_input, $file_output, $crop = 'square',$percent = false) {
            list($w_i, $h_i, $type) = getimagesize($file_input);
            if (!$w_i || !$h_i) {
                    echo 'Невозможно получить длину и ширину изображения';
                    return;
        }
        $types = array('','gif','jpeg','png');
        $ext = $types[$type];
        if ($ext) {
            $func = 'imagecreatefrom'.$ext;
            $img = $func($file_input);
        } else {
            echo 'Некорректный формат файла';
                    return;
        }
            if ($crop == 'square') {
                    $min = $w_i;
                    if ($w_i > $h_i) $min = $h_i;
                    $w_o = $h_o = $min;
            } else {
                    list($x_o, $y_o, $w_o, $h_o) = $crop;
                    if ($percent) {
                            $w_o *= $w_i / 100;
                            $h_o *= $h_i / 100;
                            $x_o *= $w_i / 100;
                            $y_o *= $h_i / 100;
                    }
            if ($w_o < 0) $w_o += $w_i;
                $w_o -= $x_o;
                    if ($h_o < 0) $h_o += $h_i;
                    $h_o -= $y_o;
            }
            $img_o = imagecreatetruecolor($w_o, $h_o);
            imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);
            if ($type == 2) {
                    return imagejpeg($img_o,$file_output,100);
            } else {
                    $func = 'image'.$ext;
                    return $func($img_o,$file_output);
            }
    }
    
    // найдем баннер для рекламы
    public static function findBanner($selectedAreaID, $selectedCityID){
        $banner = self::$DB->query('
            SELECT *,
            (SELECT DISTINCT laa.area_id FROM links_areas_advertising laa WHERE laa.advertising_id = a.id AND laa.area_id = '. $selectedAreaID .' LIMIT 1) as targetAreaCheck,
            (SELECT DISTINCT lca.city_id FROM links_cities_advertising lca WHERE lca.advertising_id = a.id AND lca.city_id = '. $selectedCityID .' LIMIT 1) as targetCityCheck
              FROM advertising a
                WHERE a.switchon < now() AND
                      a.switchoff > now() AND
                      a.type=\'banner\' AND
                      a.id IN (SELECT laa.advertising_id FROM links_areas_advertising laa WHERE laa.advertising_id = a.id AND (laa.area_id = '. $selectedAreaID .' OR laa.area_id = -1)) AND
                      a.id IN (SELECT lca.advertising_id FROM links_cities_advertising lca WHERE lca.advertising_id = a.id AND (lca.city_id = '. $selectedCityID .' OR lca.city_id = -1))
                  ORDER BY targetAreaCheck, targetCityCheck
        ')->fetch();

        $text = self::$DB->query('
            SELECT *,
            (SELECT DISTINCT laa.area_id FROM links_areas_advertising laa WHERE laa.advertising_id = a.id AND laa.area_id = '. $selectedAreaID .' LIMIT 1) as targetAreaCheck,
            (SELECT DISTINCT lca.city_id FROM links_cities_advertising lca WHERE lca.advertising_id = a.id AND lca.city_id = '. $selectedCityID .' LIMIT 1) as targetCityCheck
              FROM advertising a
                WHERE a.switchon < now() AND
                      a.switchoff > now() AND
                      a.type=\'text\' AND
                      a.id IN (SELECT laa.advertising_id FROM links_areas_advertising laa WHERE laa.advertising_id = a.id AND (laa.area_id = '. $selectedAreaID .' OR laa.area_id = -1)) AND
                      a.id IN (SELECT lca.advertising_id FROM links_cities_advertising lca WHERE lca.advertising_id = a.id AND (lca.city_id = '. $selectedCityID .' OR lca.city_id = -1))
                  ORDER BY targetAreaCheck, targetCityCheck
        ')->fetch();

        $bannerHTML = '';
        if(!empty($banner)){
            $bannerHTML .= '<a href="'. $banner['img_banner_link'] .'" target="_blank"><img src="/images/advertisings/'. $banner['id'] .'/'. $banner['src'] .'"></a>';
        }
        if(!empty($text)){
            $bannerHTML .= '<div style="color: red;">'. $text['description'] .'</div>';
        }
        
        return $bannerHTML;
    }
}