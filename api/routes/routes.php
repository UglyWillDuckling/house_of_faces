<?php
    
    /**
     * Ovdje učitavamo sve postojece routove 
     */

    require "home/home.php";
    require "comment/addComment.php";
    require "search/search.php";
    
    require "interact/like.php";
    require "interact/statusUpdate.php";
    require "interact/find.php";

    require "interact/tempPicture.php";
    require "interact/removeTmpPicture.php";
    require "interact/request.php";

    require "profile/profile.php";
    require "profile/getPicture.php";
    require "profile/addImage.php";
    require "profile/deleteImage.php";

    require "temp/uploadRegistrationImage.php";

    require "auth/register.php";
    require "auth/checkUsername.php";
    require "auth/checkEmail.php";
    require "auth/activateAccount.php";
    require "auth/activation.php";
    require "auth/login.php";
    require "auth/logout.php";

    require("test/testThumb.php");

    