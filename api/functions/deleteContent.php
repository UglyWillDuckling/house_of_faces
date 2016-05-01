<?php
    use Tricky\helpers\QueryObject\QueryObject;


    function deleteContent($content_id, &$db){

        $q = new QueryObject;
        deleteComments($content_id, $db, $q);


        $db->setTable('content');
        $q->clearRules();

        $q->setRule(['id', '=', $content_id]);
        $db->delete($q);//we delete the content in the 'content' table
    }

    /**
     * [deleteComments description]
     * @param  [type] $content_id [description]
     * @param  [type] &$db        [description]
     * @param  [type] &$q         [description]
     * @return [type]             [description]
     */
    function deleteComments($content_id, &$db, &$q)// &-pass by refernce
    {      
        $db->setTable('comments');
        $q->clearRules();
        
        $q->setRule(['content_id', '=', $content_id], 'id');

        $commentIds = $db->where(
            $q, 
            ['own_content_id']
        )->getAll();

        $ids = array();
        foreach($commentIds as $c){
            $ids[] = $c['own_content_id'];
        }

        deleteReplies($ids, $db, $q);//deleting replies for all the comments


    //deleting the comments themselves
        $db->setTable('content');
        $q->clearRules();

        $q->setRule(['id', 'IN', $ids]);

        $db->delete($q);//deleting the comments in 'content' table(foreign key 'comments' table)        
    }


    /**
     * [deleteReplies description]
     * @param  [type] $contentIds [description]
     * @param  [type] $db         [description]
     * @param  [type] $q          [description]
     * @return [type]             [description]
     */
    function deleteReplies($contentIds, &$db, &$q){
        $q->clearRules();
        $db->setTable('content');


    //first we find the replise corresponding to the give comments...
        $db->setTable('replies');
        $q->setRule(['content_id' 'IN', $contentIds]);

        $db->where($q, ['content_id']);

        $replies = $db->getAll();


    //...then we get their id-s    
        $repIds = array();
        foreach($replies as $rep){
            $repIds[] = $rep['own_content_id'];
        }


        $q->clearRules();

    //...and finally we delete them
        $q->setRule(['id', 'IN', $repIds]);
        $db->delete($q);
    }