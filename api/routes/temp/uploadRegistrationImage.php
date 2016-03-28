<?php
    use Tricky\helpers\Request\Request;


    $strap->setPost('uploadRegistrationImage', 'uploadRegistrationImage', function() use($strap){

        $image = Request::getFile('image');
        $tmp   = $image['tmp_name'];

        $json = $strap->Json;

        if($image){

            $checker = $strap->validate;

            $checker->validate(['image' => [$tmp, 'image(20)'] ]);//max 20mb
            if( $checker->passes() )
            {
                $relPath = "/public/images/temp/registrationImages/";
                $alias = bin2hex(openssl_random_pseudo_bytes(32)) . "." . pathinfo($image['name'], PATHINFO_EXTENSION);

                $relPath = $relPath . $alias;

                $path = INC_FOLDER . $relPath;//path do slike sacinjave include path konstanta i relPath kojeg smo napravili

                if( move_uploaded_file($tmp, $path) )
                {  
                    $url = $strap->baseUrl . $relPath;//stvaramo link od relativne putanje i baznog url-a
                    $json->setValue('src', $url);
                }
                else{ $json->setMessage([ 'error', "upload slike nije uspio."]); }                      
            } 
            else{ $json->setMessage([ 'error', $err->first('image') ]);  }

            $json->send();
        }
    }, ['csrf']);