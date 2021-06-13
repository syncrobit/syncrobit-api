<?php 
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

class SB_WATCHDOG{
    public static function getUserIP(){
        $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])){
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    }else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else if(isset($_SERVER['HTTP_X_FORWARDED'])){
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    }else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])){
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    }else if(isset($_SERVER['HTTP_FORWARDED_FOR'])){
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    }else if(isset($_SERVER['HTTP_FORWARDED'])){
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    }else if(isset($_SERVER['REMOTE_ADDR'])){
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }else{
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
    }

    public static function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function checkFields($required, $arr){
        $error = false;
        foreach($required as $field) {
            if (strlen($arr[$field]) == 0) {
                $error = true;
            }
        }
        
        return $error;
    }

    public static function checkIfUnitIsActive($rpi_sn){
        global $msqlu_db;
        $rpi_sn = sanitize_sql_string($rpi_sn);

        try {
            $sql = "SELECT id FROM `unit` WHERE `rpi_sn` = :rpi_sn AND `active` = 1";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

           return ($statement->rowCount() > 0) ? true : false;

        } catch (PDOException $e) {
            error_log( 'WATCHDOG: ' . print_r( $e->getMessage(), true ) );
        }

        return false;

    }
}