<?php 
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

class SB_ERROR_REP{
    public static function insertError($arr){
        $required = array('rpi_sn', 'error', 'reported_by');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        try {
            $sql = "INSERT INTO `watchdog` (`rpi_sn`, `error`, `time_stamp`, `reported_by`)
                    VALUES (:rpi_sn, :error, NOW(), :reported_by)";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $arr['rpi_sn']);
            $statement->bindParam(":error", $arr['error']);
            $statement->bindParam(":reported_by", $arr['reported_by']);
            
            return $statement->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}