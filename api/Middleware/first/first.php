<?php

    require "sessionStart.php";
    require "authAdmin.php";
    require "isLoggedIn.php";
    require "storeMember.php";

    //započinjemo session
    $strap->setInit($start_session);

    //provjeravamo da li je korisnik logiran
    $strap->setInit($isLoggedIn);

    //provjeravamo da li je korisnik admin
    $strap->setInit($isAdmin);

    //ako je korisnik logiran spremamo ga u sam strap objekt
    $strap->setInit($storeMember);
    
    