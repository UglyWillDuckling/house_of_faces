<?php

    function storeUserSession($user){

        $_SESSION['user_id'] = $user['id'];
        
        $_SESSION['name'] = $user['name'];    
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['last_name'] = $user['last_name'];

        $_SESSION['loggedIn'] = 1;  
        $_SESSION['user_level'] = $user['user_level'];        
    }