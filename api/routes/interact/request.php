<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

        $strap->setPost('interact/request', 'interact.request', function() use($strap){
            
            $id = Request::getPost('id');
            $answer = Request::getPost('answer');

            if( $id && ($answer === '1' || $answer === '0') ){

                $db = $strap->db;
                $json = $strap->Json;

                $db->setTable('friend_requests');

                try{

                    $db->startTransaction();
                    if($answer === '1'){

                        $db->set([
                        'accepted' => '1'
                        ],[
                            [
                            'id', '=', $id
                            ]
                        ]);
                    }
                    else{

                        $q = new QueryObject;

                        $q->setRule(['id', '=', $id]);

                        $db->delete($q);
                    }

                    if($db->success) {
                        $db->commit();
                        $json->setValue('ok', 'ok'); //vrijednost koja se vraća javascriptu
                    }
                    else{
                        $db->rollback();
                        $json->setValue('error', 'error while updating the request.');
                        
                        $strap->logUserError(new Exception('error while updating the request table.'));
                    }
 
                    $json->send();
                }
                catch(PDOException $e){
                    $strap->logDbError($e);
                    $json-setMessage(['error' => 'error with the database.']);
                }
            }
        }, ['member', 'csrf']);