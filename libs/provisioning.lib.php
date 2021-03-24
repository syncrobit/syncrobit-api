<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

class SB_PROVISIONING{
    
    public static function checkIP($key){
        global $msql_db;
        $userIP = SB_WATCHDOG::getUserIP();
        $key = sanitize_sql_string($key);

        try {
            $sql = "SELECT allowed_ip FROM `auth_keys` WHERE `key` = :a_key";
            $statement = $msql_db->prepare($sql);
            $statement->bindParam(":a_key", $key);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);

                $allowed_ips = explode(";", $row['allowed_ip']);
                return (in_array($userIP, $allowed_ips));
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;

    }

    public static function insertUnit($arr){
        global $msql_db;
        $required = array('rpi_sn', 'ecc_sn', 'pub_key', 'eth_mac', 'wlan_mac');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $ecc_sn     = sanitize_sql_string($arr['ecc_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);

        try {
            $sql = "INSERT INTO `units` (`rpi_sn`, `ecc_sn`, `pub_key`, `eth_mac`, `wlan_mac`, `created_on`)
                    VALUES (:rpi_sn, :ecc_sn, :pub_key, :eth_mac, :wlan_mac, NOW())";
            $statement = $msql_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":ecc_sn", $ecc_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            
            return $statement->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }

    public static function checkIfRpiSN($rpi_sn){
        global $msql_db;
        $rpi_sn = sanitize_sql_string($rpi_sn);

        try {
            $sql = "SELECT * FROM `units` WHERE `rpi_sn` = :rpi_sn";
            $statement = $msql_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

            return ($statement->rowCount() > 0);

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }

    public static function insertAlienUnit($arr){
        global $msql_db;
        $required = array('rpi_sn', 'pub_key', 'eth_mac', 'wlan_mac', 'swarm_key');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);
        $swarm_key  = sanitize_sql_string($arr['swarm_key']);

        try {
            $sql = "INSERT INTO `units` (`rpi_sn`, `pub_key`, `eth_mac`, `wlan_mac`, `diy`, `swarm_key`, `created_on`)
                    VALUES (:rpi_sn, :pub_key, :eth_mac, :wlan_mac, :diy, :swarm_key, NOW())";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $msql_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            $statement->bindValue(":diy", 1);
            $statement->bindParam(":swarm_key", $swarm_key);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log("AlIEN UNIT: ". $e->getMessage());
        }
        
        return false;
    }

    public static function updateAlienUnit($arr){
        global $msql_db;
        $required = array('rpi_sn', 'pub_key', 'eth_mac', 'wlan_mac', 'swarm_key');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);

        try {
            $sql = "UPDATE `units` SET `pub_key` = :pub_key, `eth_mac` = :eth_mac, `wlan_mac` = :wlan_mac, `swarm_key` = :swarm_key WHERE `rpi_sn` = :rpi_sn";
            $statement = $msql_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            $statement->bindParam(":swarm_key", $swarm_key);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log("AlIEN UNIT: ". $e->getMessage());
        }
        
        return false;
    }

}