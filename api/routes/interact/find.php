<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

        $strap->setPost('find', 'find', function() use($strap){

            $word = Request::getPost('word');

            if($word && strlen($word) > 2){
                try{
                    $db = $strap->db;
                    $json = $strap->Json;

                //set the table
                    $db->setTable('users');

                    $q = new QueryObject;

                    $word = '%' . $word . '%';
                    $q->setRule(['name', 'LIKE', $word, 'OR'], 'name');
                    $q->setRule(['nickname', 'LIKE', $word], 'nick');

                    $q->setJoin([// join on the pictures table
                        'left', 
                        'pictures', 
                        'pictures.id', 
                        'users.user_image'
                    ]);

                    $users = $db->whereJoin($q, [
                        'users.name',
                        'users.nickname',
                        'users.id as user_id',
                        'pictures.path as userImage',

                    ])->getAll();

                    $json->setValue('users', $users);
                }
                catch(PDOException $e){
                    echo $e->getMessage();
                    $json->setMessage(['error' => 'error with the database.']);
                }
                finally{
                    $json->send();
                }     
            }
            else{
                $strap->logUserError(new Exception('search without a proper word.'));
            }
        }, ['member', 'csrf']);