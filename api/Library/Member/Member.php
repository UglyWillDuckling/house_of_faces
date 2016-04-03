<?php namespace Tricky\Member;
    
    use PDO;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\helpers\Request\Request;

    class Member 
    {

        private $db, $strap;

        public function __construct($db, $id, $strap){

            $this->db = $db;
            $this->strap = $strap;
            $this->defaultImage = $strap->defaultUserImage;

            try{

                $db->setTable('users');

                $q = new QueryObject;

                $q->setRule(['users.id', '=', $id], 'id');

                $data = $db->where($q,[
                    'id',
                    'name',
                    'nickname',
                    'last_name',
                    'place_id',
                    'country_id',
                    'favorite_movie',
                    'favorite_song',
                    'about',
                    'user_level',
                    'favorite_color',
                    'hobbies',
                    'user_image'
                ])->prvi();
                if($data){
                    foreach($data as $row => $value){
                        $this->{$row} = $value; //za svaki stupac stvaramo novo svojstvo u 'Member' objektu
                    }

                //getting the users image
                    if($image_id = $data['user_image']){

                        $q->setRule(['id', '=', $image_id], 'id');//we reset the 'id' rule

                        $db->setTable('pictures');

                        $db->where($q, [
                            'path as userImage'
                        ]);

                        $this->user_image = $db->prvi()['userImage'];
                    }                    
                }
                else{
                    throw new Exception('the user with id ' . $id . ', does not exist.');//ako korisnik sa danim id-ijem ne postoji bacamo gresku
                }   
                $this->query = $q;
            }
            catch(PDOException $e){
                $strap->logDBError($e->getMessage());
                die;
            }
            catch(Exception $e){
                $strap->logUserError($e->getMessage());
                die;
            }
        }

        public function friends(){

            $db = $this->db;
            $db->setTable('friend_requests');

        //dohvacamo sve
            $friends1 = $db->where([
                'AND' =>
                [
                    [
                    'field' => 'user_id',
                    'rule' => '=',
                    'value' => $this->id
                    ],
                    [
                    'field' => 'accepted',
                    'rule' => '=',
                    'value' => 1
                    ]
                ]
            ])->getAll();

            $friends2 =  $db->where([
                'AND' =>
                [
                    [
                    'field' => 'friend_id',
                    'rule' => '=',
                    'value' => $this->friend_id
                    ],
                    [
                    'field' => 'accepted',
                    'rule' => '=',
                    'value' => 1
                    ]
                ]
            ])->getAll();

            $db->setTable('users');
        }

        public function getMyPosts(){

            $db = $this->db;
            $db->setTable('posts');

            $query = new QueryObject;

            $query->setRule(['owner_id', '=', $this->id]);


            $query->setCondition(['order by', ' creation desc']);
            $query->setCondition(['limit', ' 15']);

            $query->setJoin(['inner', 'users', 'posts.owner_id', 'users.id']);
            $query->setJoin(['left', 'pictures', 'users.user_image', 'pictures.id']);


            $myPosts = $db->whereJoin($query, [
              'posts.id as postId',
              'posts.content_id as ownId', 
              'users.name',
              'users.id as userId',
              'users.last_name',
              'posts.tekst',
              'pictures.path as userPic',
              'posts.creation as creation',                   
            ])->getAll();

            foreach($myPosts as &$post)
            {
               $post['comments']  = $this->getComments($post);
               $post['liked']     = $this->checkLove($post);
               $post['pics']      = $this->_getPostPics($post);
            }

            return $myPosts;
        }

        public function recentPosts(){

            $user_id = $this->id;

            $db = $this->db;
            $db->setTable('friend_requests');

        
            $query = new QueryObject;

         //setting the rules for the where clause   
            $query->setRule(['friend_requests.user_id', '=' , $this->id, 'AND'], 'user_id');
            $query->setRule(['accepted', '=', '1']);

        //setting the ordering and the limit of posts returned
            $query->setCondition(['order by', 'creation desc']);
            $query->setCondition(['limit', '25']);
 
            $query->setJoin(['inner', 'users', 'users.id', 'friend_requests.friend_id'], 'joinId');
            $query->setJoin(['inner','posts', 'users.id', 'posts.owner_id']);
            $query->setJoin(['left','pictures', 'users.user_image', 'pictures.id']);

            
            $friendPosts1 = $db->whereJoin(
            $query,
            ['posts.id as postId',
             'posts.content_id as ownId', 
              'users.name',
              'users.id as userId',
              'users.last_name',
              'posts.tekst',
              'pictures.path as userPic',
              'posts.creation as creation',             
            ]
            )->getAll();

            //preCode($friendPosts1);die;
            

        //changing the join under the key 'joinId'
            $query->setJoin(['inner', 'users', 'users.id', 'friend_requests.user_id'], 'joinId');
            $query->setRule(['friend_id', '=' , $this->id, 'AND'], 'user_id');

            $friendPosts2 = $db->whereJoin(
             $query, 
             ['posts.id as postId, 
              posts.content_id as ownId,
              users.name, 
              users.id as userId, 
              users.last_name, 
              posts.tekst, 
              pictures.path as userPic, 
              posts.creation as creation']
            )->getAll();

            $friendPosts = array_merge($friendPosts1, $friendPosts2);

           
        //get the comments for each post
            foreach($friendPosts as &$post)
            {
               $post['comments']  = $this->getComments($post);
               $post['liked']     = $this->checkLove($post);
               $post['pics']      = $this->_getPostPics($post);
            }


            $myPosts = $this->getMyPosts();

            $posts = array_merge($friendPosts, $myPosts);

            /////////////////////////////////////////////////////////////
                //sort the posts by date, leave only the first 10  //
            /////////////////////////////////////////////////////////////
            $posts=orderPosts($posts);

            return $posts;
        }

        private function _getPostPics($post){

            $db = $this->db;
            $db->setTable('statusandpics');

            $q = new QueryObject;

        
            $q->setRule([ 
                'post_id', '=', $post['postId'] 
            ]);

            $q->setJoin(['inner', 'pictures', 'pictures.id', 'statusandpics.picture_id']);//dodaj mogucnost bez arraya

            $pics = $db->whereJoin($q, [
                'pictures.path',

            ])
            ->getAll();

            return $pics;
        }//_getPostPics()


        public function checkLove($content){

            $db = $this->db;
            $q = new QueryObject;

            $db->setTable('likes');

            $q->setRule(['user_id', '=', $this->strap->member->id, 'AND']);

            $ownContentId = isset($content['ownId']) ? $content['ownId'] : $content['content_id']; 

            $q->setRule(['content_id', '=',  $ownContentId]);

            $liked = $db->where($q)->prvi();

            if($liked)
                return true;

            return false;
        }

        public function getComments(&$content)
        {
            $db = $this->db;          
            $postQuery = new QueryObject;

            $db->setTable('comments');
          
            $ownContentId = isset($content['ownId']) ? $content['ownId'] : $content['content_id']; 


            $postQuery->setRule(['comments.content_id', '=',  $ownContentId], 'contentRule');

            $postQuery->setJoin(['inner', 'content', 'content.id', 'comments.own_content_id'], 'contentJoin');
            $postQuery->setJoin(['inner', 'users', 'users.id', 'comments.user_id'], 'userJoin');
            $postQuery->setJoin(['left', 'pictures', 'users.user_image', 'pictures.id'], 'imageJoin');
            
            $comments = $db->whereJoin($postQuery, [
                'users.name as username',
                'users.id as userId',

                'comments.id',      
                'comments.tekst',  
                'comments.own_content_id as ownId',            
                'content.number_of_likes',
                 
                'pictures.path as userImage',
                'content.number_of_likes as likes'
            ])->getAll();
            
          //get the replies  
            foreach($comments as &$comment)
            {
                $db->setTable('replies');

                $id = $comment['ownId'];
                $postQuery->setRule(['replies.content_id', '=', $id], 'contentRule');

                $postQuery->setJoin(['inner', 'content', 'replies.own_content_id','content.id'], 'contentJoin');
                $postQuery->setJoin(['inner', 'users', 'users.id', 'replies.user_id'], 'userJoin');
                $postQuery->setJoin(['left', 'pictures', 'users.user_image', 'pictures.id'], 'imageJoin');
               
                $replies = $db->whereJoin($postQuery, [
                    'replies.id',
                    'replies.own_content_id as ownId',
                    'replies.tekst',
                    'replies.content_id',

                    'content.number_of_likes as likes',

                    'users.name as username',
                    'users.id as userId',
                    'pictures.path as userImage',            
                ])->getAll();

            //potrebna provjera za svaki reply zbog likea od strane korisnika
                foreach($replies as &$reply)
                {
                    $reply['liked'] = $this->checkLove($reply);

                    if(!$reply['userImage'])
                    {
                        $reply['userImage'] = $this->strap->defaultUserImage;
                    }
                }

                //die(preCode($replies));

                $comment['replies'] = $replies;
                $comment['liked'] = $this->checkLove($comment);

            //ako slika korisnika nije definirana na njeno mjesto stavljamo defaultnu sliku    
                $comment['userImage'] = $comment['userImage'] ?: $this->defaultImage;    
            }

            return $comments ?: array();
        }//getComments()

        /**
         * dohvaca id-jeve prijatelja ovog korisnika
         * @return [array] [description]
         */
        private function get_friend_ids(){

            $db = $this->db;
            //ovdje spremamo sve vrijednosti id polja prijatelja korisnika
            $users = array();

            $users1 = $db->friendQuery('SELECT user_id FROM friend_requests WHERE friend_id=1 AND accepted=1');
            $users1 = $users1->fetchAll(PDO::FETCH_ASSOC);

            foreach($users1 as $user)
            {
                $users[] = $user['user_id'];
            }

            $users2  = array();
            $users2 = $db->friendQuery('SELECT friend_id FROM friend_requests WHERE user_id=1 AND accepted=1');

            if($users2)
            $users2 = $users2->fetchAll(PDO::FETCH_ASSOC);


            foreach($users2 as $user)
            {
                $users[] = $user['friend_id'];
            }
            
            return $users;
        }

        public function getMessages(){

            $db = $this->db;

        }

        /**
         * dobavljanje neodgovorenih zahtjeva za prijateljstvo
         * @return [array] 
         */
        public function getRequests(){

            $db=$this->db;
            $db->setTable('friend_requests');

            $q = new QueryObject;

            $q->setRule(['friend_id', '=', $this->id, 'AND']);
            $q->setRule(['accepted', '=', '0']);

            $q->setJoin(['inner', 'users', 'friend_requests.user_id', 'users.id']);
            $q->setJoin(['left', 'pictures', 'users.user_image', 'pictures.id']);

            $reqs = $db->whereJoin($q,[
                'users.name as username',
                'users.nickname',
                'pictures.path as userImage',
                'friend_requests.id as request_id',
            ])
            ->getAll();

            $this->requests = $reqs;

            return $reqs;
        }

        public function hasRequests(){
            if($this->requests)
                return true;

            return false;
        }

        public function giveRequests(){
            if($this->requests) return $this->requests;

            return false;
        }

        /**
         * broj nepročitanih poruka za korisnika
         * @return [type] [description]
         */
        public function getNumberOfMessages(){

            $db = $this->db;

            $db->setTable('messages');

            $q = new QueryObject();

            $q->setRule(['reader_id', '=', $this->id, 'AND']);
            $q->setRule(['status', '=', '0']);
            
            $db->where($q, [
                'COUNT("reader_id")',
            ]);

            return $db->prvi()['COUNT("reader_id")'];
        }

        public function getNumberOfRequests(){

            $db = $this->db;

            $db->setTable('friend_requests');

            $q = new QueryObject();

            $q->setRule(['friend_id', '=', $this->id, 'AND']);
            $q->setRule(['accepted', '=', '0']);
            
            $db->where($q, [
                'COUNT("reader_id")',
            ]);

            return $db->prvi()['COUNT("reader_id")'];
        }//\getNumberOfRequests()

        public function getMyPictures(){

            $db = $this->db;

            $q = new QueryObject;

            $db->setTable('pictures');

            $q->setRule(['user_id', '=', $this->id]);

            $q->setJoin(['inner', 'content', 'pictures.content_id', 'content.id']);

            $db->whereJoin($q, [
                'pictures.path as path',
                'pictures.id as picture_id',
                'pictures.content_id as content_id',
                'description',
            ]);

            $slike = $db->getAll();

            foreach($slike as &$slika)
            {
                if($slika){
                    $slika['comments'] = $this->getComments($slika);
                    $slika['love']  = $this->checkLove($slika);                              
                }
            }

            return $slike;
        } 

        function saveProfileImage($slika){
            $doodlydoo = random_bytes(32);

            $d= saveImage($doodlydoo, $slika, $this->id, $this->db);

            $db->setTable('users');

            $db->set([
                'user_image' => $d['picture_id']
            ],[      
                [
                'id', '=', $this->id  
                ],            
            ]);
        }
    }//\Member class
