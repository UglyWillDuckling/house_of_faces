<?php

    function create_thumb($width, $height, $target, $extension){

        list($w, $h) = getImageSize($target);

        $startX = ($w/2) - ($width/2);
        $startY = ($h/2) - ($height/2);
        $img = "";

        if($extension=="jpg") $img=imagecreatefromjpeg($target); 

        elseif($extension=="gif") $img=imagecreatefromgif($target);

        elseif($extension=="png") $img=imagecreatefrompng($target);
          
    
        $thumb = imagecreatetruecolor($width, $height);

        //the cropped and resized image is saved in $thumb
        imagecopyresampled($thumb, $img, 0, 0, $startX, $startY, $w, $h, $w, $h);

        //imagejpeg($thumb, "test.jpeg");

        return $thumb;
    }
