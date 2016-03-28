<?php

    require "groupFunk.php";

    /****** stvaramo grupe routove zajedno sa pripadajucim funkcijama *****/

    //csrf routovi pri 'pokretanju' vrše provjeru postojanja i valjanosti csrf tokena u $_POST-u
    $strap->setGroup( 'csrf', array($checkCsrf) );

    //routovi u admin grupi odmah kreiraju random token i spremaju ga u $_SESSION['token']
    $strap->setGroup( 'createCsrf', array($createCsrf) );

    //admin grupa automatski provjerava je li korisnik admin
    $strap->setGroup( 'admin', array($adminCheck) );

    //provjeravamo da li je korisnik logiran
    $strap->setGroup( 'member', array($isMember) );

    //brisanje slika iz temp foldera
    $strap->setGroup( 'statPics', array($deleteStatPics) );







