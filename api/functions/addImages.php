<?php

        function addImages($pics, $db, $userId, $postId, $baseUrl){

        $picFolder  = INC_FOLDER . "/public/images/users/";
        $tempFolder = INC_FOLDER . "/public/images/temp/users/" . $userId . "/";

        $paths = array(); //ovdje spremamo nove url-ove

        $good = true;
        for($i=2; $i<sizeof($pics); $i++)
        {

            $temp_name = $tempFolder . $pics[$i];
            $dest      = $picFolder . $pics[$i];
            $path      = "/public/images/users/" . $pics[$i]; 

            $paths[] = $baseUrl . $path; //nadovezujemo bazni url naseg sajta radi ispravnog prikaza u js-u

            if(rename($temp_name, $dest)){
        
        //insert u content tablicu  
                $db->setTable('content');

                $db->add([
                    'type_of_content' => 2,
                    'creator_id' => $userId,                   
                ]);

         //insert u pictures tablicu       
                $db->setTable('pictures');
                $content_id = $db->lastInsertId();

                $db->add([
                    'user_id' => $userId,
                    'path' => $path,
                    'content_id' => $content_id,
                ],[
                    'created' => 'UTC_TIME'
                ]);

        //insert u statusAndPics tablicu  
                $db->setTable('statusAndPics');
                $pictureId = $db->lastInsertId();

                $db->add([
                    'post_id' => $postId,
                    'picture_id' => $pictureId
                ]);

            }
            else{
                return false;
            }
        } 
        //die(preCode($paths));
        return $paths;
    }   