<?php
include SB_LIBS."provision_test.lib.php";

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');

    if(SB_WATCHDOG::isJSON($json)){
        $data = json_decode($json, true);

        if(!SB_PROVISION_TEST::checkIfUnitExists($data['rpi_sn'])){
            if(SB_PROVISION_TEST::insertTestResults($data)){
                $response['status'] = "success";
                http_response_code(201);
            }else{
                $response['status'] = "failed";
                http_response_code(400);
            }
        }else{
            if(SB_PROVISION_TEST::updateTests($data)){
                $response['status'] = "success";
                http_response_code(200);
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