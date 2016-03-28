<?php
    use Tricky\helpers\Request\Request;
    use Tricky\Member\Member;

    $strap->setGet('profile', 'profile', function() use($strap){
       
        $user_id = Request::getGet('id');

        if($user_id){

            $db = $strap->db;

            $user;
            $ownProfile;

          try{
            if($user_id != $strap->member->id){
                $user = new Member($db, $user_id, $strap);
                $ownProfile = false;         
            }
            else{
                $ownProfile = true;
                $user = $strap->member;
            }

            $userPics = $user->getMyPictures();
            $posts = $user->getMyPosts();

            $strap->render('profile/profile.twig',[

                'user' => $user,
                'userPics' => $userPics,
                'ownProfile' => $ownProfile,
                'posts' => $posts
            ]);
         }
         catch(PDOException $e){
            echo $e->getMessage();
            $strap->logDbError($e);
         }


        }
         

    }, ['member', 'createCsrf']);