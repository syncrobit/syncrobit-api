<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

class SB_PROVISIONING{
    
    public static function checkIP($key){
        global $msqlu_db;
        $userIP = SB_WATCHDOG::getUserIP();
        $key = sanitize_sql_string($key);

        try {
            $sql = "SELECT allowed_ip FROM `auth_keys` WHERE `key` = :a_key";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":a_key", $key);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);

                $allowed_ips = explode(";", $row['allowed_ip']);
                return (in_array($userIP, $allowed_ips));
            }

        } catch (PDOException $e) {
            error_log("PROVISIONING: ". print_r( $e->getMessage(), true ));
        }
        
        return false;

    }

    public static function insertUnit($arr){
        global $msqlu_db;
        $required = array('rpi_sn', 'ecc_sn', 'pub_key', 'eth_mac', 'wlan_mac');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $ecc_sn     = sanitize_sql_string($arr['ecc_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);
        $onboarding = sanitize_sql_string($arr['address']);
        $miner_name = sanitize_sql_string($arr['name']);
        $sb_sn      = sanitize_sql_string($arr['sb_sn']);
        

        try {
            $sql = "INSERT INTO `units` (`rpi_sn`, `ecc_sn`, `pub_key`, `onboard_address`, `miner_name`, `eth_mac`, `wlan_mac`, `sb_sn`, `created_on`)
                    VALUES (:rpi_sn, :ecc_sn, :pub_key, :onboard_address, :miner_name, :eth_mac, :wlan_mac, :sb_sn, NOW())";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":ecc_sn", $ecc_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            $statement->bindParam(":onboard_address", $onboarding);
            $statement->bindParam(":miner_name", $miner_name);
            $statement->bindParam(":sb_sn", $sb_sn);
            
            return $statement->execute();

        } catch (PDOException $e) {
            error_log("PROVISIONING: ". print_r( $e->getMessage(), true ));
        }
        
        return false;
    }

    public static function checkIfRpiSN($rpi_sn){
        global $msqlu_db;
        $rpi_sn = sanitize_sql_string($rpi_sn);

        try {
            $sql = "SELECT * FROM `units` WHERE `rpi_sn` = :rpi_sn";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

            return ($statement->rowCount() > 0);

        } catch (PDOException $e) {
            error_log("PROVISIONING: ". print_r( $e->getMessage(), true ));
        }
        
        return false;
    }

    public static function insertAlienUnit($arr){
        global $msqlu_db;
        $required = array('rpi_sn', 'pub_key', 'eth_mac', 'wlan_mac', 'swarm_key');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);
        $swarm_key  = sanitize_sql_string($arr['swarm_key']);
        $onboarding = sanitize_sql_string($arr['address']);
        $miner_name = sanitize_sql_string($arr['name']);

        try {
            $sql = "INSERT INTO `units` (`rpi_sn`, `pub_key`, `onboard_address`, `miner_name`, `eth_mac`, `wlan_mac`, `diy`, `swarm_key`, `created_on`, `onboarded_hs`)
                    VALUES (:rpi_sn, :pub_key, :onboard_address, :miner_name, :eth_mac, :wlan_mac, :diy, :swarm_key, NOW(), 1)";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            $statement->bindValue(":diy", 1);
            $statement->bindParam(":swarm_key", $swarm_key);
            $statement->bindParam(":onboard_address", $onboarding);
            $statement->bindParam(":miner_name", $miner_name);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log("PROVISIONING: ". print_r( $e->getMessage(), true ));
        }
        
        return false;
    }

    public static function updateAlienUnit($arr){
        global $msqlu_db;
        $required = array('rpi_sn', 'pub_key', 'eth_mac', 'wlan_mac', 'swarm_key');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        $rpi_sn     = sanitize_sql_string($arr['rpi_sn']);
        $pub_key    = sanitize_sql_string($arr['pub_key']);
        $eth_mac    = sanitize_sql_string($arr['eth_mac']);
        $wlan_mac   = sanitize_sql_string($arr['wlan_mac']);

        try {
            $sql = "UPDATE `units` SET `pub_key` = :pub_key, `eth_mac` = :eth_mac, `wlan_mac` = :wlan_mac, `swarm_key` = :swarm_key 
                    `onboard_address` = :onboard_address, `miner_name` = :miner_name WHERE `rpi_sn` = :rpi_sn";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->bindParam(":pub_key", $pub_key);
            $statement->bindParam(":eth_mac", $eth_mac);
            $statement->bindParam(":wlan_mac", $wlan_mac);
            $statement->bindParam(":swarm_key", $swarm_key);
            $statement->bindParam(":onboard_address", $onboarding);
            $statement->bindParam(":miner_name", $miner_name);

            return $statement->execute();

        } catch (PDOException $e) {
            error_log("PROVISIONING: ". print_r( $e->getMessage(), true ));
        }
        
        return false;
    }

}