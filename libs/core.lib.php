<?php


class SB_CORE {
    public static function checkIfAuthHeaderExists($header){
        return (!isset($header) && empty($header)) ? false : true;
    }

    public static function unitCheckAuth($key){
        try {
            $sql = "SELECT id FROM `auth_keys` WHERE `key` = :a_key";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":a_key", $key);
            $statement->execute();

            return ($statement->rowCount() > 0) ? true : false;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }

    public static function getRoutes($route){
        global $response;

        if(file_exists(SB_MODULES."/".$route.".mod.php")){
            include SB_MODULES."/".$route.".mod.php";
        }else{
            $response['status'] = "Route does not exist";
            http_response_code(404);  
        }
    }
}