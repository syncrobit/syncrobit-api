<?php
include SB_LIBS."provisioning.lib.php";

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');

    if(SB_WATCHDOG::isJSON($json)){
        $data = json_decode($json, true);

        if(SB_PROVISIONING::checkIfRpiSN($data['rpi_sn']) && SB_WATCHDOG::checkIfUnitIsActive($data['rpi_sn'])
           && SB_WATCHDOG::checkMinerAddress($data['miner_address'])){
            $response['status'] = "success";
            http_response_code(202);
        }else{
            $response['status'] = "Not found";
            http_response_code(404);
        }
        
    }else{
        $response['status'] = "Request not in JSON Format";
        http_response_code(400);  
    }
}