<?php
    
    $strap->setGet('test', 'test', function(){


        require INC_FOLDER . "/api/functions/create_thumb.php";

        $path = INC_FOLDER . "/public/images/test/landscape.jpg";
        
        $palac = create_thumb(150, 150, $path);
    });


