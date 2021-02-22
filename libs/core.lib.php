<?php


class SB_CORE {
    public static function checkIfAuthHeaderExists($header){
        return (!isset($header) && empty($header)) ? false : true;
    }

    public static function getRoutes($route){
        global $response;

        if(file_exists(SB_MODULES."/".$route.".mod.php")){
            include SB_MODULES."/".$route.".mod.php";
        }else{
            $response['status'] = "Route does not exist";
            http_response_code(404);  
        }
    }
}