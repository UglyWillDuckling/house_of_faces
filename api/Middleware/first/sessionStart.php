<?php
    use Tricky\helpers\Request\Request;

    $start_session = function() use($strap){
        
        session_start();
        //session_cache_limiter(false);
        $strap->Json->storeCsrfToken();
    };