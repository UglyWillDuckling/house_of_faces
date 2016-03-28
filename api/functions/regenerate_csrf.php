<?php

    function regenerateCsrf()
    {
        $bytes = random_bytes(128);  
        $_SESSION['csrfToken'] = $bytes;

        return $bytes;
    }