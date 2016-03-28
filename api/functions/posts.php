<?php


 function orderPosts($posts){

        usort($posts, function($a, $b){
            if($a['creation'] == $b['creation']) return 0;

            return ($a['creation'] < $b['creation']); 
        });

        $posts = array_slice($posts, 0, 20);

        return $posts;
    };