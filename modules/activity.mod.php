<?php

include SB_LIBS."helium.lib.php";

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');

    if(SB_WATCHDOG::isJSON($json)){
        $data = json_decode($json, true);
        $response = SB_HELIUM::getActivity($data['gw_addr']);
        http_response_code(200);
    }else{
        $response['status'] = "Request not in JSON Format";
        http_response_code(400);  
    }
}