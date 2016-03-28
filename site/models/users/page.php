<?php

class usersModel
{
    private static $DB;
    
    function __construct(){
        self::$DB = Application::$DB;
    }
    
    public static function getMyOwnerObjects($userID){
        $objects = self::$DB->query('
            SELECT o.*,
                   (SELECT COUNT(uo."objectID")
                      FROM users_objects uo
                        WHERE uo."objectID" = o.id) as responses
              FROM objects o
                WHERE o."createrUserID"='. $userID)->fetchAll();
        return $objects;
    }
    
    public static function getMyResponseObjects($userID){
        $objects = self::$DB->query('
            SELECT o.*, 
                   (SELECT COUNT(c.id)
                      FROM comments c
                        WHERE c."typeID" = o.id AND
                              c."type"=\'object_comment\') as comment_count
              FROM objects o
              LEFT JOIN users_objects uo ON o."id" = uo."objectID"
                WHERE uo."fromUserID"='. $userID)->fetchAll();
        return $objects;
    }
    
    public static function getMyOwnerJobs($userID){
        $jobs = self::$DB->query('
            SELECT j.*,
                   (SELECT COUNT(uj."jobID")
                      FROM users_jobs uj
                        WHERE uj."jobID" = j.id) as responses
              FROM jobs j
                WHERE j."createrUserID"='. $userID)->fetchAll();
        return $jobs;
    }
    
    public static function getMyResponseJobs($userID){
        $jobs = self::$DB->query('
            SELECT j.*, 
                   (SELECT COUNT(c.id)
                      FROM comments c
                        WHERE c."typeID" = j.id AND
                              c."type"=\'job_comment\') as comment_count
              FROM jobs j
              LEFT JOIN users_jobs uj ON j."id" = uj."jobID"
                WHERE uj."fromUserID"='. $userID)->fetchAll();
        return $jobs;
    }
}
