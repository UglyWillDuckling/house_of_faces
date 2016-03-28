<?php
    use Twig\Twig;
    
    $twig = $strap->view;
        
         $twig->addFilter(new Twig_SimpleFilter('rtrim', function($str, $visak){
        
            return rtrim($str, $visak);
    }));
