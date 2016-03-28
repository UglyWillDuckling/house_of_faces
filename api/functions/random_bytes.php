<?php

    function random_bytes($num_of_bytes=128)
    {
    return bin2hex(openssl_random_pseudo_bytes($num_of_bytes));
    }