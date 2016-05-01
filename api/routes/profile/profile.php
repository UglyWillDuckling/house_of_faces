<?php
    use Tricky\helpers\Request\Request;
    use Tricky\Member\Member;


    $strap->setGet('profile', 'profile', function() use($strap){
       
        $user_id = Request::getGet('id');

        if($user_id){

            $db = $strap->db;

            $user;
            $ownProfile; //check if this is this users profile


         try{
            if($user_id != $strap->member->id){//ako ovo nije korisnikov profil

                $user = new Member($db, $user_id, $strap);
                $ownProfile = false;         
            }
            else{
                $ownProfile = true;
                $user = $strap->member;
            }

            $user->storeUserInfo(); //function for getting all of the users data
            
            $userPics = $user->getMyPictures();
            $posts = $user->getMyPosts();         
            $friends = $user->friends(); //get this users friends
            $commonFriends = array();

            $db->setTable('zanimanje');
            $zanimanja = $db->findAll()->getAll();
            $content = array();
            $friend = false;//varijabla za provjeru prijateljstva izmedu trenutnog korisnika i vlasnika profila

            if(!$ownProfile) //ako ovo nije korisnikov profil radimo provjeru zajednickih prijatelja
            {
                $myFriends = $strap->member->friends();

                $requests_pending = $strap->member->getRequests();

                foreach($friends as &$f){//go trough all of this users friends

                    if( in_array($f, $myFriends) )
                    {
                        $f['commonFriend'] = true;
                        $commonFriends[] = $f;
                    }
                    else{
                        $f['commonFriend'] = false;
                    }

                    foreach($requests_pending as &$req)
                        if($req['user_id'] == $f['user_id']) { $f['pending'] == true; }
                }

                $user->friendshipStatus = $strap->member->checkFriendshipStatus(
                    $user->id
                );
            }
            else{
                require INC_FOLDER . "/api/functions/getContent.php";
                $content = getContent($db);//movies, shows, songs etc.
            }

            $strap->render('profile/profile.twig',[
                'user'     => $user, #info on the user being shown
                'userPics' => $userPics,
                'ownProfile' => $ownProfile, #is this my profile?
                'posts'      => $posts,
                'friends' => $friends,
                'commonFriends' => $commonFriends,
                'content' => $content,
                'friend' => $friend #are we friends?
            ]);
         }
         catch(PDOException $e){
            echo $e->getMessage();
            $strap->logDbError($e);
         }
         catch(PDOException $e){
            echo $e->getMessage();
            $strap->logUserError($e);
         }
        }
        else{

        }
    }, ['member', 'createCsrf']);