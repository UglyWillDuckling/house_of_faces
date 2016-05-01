<?php 

//Imenici
    use Twig\Twig;
    use RandomLib\Factory as RandomLib;
    use Tricky\Boot\Bootstrap; 
    use Tricky\Validation\Validator; 
    use Tricky\Json\Json; 
    

//require za composerov autoload (uključuje automatsko učitavanje naših klasa preko psr-4 metode)
    require "../vendor/autoload.php";
 //učitavamo vlastite funkcije 
    require "functions/funkyLoad.php"; 
 //učitavamo konfiguracijski array i konstante sajta   
    require "../config/loadConfig.php";

//stvaramo objekt 'Bootstrap' klase koji je odgovoran za rad čitavog sajta
   $strap = new Bootstrap(ROUTE_FOLDER, $config);

//ovisno o grupi pokrecu se odredene radnje prije no sto se neki route 'otvori'
   require "groups/groups.php";
//funkcije koje se obavezno pokrecu prije otvaranja bilo koje stranice
   require "Middleware/first/first.php";
//svi routovi naseg sajta, spremaju se u $strap objekt   
    require "routes/routes.php"; 

   $strap->baseUrl = BASE_URL;
   $strap->error_log = ERROR_LOG;
   $strap->db_error_log = DB_ERROR_LOG;

   $strap->defaultUserImage = $strap->baseUrl . DEFAULT_USER_IMAGE;
   $strap->javascript = $strap->baseUrl . "/api/javascript/";
   
/**
     * inicijalizacija twig objekta i postavljanje istoga kao 'view' u $strap objektu 
     * U aplikaciji se koristi za  renderiranje sadržaja
*/
    $strap->setView( VIEW_FOLDER, function($view_folder) {
        
        $loader   = new Twig_Loader_Filesystem($view_folder);   
        $twig     = new Twig_Environment($loader);
        
        $diffFilter = new Twig_SimpleFilter('time_diff', "date_difference");
        $twig->addFilter($diffFilter);


        return $twig;
    }); 
    // postavljanje objekta 'Twig' klase za view
    require "Middleware/setView/setView.php";

//validator klasa extends violin
   $strap->validate = new Validator($strap);

//random generator stringova  
    $factory     = new RandomLib;
    $strap->random = $factory->getMediumStrengthGenerator();

    $strap->Json = new Json($strap, true);
