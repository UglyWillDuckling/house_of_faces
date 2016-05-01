<?php

    use Tricky\helpers\Request\Request;
    use Tricky\Member\Member;

    $strap->setGet('messages', 'messages', function() use($strap){


        $member = $strap->member;

        $messages = $member->getMessages();

        $strap->render("messages/messages.twig", [
            'unreadMessages' => $messages['unread'],
            'readMessages' => $messages['read'],
            'numberOfMessages' => sizeof($messages)
        ]);
    }, ['member', 'createCsrf']);