<?php
    use Tricky\helpers\QueryObject\QueryObject;

    $isLoggedIn = function() use($strap){

        if( isset($_SESSION['loggedIn']) )
        {
            $strap->loggedIn = true; 
            return true;
        }
        elseif( isset($_COOKIE['nonce']) ) //provjeravamo cookie sa jednokratnim tokenom od zadnjeg logina korisnika
        {
            
           // die(preCode($_COOKIE['nonce']));
            try
            {
                $n = $_COOKIE['nonce'];

                $db = $strap->db;
                $db->setTable('users');

                //die($n);
                $q = new QueryObject;

                $q->setRule(['cookie', '=', $n]);

                $db->where($q);

                if($user = $db->prvi()){

                    require INC_FOLDER . "/api/functions/storeUserSession.php";

                    storeUserSession($user);//spremamo podatke korisnika u session

                    $strap->loggedIn = true;
                    return 1;
                }
                
                $strap->loggedIn = false;     
            }
            catch(PDOException $e){
                $strap->logDbError( $e );
                die($e->getMessage());
            }         
        }
        
        $strap->loggedIn = false;
    };