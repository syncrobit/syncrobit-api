<?php

include "includes/initd.inc.php";

if(!SB_CORE::checkIfAuthHeaderExists($_SERVER['HTTP_AUTHORIZATION'])){
    $response['status'] = "No Auth token found";
    http_response_code(401);
}else{
    if(!isset($_GET['page']) && empty($_GET['page'])){
        $response['status'] = "No route selected";   
    }else{
        SB_CORE::getRoutes($_GET['page']);
    }
}




echo json_encode($response);