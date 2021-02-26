<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

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

    public static function getSettings($setting_name){
        try {
            $sql = "SELECT setting_value FROM `settings` WHERE `setting_name` = :setting_name";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":setting_name", $setting_name);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                return $row['setting_value'];
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }

    public static function requestURL($uri, $auth, $data){
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer '.$auth, 
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $output = curl_exec($ch);
        $info = curl_getinfo($ch); 

        return array("info" => $info, "response" => $output);
    }
}