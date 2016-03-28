<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


        $strap->setPost('comment', 'comment', function() use($strap){

            $db = $strap->db;
            $json = $strap->Json;

            $type_of_content = 3;
            
            $tekst = trim(Request::getPost('content'));
            $content_id = Request::getPost('content_id');
            $type = Request::getPost('type');


            $table = ($type=='comment') ? 'comments' : 'replies';

            if($tekst && $content_id)
            {
                $user_id = Request::getSession('user_id');
                //dodaj komentar u bazu
                try{
                    $db->startTransaction();

                    $db->setTable('content');

                    $success = $db->add([
                    'type_of_content' => $type_of_content,
                    'creator_id' => $user_id
                    ]);

                  //u slučaju da je insert u content tablicu uspio,izvrsavamo insert u 'comments' tablicu
                    $ownId = $db->lastInsertId();

                    $db->setTable($table);

                    $commentAdded = $db->add([
                        'content_id' => $content_id, //id posta
                        'own_content_id' => $ownId, //id samog komentara
                        'user_id' => $user_id,
                        'tekst'   => $tekst,
                    ], [
                        'creation' => 'UTC_TIME' //stupci koji ne trebaju bind
                    ]);
               
                    //get user info                      
                        $db->setTable('users');

                        $q = new QueryObject;

                        $q->setRule(['users.id', "=", $user_id]);
                        $q->setJoin(['left', 'pictures', 'users.user_image', 'pictures.id']);

                        $user = $db->whereJoin($q,[
                            'users.nickname', 'name', 'pictures.path as userImage'
                        ])->prvi();
                        $user['comment_id'] = $ownId;//this is the content_id of the comment

                    //za slučaj da korisnik nema sliku profila    
                        if(!$user['userImage'])
                            $user['userImage'] = $strap->baseUrl . "/public/images/default/defaultUser.png"; 
                        
                        $db->commit();
                        $json->setMessage($user);
                } 
                catch(PDOException $e){
                    $db->rollback();

                    //logiraj gresku
                    $strap->logDbError($e);
                    $json->setMessage(['error' => 'greska s bazom podataka.']);
                }  
                finally{// bez obzira na gresku i dalje vracamo json objekt
                    $json->send();
                }
            }
            else {
                $strap->logUserError(new Exception('no text or content_id was submitted in the comment.'));
            }
        }, ['member', 'csrf']);