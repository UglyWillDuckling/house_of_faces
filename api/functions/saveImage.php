<?php
    require INC_FOLDER . "/api/functions/create_thumb.php";

    function saveImage($randomName, $slika, $id, $db){

        $ext = pathinfo($slika['name'], PATHINFO_EXTENSION);
        $imgName = $randomName . "." . $ext;

        $relative_path = "/public/images/users/" . $imgName;
        $absolute_path = INC_FOLDER . $relative_path;

        
        if(!@move_uploaded_file($slika['tmp_name'], $absolute_path))
        {
            throw new Exception('error with the image upload.');
        }

        $thumb = create_thumb(450, 450, $absolute_path, $ext);

        $thumb_path = INC_FOLDER . "/public/images/thumbs/" . $randomName . "." . $ext;

        imagejpeg($thumb, $thumb_path);//we save the thumb in the appropriate place

        ////////////////////////////////////////////
        //inserting the new image in the database //
        ////////////////////////////////////////////

        $db->setTable('content');// we first save the image in the content table...

        $db->add([
            'type_of_content' => 2,
            'creator_id' => $id
        ]);

        $db->setTable('pictures');// ...and then in the pictures table
        $db->add([
            'user_id'    => $id,
            'path'       => $relative_path,
            'content_id' => $db->lastInsertId()

        ], [
            'created' => 'UTC_TIME'
        ]);

        $picture_id = $db->lastInsertId();

        return [
            'picture_id' => $picture_id,
            'relative_path' => $relative_path,
            'thumb_path' => $thumb_path,
            'image_name' => $imgName
        ];
    }