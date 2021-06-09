<?php

include SB_LIBS."helium.lib.php";

if(!SB_CORE::unitCheckAuth($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "Authorization failed";
    http_response_code(401);
}else{
    $json = file_get_contents('php://input');

    if(SB_WATCHDOG::isJSON($json)){
        $data = json_decode($json, true);

        $response['blockchain_height']  = SB_HELIUM::getBlockChainHeight();
        $response['rewards_1d']         = SB_HELIUM::get1dRewards($data['gw_addr']);
        $response['rewards_7d']         = SB_HELIUM::get7dRewards($data['gw_addr']);
        $response['rewards_30d']        = SB_HELIUM::get30dRewards($data['gw_addr']);
        $response['rewards_365d']       = SB_HELIUM::get365dRewards($data['gw_addr']);
        $response['last_week']          = SB_HELIUM::getLatWeekRewards($data['gw_addr']);
        $response['oracle_price']       = SB_HELIUM::getOraclePrice();

        http_response_code(200);
    }else{
        $response['status'] = "Request not in JSON Format";
        http_response_code(400);  
    }
}