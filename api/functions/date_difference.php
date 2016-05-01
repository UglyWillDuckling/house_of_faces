<?php


    function date_difference($before, $after){


        $date = new DateTime($before);
        $date2 = new DateTime($after);

        $diff = $date->diff($date2);

        $date_info = [
            'years' => $diff->y,
             'months' =>  $diff->m,
             'days' =>  $diff->d,
           'hours' =>  $diff->h,
           'minutes' =>  $diff->i,
           'seconds' =>  $diff->s,
        ];

        $difference = "";

        foreach($date_info as $time => $value){

            if($value){

                if($value == 1)
                    $difference = "a " . substr($time, 0, -1) . " ago.";
                else
                    $difference = $value . " " . $time . " ago.";

                break;
            }
        }

        return $difference;
    }

    function contentAge($before){
        $diff = date_difference($before, "now");

        return $diff;
    }