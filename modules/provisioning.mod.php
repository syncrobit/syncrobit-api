<?php

include SB_LIBS."provisioning.lib.php";

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION']) && !SB_PROVISIONING::checkIP($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');
    
    if(SB_WATCHDOG::isJSON($json)){
        $data = json_decode($json, true);
        if(!SB_PROVISIONING::checkIfRpiSN($data['rpi_sn'])){
            if(SB_PROVISIONING::insertUnit($data)){
                $response['status'] = "success";
                http_response_code(201);
            }else{
                $response['status'] = "failed";
                http_response_code(400);
            }
        }else{
            $response['status'] = "Unit already exists";
            http_response_code(200);
        }
    }else{
        $response['status'] = "Request not in JSON Format";
        http_response_code(400);
    }
}

