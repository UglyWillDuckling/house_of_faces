<?php

    function preCode($var)
    {       
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    function arrayDie($arr){

        print_r($arr); die;
    }

    function is_dir_empty($dir) {
    foreach (new DirectoryIterator($dir) as $fileInfo) {
        if($fileInfo->isDot()) continue;
        return false;
    }
    return true;
}