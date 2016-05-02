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
                WHERE o."createrUserID"='. $userID .'
                AND o.type_of_kind<>2')->fetchAll();
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
                WHERE uo."fromUserID"='. $userID .'
                AND o.type_of_kind<>2')->fetchAll();
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
    
    public static function getIncomeMessages($userID){
        $messages = self::$DB->query('
            SELECT DISTINCT ON ("id") u."id",
                   u."avatar",
                   u."name",
                   u."surname",
                   m."text",
                   m."created",
                   m."read"
              FROM messages m
              JOIN users u ON m."fromUserID" = u."id"
                WHERE m."toUserID"='. $userID .'
                  ORDER BY "id", m."read"')->fetchAll();
        return $messages;
    }
    
    public static function getOutcomeMessages($userID){
        $messages = self::$DB->query('
            SELECT DISTINCT ON ("id") u."id",
                   u."avatar",
                   u."name",
                   u."surname",
                   m."text",
                   m."created",
                   m."read"
              FROM messages m
              JOIN users u ON m."toUserID" = u."id"
                WHERE m."fromUserID"='. $userID .'
                  ORDER BY "id", m."read"')->fetchAll();
        return $messages;
    }
    
    public static function getHistoryOfMessagesByUser($firstUser, $secondUser){
        $messages = self::$DB->query('
            SELECT u."id",
                   u."avatar",
                   u."name",
                   u."surname",
                   m."text",
                   m."created"
              FROM messages m
              JOIN users u ON m."fromUserID" = u."id"
                WHERE (m."fromUserID"='. $firstUser .' AND m."toUserID"='. $secondUser .') OR
                      (m."fromUserID"='. $secondUser .' AND m."toUserID"='. $firstUser .')
                  ORDER BY m."created"')->fetchAll();
        return $messages;
    }
    
    public static function getUser($userID){
        return self::$DB->query('SELECT * FROM users WHERE id='. $userID)->fetchAll();
    }
    
    public static function setMessage($fromUserID, $toUserID, $text){
        $create_sql = self::$DB->prepare('
            INSERT INTO messages ("fromUserID", "toUserID", "text")
              VALUES(\''. $fromUserID .'\',
                     \''. $toUserID .'\',
                     \''. $text .'\')');
        if($create_sql->execute() === true) return 'Сообщение отправлено.';
        else return 'Ошибка отправки сообщения.';
    }
    
    public static function setReadableMessages($fromUserID, $toUserID){
        $messages = self::$DB->query('SELECT * FROM messages WHERE "fromUserID"='. $toUserID .' AND "toUserID"='. $fromUserID)->fetchAll();
        foreach($messages as $message) self::$DB->prepare('UPDATE messages SET "read"=\'on\' WHERE id='. $message['id'])->execute();
    }
}
