<?php
/**
 * Copyright (C) SyncroB.it - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Feb 2021
 */

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');

    if(SB_WATCHDOG::isJSON($json)){

        $data = json_decode($json, true);
        $record_id = SB_ADVERTISE::getRecord($data['rpi_sn']);
        $ipAddr = str_replace("/24", "", $data['ip_addr']);

        if(!$record_id){
            if(SB_ADVERTISE::insertDbRecord($data['rpi_sn'], $ipAddr)){
                $response['status'] = "success";
                http_response_code(201);
            }else{
                $response['status'] = "failed";
                http_response_code(400);
            }
        }else{
            if(SB_ADVERTISE::updateDbIP($data['rpi_sn'], $ipAddr, $record_id)){
                $response['status'] = "success";
                http_response_code(201);
            }else{
                $response['status'] = "failed";
                http_response_code(400);
            }
        }
    
    }else{
        $response['status'] = "Request not in JSON Format";
        http_response_code(400);
    }
}