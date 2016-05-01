<?php

    function getContent(&$db){
        $c = array();

         $c['movies'] = $db
            ->setTable('movies')
            ->findAll()
            ->getAll();

            $c['zanimanja'] = $db
            ->setTable('zanimanje')
            ->findAll()
            ->getAll();

            $c['shows'] = $db
            ->setTable('shows')
            ->findAll()
            ->getAll();

            $c['countries'] = $db
            ->setTable('countries')
            ->findAll()
            ->getAll(); 

            $c['places'] = $db
            ->setTable('places')
            ->findAll()
            ->getAll();

            $c['songs'] = $db
            ->setTable('songs')
            ->findAll()
            ->getAll();

            $c['companies'] = $db
            ->setTable('companies')
            ->findAll()
            ->getAll();

            $c['schools'] = $db
            ->setTable('schools')
            ->findAll()
            ->getAll();

            $c['colleges'] = $db
            ->setTable('colleges')
            ->findAll()
            ->getAll();

            return $c;
    }