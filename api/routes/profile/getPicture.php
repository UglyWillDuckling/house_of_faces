<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\Member\Member;

    $strap->setPost('profile/getPicture', 'profile.getPicture', function() use($strap){
       
        $picture_id = Request::getPost('id');

        if($picture_id){

            try{
               
                $db = $strap->db;
                $json = $strap->Json;

                $q = new QueryObject;

                $db->setTable('pictures');

                $q->setRule(['id', '=', $picture_id]);

                $db->where($q,[
                    'description',
                    'path',
                    'content_id'
                ]);

                $picture = $db->prvi();

                if($picture){
                    
                    $picture['path'] = $strap->baseUrl . $picture['path'];
                    $picture['comments'] = $strap->member->getComments($picture);   
                    $picture['liked'] = $strap->member->checkLove($picture);   

                    $json->setValue('picture', $picture);         
                }
                else{
                    $json->setMessage(['error' => 'ova slika ne postoji.']);
                }

                $json->send();
            }
            catch(PDOException $e){

                echo $e->getMessage();
                //$strap->logDbError($e);
            }
        }
        else{
            $strap->logUserError(new Exception('no picture id supplied.'));
        }
    },['member']);