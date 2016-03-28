<?php
if(!empty($_SESSION['user']) && $_SESSION['user']['id'] === $common_data['job']['createrUserID']){
    $DB = Application::$DB;
    $del_sql = $DB->prepare('
        DELETE FROM jobs
          WHERE id='. $common_data['job']['id']);
    if($del_sql->execute() === true){
        echo '
            <script type="text/javascript">
                location.replace("/users/'. $_SESSION['user']['id'] .'/my_objects/");
            </script>
        ';
    }else{
        echo 'Вакансию не получилось удалить, попробуйте позже';
    }
    exit;
}else{
    echo 'Не достаточно прав для удаления.';
}
?>
<h1>Удалить вакансию</h1>