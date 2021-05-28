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
        global $msqlu_db ;
        $key = sanitize_sql_string($key);

        try {
            $sql = "SELECT id FROM `auth_keys` WHERE `key` = :a_key";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":a_key", $key);
            $statement->execute();

            return ($statement->rowCount() > 0) ? true : false;

        } catch (PDOException $e) {
            error_log("CORE: ". print_r( $e->getMessage(), true ));
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
        global $msqlu_db;
        $setting_name = sanitize_sql_string($setting_name);

        try {
            $sql = "SELECT setting_value FROM `settings` WHERE `setting_name` = :setting_name";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":setting_name", $setting_name);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                return $row['setting_value'];
            }

        } catch (PDOException $e) {
            error_log("CORE: ". print_r( $e->getMessage(), true ));
        }
        
        return false;
    }

    public static function requestURL($uri, $auth, $data, $method = "POST"){
        $ch = curl_init($uri);

        if($method == "POST"){
            curl_setopt($ch, CURLOPT_POST, 1);
        }elseif($method == "PUT"){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }
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

    public static function moneyFormat($amount, $decimals = 3, $no_format = 0){
        if(empty($amount)){
            return "0.00";
        }
        return ($no_format == 0) ? number_format($amount / 100000000, $decimals) : ($amount / 100000000);
    }
    
}