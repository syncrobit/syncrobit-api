<?php 
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

class SB_ERROR_REP{
    public static function insertError($arr){
        global $msqlu_db;
        $required = array('rpi_sn', 'error', 'reported_by');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn         = sanitize_sql_string($arr['rpi_sn']);
        $error          = sanitize_sql_string($arr['error']);
        $reported_by    = sanitize_sql_string($arr['reported_by']);
        
        try {
            $sql = "INSERT INTO `watchdog` (`rpi_sn`, `error`, `time_stamp`, `reported_by`)
                    VALUES (:rpi_sn, :error, NOW(), :reported_by)";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":error", $error);
            $statement->bindParam(":reported_by", $reported_by);
            
            return $statement->execute();

        } catch (PDOException $e) {
            error_log("ERROR REPORT: ". print_r( $e->getMessage(), true ));
        }
    }
}