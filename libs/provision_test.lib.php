<?php

class SB_PROVISION_TEST{
    public static function checkIfUnitExists($rpi_sn){
        $rpi_sn = sanitize_sql_string($rpi_sn);

        try {
            $sql = "SELECT * FROM `unit_tests` WHERE `rpi_sn` = :rpi_sn";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

            return ($statement->rowCount() > 0);

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }

    public static function insertTestResults($arr){
        $required = array('rpi_sn', 'eth', 'wlan', 'ble', 'ecc', 'radio');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $eth        = sanitize_sql_string($arr['eth']);
        $wlan       = sanitize_sql_string($arr['wlan']);
        $ble        = sanitize_sql_string($arr['ble']);
        $ecc        = sanitize_sql_string($arr['ecc']);
        $radio      = sanitize_sql_string($arr['radio']);

        try {
            $sql = "INSERT INTO `unit_tests` (`rpi_sn`, `eth`, `wlan`, `ble`, `ecc`, `radio`, `tested_on`)
                    VALUES (:rpi_sn, :eth, :wlan, :ble, :ecc, :radio, NOW())";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":eth", $eth);
            $statement->bindParam(":wlan", $wlan);
            $statement->bindParam(":ble", $ble);
            $statement->bindParam(":ecc", $ecc);
            $statement->bindParam(":radio", $radio);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log( 'API: ' . print_r( $e->getMessage(), true ) );
        }
        
        return false;

    }

    public static function updateTests($arr){
        $required = array('rpi_sn', 'eth', 'wlan', 'ble', 'ecc', 'radio');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $eth        = sanitize_sql_string($arr['eth']);
        $wlan       = sanitize_sql_string($arr['wlan']);
        $ble        = sanitize_sql_string($arr['ble']);
        $ecc        = sanitize_sql_string($arr['ecc']);
        $radio      = sanitize_sql_string($arr['radio']);

        try {
            $sql = "UPDATE `unit_tests` SET `eth` = :eth, `wlan` = :wlan, `ble` = :ble, `ecc` = :ecc, `radio` = :radio, `update_on` = NOW() WHERE `rpi_sn` = :rpi_sn";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":eth", $eth);
            $statement->bindParam(":wlan", $wlan);
            $statement->bindParam(":ble", $ble);
            $statement->bindParam(":ecc", $ecc);
            $statement->bindParam(":radio", $radio);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log( 'API: ' . print_r( $e->getMessage(), true ) );
        }
        
        return false;
    }
}