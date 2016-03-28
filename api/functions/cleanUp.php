<?php

function clean($dirt)
    {   
        return htmlentities($dirt, ENT_COMPAT, "UTF-8");
    }