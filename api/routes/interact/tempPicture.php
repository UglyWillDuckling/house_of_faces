<?php
    use Tricky\helpers\Request\Request;
    

      $strap->setPost('tempPicture', 'tempPicture', function() use($strap){

            $picture = Request::getFile('image');
            $userId = Request::getSession('user_id');

            $eval = $strap->validate;
            $json = $strap->Json;

            if($picture){

                $temp = $picture['tmp_name'];
                $eval->validate(['picture'=> [$temp, 'image(99)']]);//provjera ispravnosti slike

                //izvrsi provjeru slike te potom i sam upload u temp folder
                if($eval->passes()){
                    //make a new temporary folder for the user

                    $append = "/public/images/temp/users/" . $userId;

                    $dir = INC_FOLDER . $append;
                    @mkdir($dir);

                    $ext = pathinfo($picture['name'], PATHINFO_EXTENSION);

                //napravi nasumicni string za ime slike
                    $sillyName = bin2hex(openssl_random_pseudo_bytes(20)) . "." .$ext;

                    $destination = $dir . "/" .  $sillyName;

                //upload the image
                    if( move_uploaded_file($picture['tmp_name'], $destination) )
                    {   
                        $relPath =  $strap->baseUrl . $append . "/" . $sillyName;   

                        $json->setValue('path', $relPath);
                        $json->send(); //ako je upload uspio posalji natrag korisniku RELATIVNI path do slike
                    }
                    else
                    {
                    $json->setValue('error',  'pogreska prilikom uploada slike.');
                    $json->send();
                    }
                }
                else{

                     $json->setValue( 'error',  $eval->errors()->first('image') );
                     $json->send();
                }
            }
      }, ['member', 'csrf']);  
