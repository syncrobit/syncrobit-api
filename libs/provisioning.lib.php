<?php
class SB_PROVISIONING{
    public static function checkAuth($key){
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

    public static function checkIP($key){
        $userIP = SB_WATCHDOG::getUserIP();

        try {
            $sql = "SELECT allowed_ip FROM `auth_keys` WHERE `key` = :a_key";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
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
        $required = array('rpi_sn', 'ecc_sn', 'pub_key', 'eth_mac', 'wlan_mac');
        if(SB_WATCHDOG::checkFields($required, $arr)){
            return false;
        }

        try {
            $sql = "INSERT INTO `units` (`rpi_sn`, `ecc_sn`, `pub_key`, `eth_mac`, `wlan_mac`)
                    VALUES (:rpi_sn, :ecc_sn, :pub_key, :eth_mac, :wlan_mac)";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $arr['rpi_sn']);
            $statement->bindParam(":ecc_sn", $arr['ecc_sn']);
            $statement->bindParam(":pub_key", $arr['pub_key']);
            $statement->bindParam(":eth_mac", $arr['eth_mac']);
            $statement->bindParam(":wlan_mac", $arr['wlan_mac']);
            
            return $statement->execute();

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return false;
    }
}