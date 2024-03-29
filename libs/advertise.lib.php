<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

 class SB_ADVERTISE{
    public static function getRecord($rpi_sn){
        global $msqlu_db;
        $rpi_sn = sanitize_sql_string($rpi_sn);

        try {
            $sql = "SELECT record_id, internal_id FROM `unit_dns` WHERE `rpi_sn` = :rpi_sn";
            $statement = $msqlu_db->prepare($sql);
            $statement->bindParam(":rpi_sn", $rpi_sn);
            $statement->execute();

            if($statement->rowCount() > 0){
                $row = $statement->fetch(PDO::FETCH_ASSOC);
                return array("rID" => $row['record_id'], "iID" => $row['internal_id']);
            }

        } catch (PDOException $e) {
            error_log( 'ADVERTISE: ' . print_r( $e->getMessage(), true ) );
        }

        return false;
    }

    public static function insertDbRecord($rpi_sn, $ipAddr, $vpnIP){
        global $msqlu_db;
        $record_id      = self::createRemoteNS($rpi_sn, $ipAddr);
        $internal_id    = self::createInternalNS($rpi_sn, $vpnIP);

        $rpi_sn         = sanitize_sql_string($rpi_sn);
        $ipAddr         = sanitize_sql_string($ipAddr);
        $record_id      = sanitize_sql_string($record_id);
        $vpnIP          = sanitize_sql_string($vpnIP);
        $internal_id    = sanitize_sql_string($internal_id);

        if($record_id != false || $internal_id != false){
            try {
                $sql = "INSERT INTO `unit_dns` (`rpi_sn`, `ip`, `vpn_ip`, `record_id`, `internal_id`, `last_updated`) 
                        VALUES (:rpi_sn, :ip, :vpn_ip, :record_id, :internal_id, NOW())";
                $statement = $msqlu_db->prepare($sql);
                $statement->bindParam(":rpi_sn", $rpi_sn);
                $statement->bindParam(":ip", $ipAddr);
                $statement->bindParam(":record_id", $record_id);
                $statement->bindParam(":vpn_ip", $vpnIP);
                $statement->bindParam(":internal_id", $internal_id);

                return $statement->execute();

            } catch (PDOException $e) {
                error_log( 'ADVERTISE: ' . print_r( $e->getMessage(), true ) );
            }
        }

        return false;
    }

    public static function updateDbIP($rpi_sn, $ipAddr, $vpnIP, $record_id, $internal_id){
        global $msqlu_db;
        $remoteNS   = self::updateRemoteNS($record_id, $ipAddr);
        $internalNS = self::updateInternalNS($internal_id, $vpnIP);

        $rpi_sn     = sanitize_sql_string($rpi_sn);
        $ipAddr     = sanitize_sql_string($ipAddr);
        $vpnIP      = sanitize_sql_string($vpnIP);

        if($remoteNS != false && $internalNS != false){
            try {
                $sql = "UPDATE `unit_dns` SET `ip` = :ip, `vpn_ip` = :vpn_ip, `last_updated` = NOW() WHERE `rpi_sn` = :rpi_sn";
                $statement = $msqlu_db->prepare($sql);
                $statement->bindParam(":rpi_sn", $rpi_sn);
                $statement->bindParam(":ip", $ipAddr);
                $statement->bindParam(":vpn_ip", $vpnIP);

                return $statement->execute();

            } catch (PDOException $e) {
                error_log( 'ADVERTISE: ' . print_r( $e->getMessage(), true ) );
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

    public static function updateRemoteNS($record_id, $ipAddr){
        $api        = SB_CORE::getSettings('linode_api');
        $apiKey     = SB_CORE::getSettings('api_key');
        $domainID   = SB_CORE::getSettings('domain_id');

        $uri = $api.$domainID."/records/".$record_id;
        $req = array(
            "target"    => $ipAddr,
            "ttl_sec"   => 300
        );

        $req = json_encode($req);
        $response = SB_CORE::requestURL($uri, $apiKey, $req, "PUT");
        $res_body = json_decode($response['response'], true);
        
        if($response['info']['http_code'] == 200 && !empty($res_body['id'])){
            return $res_body['id'];
        }

        return false;
    }

    public static function createInternalNS($rpi_sn, $vpnAddr){
        $api        = SB_CORE::getSettings('linode_api');
        $apiKey     = SB_CORE::getSettings('api_key');
        $domainID   = SB_CORE::getSettings('internal_id');

        $uri = $api.$domainID."/records";
        $req = array(
            "type"      => "A",
            "name"      => "cham-".$rpi_sn,
            "target"    => $vpnAddr,
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

    public static function updateInternalNS($record_id, $vpnAddr){
        $api        = SB_CORE::getSettings('linode_api');
        $apiKey     = SB_CORE::getSettings('api_key');
        $domainID   = SB_CORE::getSettings('internal_id');

        $uri = $api.$domainID."/records/".$record_id;
        $req = array(
            "target"    => $vpnAddr,
            "ttl_sec"   => 300
        );

        $req = json_encode($req);
        $response = SB_CORE::requestURL($uri, $apiKey, $req, "PUT");
        $res_body = json_decode($response['response'], true);
        return $response;
        if($response['info']['http_code'] == 200 && !empty($res_body['id'])){
            return $res_body['id'];
        }

        return false;
    }


 }

 ?>