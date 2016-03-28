<?php
     use Tricky\Member\Member;
     use Tricky\helpers\Request\Request;

    $storeMember = function() use($strap){

        if($strap->loggedIn){
            $strap->member = new Member($strap->db, Request::getSession('user_id'), $strap);


            if( $requests = $strap->member->getRequests() )
            {      
                $strap->storeArg('requests', $requests);
                $strap->storeArg('numberOfRequests', count($requests));
            }
        }
        else{
            $strap->member = NULL;
        }
    };