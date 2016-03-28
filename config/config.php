<?php
    

//array sa podacima o konfiguraciji sajta(sprema se u Bootstrap)
    $config = array(
    
        "database" => [
            "user"     => "root",
            "password" => "",
            "db"       => "book_of_faces",
            "host"     => "127.0.0.1",
            "type"     => "mysql"
        ],
        'password' => [     
            'algo' => PASSWORD_BCRYPT //algoritam za hash passworda      
        ],
        'image' => [
            'size'   => 50000,                 //5MB
            'format' => ['jpg', 'png', 'gif'] //ogranicenja pri uploadu slika
        ]  ,
        "cookie" => [
            'life' => (time() + 60*60*48),         
        ]
    );          
