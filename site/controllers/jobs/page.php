<?php
$applicationURL = Application::$URL;
if(!empty($applicationURL[2])){
    
    if($applicationURL[2] === 'add'){
        $type = 'job_update';
        $job = null;
    }else{
        $job = Application::$DB->query('
            SELECT j.*,
                   s."name" as s_name
              FROM jobs j
              LEFT JOIN schedules s ON j."scheduleID" = s."id"
                WHERE j."id"='. $applicationURL[2])->fetchAll();

        $type = 'job_page';
        $job = $job[0];

        if(!empty($applicationURL[3])){
            switch($applicationURL[3]){
                case 'edit':
                    $type = 'job_update';
                break;
                case 'delete':
                    $type = 'job_delete';
                break;
            }
        }
    }

    $common_data = [
        'type' => $type,
        'job' => $job
    ];
    get_page($common_data);
}else{    
    $common_data = ['type' => 'jobs'];
    get_page($common_data);
}

function get_page($common_data){
    require_once $_SERVER['DOCUMENT_ROOT'] .'/site/veiws/jobs/'. $common_data['type'] .'.php';
}