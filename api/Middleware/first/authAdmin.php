<?php
    

    $isAdmin = function() use($strap){

     //ako je postavljen loggedIn kao true i 'user_level' varijabla je 2 admin var dobivamo vrij. true    
        if($strap->loggedIn && $_SESSION['user_level'] === 2)
        {
             $strap->admin = true;            
        }
        else {
            $strap = false;
        }
    };
