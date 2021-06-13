<?php 

class SB_HELIUM{
    public static function getBlockChainHeight($format = 1){
        global $pg_db;

        try{
            $sql = "SELECT max(height) FROM blocks";
            $statement = $pg_db->prepare($sql);
            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);
            
            return ($format == 0) ? $row['max'] : number_format($row['max']);

           }catch (PDOException $e){
            error_log($e->getMessage());
           }
    }

    public static function get1dRewards($gateway){
        global $pg_db;

        try{
            $sql = "SELECT sum(amount) FROM rewards WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) >= DATE((now() - '1 day'::interval))";

            $statement = $pg_db->prepare($sql);
            $statement->execute();
            
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return SB_CORE::moneyFormat($row['sum'], 2); 

           }catch (PDOException $e){
            error_log($e->getMessage());
           }
    }

    public static function get7dRewards($gateway, $format = 1){
        global $pg_db;

        try{
            $sql = "SELECT sum(amount) FROM rewards WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) BETWEEN DATE((now() - '7 day'::interval)) AND DATE((now() - '1 day'::interval))";

            $statement = $pg_db->prepare($sql);
            $statement->execute();
        
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return SB_CORE::moneyFormat($row['sum'], 2);

           }catch (PDOException $e){
            error_log($e->getMessage());
           }
    }

    public static function getLatWeekRewards($gateway, $format = 0){
        global $pg_db;

        try{
            $sql = "SELECT sum(amount) FROM rewards WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) BETWEEN DATE((now() - '14 day'::interval)) AND DATE((now() - '7 day'::interval))";

            $statement = $pg_db->prepare($sql);
            $statement->execute();
        
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return SB_CORE::moneyFormat($row['sum'], 2, $format);

           }catch (PDOException $e){
            error_log($e->getMessage());
           }

    }

    public static function get30dRewards($gateway, $format = 0){
        global $pg_db;

        try{
            $sql = "SELECT sum(amount) FROM rewards WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) BETWEEN DATE((now() - '30 day'::interval)) AND DATE((now() - '1 day'::interval))";

            $statement = $pg_db->prepare($sql);
            $statement->execute();
                  
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return SB_CORE::moneyFormat($row['sum'], 2, $format);
    
        }catch (PDOException $e){
            error_log($e->getMessage());
        }
    }

    public static function get365dRewards($gateway, $format = 0){
        global $pg_db;

        try{
            $sql = "SELECT sum(amount) FROM rewards WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) BETWEEN DATE((now() - '365 day'::interval)) AND DATE((now() - '1 day'::interval))";

            $statement = $pg_db->prepare($sql);
            $statement->execute();
                  
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return SB_CORE::moneyFormat($row['sum'], 2, $format);
    
        }catch (PDOException $e){
            error_log($e->getMessage());
        }
    }

    public static function getOraclePrice(){
        global $pg_db;

        try{
            
            $sql = "SELECT price FROM oracle_prices p INNER JOIN blocks b ON p.block = b.height 
                    ORDER BY p.block DESC LIMIT 2";
            $statement = $pg_db->prepare($sql);
            $statement->execute();

            $row = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            return SB_CORE::moneyFormat($row[0]['price'], 2);
    
        }catch (PDOException $e){
            error_log($e->getMessage());
        }
    }

    public static function getActivity($gateway, $format = 0){
        global $pg_db;

        try{
            $sql = "SELECT r.block, r.time, r.amount, t.type FROM rewards r INNER JOIN transactions t ON r.transaction_hash = t.hash WHERE gateway = '".$gateway."' 
                    AND DATE(to_timestamp(time)) >= DATE((now() - '1 day'::interval)) LIMIT 10";
            $statement = $pg_db->prepare($sql);
            $statement->execute();
                  
            $row = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $row;
    
        }catch (PDOException $e){
            error_log($e->getMessage());
        }

    }
}