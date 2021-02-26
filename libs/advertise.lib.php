<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

 class SB_ADVERTISE{
    public static function getRecord($rpi_sn){
        try {
            $sql = "SELECT record_id FROM `unit_dns` WHERE `rpi_sn` = :rpi_sn";
            $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
            $statement = $db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                return $row['record_id'];
            }

        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return false;
    }

    public static function insertDbRecord($rpi_sn, $ipAddr){
        $record_id = self::createRemoteNS($rpi_sn, $ipAddr);

        if($record_id != false){
            try {
                $sql = "INSERT INTO `unit_dns` (`rpi_sn`, `ip`, `record_id`) VALUES (:rpi_sn, :ip, :record_id)";
                $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
                $statement = $db->prepare($sql);
                $statement->bindParam(":rpi_sn", $rpi_sn);
                $statement->bindParam(":ip", $ipAddr);
                $statement->bindParam(":record_id", $record_id);

                return $statement->execute();

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        return false;
    }

    public static function updateDbIP($rpi_sn, $ipAddr, $record_id){
        if(self::updateRemoveNS($record_id, $ipAddr) != false){
            try {
                $sql = "UPDATE `unit_dns` SET `ip`= :ip WHERE `rpi_sn` = :rpi_sn";
                $db = new PDO("mysql:host=".SB_DB_HOST.";dbname=".SB_DB_UNITS, SB_DB_USER, SB_DB_PASSWORD);
                $statement = $db->prepare($sql);
                $statement->bindParam(":rpi_sn", $rpi_sn);
                $statement->bindParam(":ip", $ipAddr);

                return $statement->execute();

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return false;
    }

    public static function createRemoteNS($rpi_sn, $ipAddr){
        $api        = SB_CORE::getSettings('linode_api');
        $apiKey     = SB_CORE::getSettings('api_key');
        $domainID   = SB_CORE::getSettings('domain_id');
        
        $uri = $api.$domainID."/records";
        $req = array(
            "type"      => "A",
            "name"      => "cham-".$rpi_sn,
            "target"    => $ipAddr,
            "priority"  => 50,
            "weight"    => 50,
            "port"      => 80,
            "service"   => null,
            "protocol"  => null,
            "ttl_sec"   => 300
        );

        $req = json_encode($req);
        $response = SB_CORE::requestURL($uri, $apiKey, $req);
        $res_body = json_decode($response['response'], true);

        if($response['info']['http_code'] == 200 && !empty($res_body['id'])){
            return $res_body['id'];
        }

        return false;
    }

    public static function updateRemoveNS($record_id, $ipAddr){
        $api        = SB_CORE::getSettings('linode_api');
        $apiKey     = SB_CORE::getSettings('api_key');
        $domainID   = SB_CORE::getSettings('domain_id');

        $uri = $api.$domainID."/records/".$record_id;
        $req = array(
            "target"    => $ipAddr,
            "ttl_sec"   => 300
        );

        $req = json_encode($req);
        $response = SB_CORE::requestURL($uri, $apiKey, $req);
        $res_body = json_decode($response['response'], true);

        if($response['info']['http_code'] == 200 && !empty($res_body['id'])){
            return $res_body['id'];
        }

        return false;
    }


 }

 ?>