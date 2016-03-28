<?php
    function saveImage($path, $relativePath, $db, $slika, $userId){


         if(!@move_uploaded_file($slika['tmp_name'], $path))
         {
            throw new Exception('error with the image upload.');
         }


         $db->setTable('content');

         $db->add([
            'type_of_content' => 2,
            'creator_id' => $userId
         ]);


        $db->setTable('pictures');
        $db->add([
            'user_id'    => $userId,
            'path'       => $relativePath,
            'content_id' => $db->lastInsertId()

        ], [
            'created' => 'UTC_TIME'
        ]);


        $db->setTable('users');
        $db->set([         
            'user_image' => $db->lastInsertId()
        ],
        [
            [
                'id', '=', $userId
            ]
        ]);


    }