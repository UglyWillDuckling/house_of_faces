<?php
    use Tricky\helpers\QueryObject\QueryObject;


    function deleteContent($content_id, $db){

        $q = new QueryObject;
        $db->setTable('comments');
        
        $q->setRule(['content_id', '=', $content_id], 'id');

        $commentIds = $db->where(
            $q, 
            ['own_content_id']
        )->getAll();

        $ids = array();
        foreach($commentIds as $c){
            $ids[] = $c['own_content_id'];
        }

        deleteComments($ids, $db, $q);

        $db->setTable('content');
        $q->clearRules();

        $q->setRule(['id', '=', $content_id]);
        $db->delete($q);//we delete the content in the 'content' table
    }


    function deleteComments($contentIds, $db, $q)
    {
        $db->setTable('content');
        $q->clearRules();

        $q->setRule(['id', 'IN', $contentIds]);

        $db->delete($q);//deleting the comments in 'content' table(foreign key 'comments' table)

        deleteReplies($contentIds, $db, $q);
    }


    /**
     * [deleteReplies description]
     * @param  [type] $contentIds [description]
     * @param  [type] $db         [description]
     * @param  [type] $q          [description]
     * @return [type]             [description]
     */
    function deleteReplies($contentIds, $db, $q){
        $q->clearRules();
        $db->setTable('content');



        $db->setTable('replies');
        $q->setRule(['content_id' 'IN', $contentIds]);

        $db->where($q, ['content_id']);

        $replies = $db->getAll();


        $repIds = array();
        foreach($replies as $rep){
            $repIds[] = $rep['own_content_id'];
        }


        $q->setRule('id', 'IN', $repIds);

        $db->delete($q);
    }