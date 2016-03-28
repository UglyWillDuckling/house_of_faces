<?php
  use Tricky\Member\Member;
  use Tricky\helpers\Request\Request;
  

        $strap->setGet('home', 'home', function() use($strap){

        $db = $strap->db;

        //get messages for the user if he is logged in, show recent posts from them and their friends(around 15)
        //show friends that are currently online(javascript chat client),
        
        if( $strap->isLoggedIn() )
        {         
          try{
            $user = $strap->member;

            $msgNumber = $user->getNumberOfMessages();

            $posts = $user->recentPosts();
       
            $strap->render('home/home_member.twig', [
                'posts' => $posts,
                'user' => $user,
                'numberOfMessages' => $msgNumber,
            ]);        
           }
           catch(PDOException $e){
            echo "db error: " . $e->getMessage();
            $strap->logDBError( $e );
            die;
           }
           catch (Exception $e){      
             $strap->logUserError( $e );
             die;
           } 
        }
        else
        { 
          $strap->render('home/home_register.twig'); 
        }
    }, ['statPics', 'createCsrf']);

