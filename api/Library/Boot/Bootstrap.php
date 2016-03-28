<?php namespace Tricky\Boot;

    use Tricky\Database\DB;
    use Tricky\helpers\Request\Request; 
     

    class Bootstrap{

        protected                 
                  $getRoutes       = array(),
                  $postRoutes      = array(),                 
                  $routes_folder   = null,
                  $flashMsg        = null,
                  $start_callbacks = array(), 
                  $args            = array(),                            
                  $groups          = array();
                  
        public  
                $config = array(),
                $db_error_log,
                $error_log,           
               $view     = null,
               $prevPage = '';   
                
        /**
         * 
         * u konstruktoru klase određujemo folder sa routovima te 'Bootstrap' objektu dajemo
         * odgovarajuću konfiguracijsku datoteku
         * 
         * @param [string] $folder path prema folderu koji sadrži sve routove
         * @param [array] $config array koji sadrži podatke o konfiguraciji sajta
         */
        public function __construct($folder, $config){
            
            $this->routes_folder = $folder;         
            $this->config        = $config;
            
            $dbConfig = $this->config['database'];
            
            $db_name = $dbConfig['db'];
            $db_type = $dbConfig['type'];
            $user    = $dbConfig['user'];
            $pass    = $dbConfig['password'];
            $host    = $dbConfig['host'];
                    

            $this->db = new DB($db_type, $db_name, $host, $user, $pass);
        }

        /**
         * funkcija koja pokreće odgovarajući route prema zadanim parametrima u url-u
         * @return [bool] vraca true ako uspije, u suprotnom renderira stranicu sa greškom
         */
        public function run()
        {                  
            $route = $this->_parseRoute();              
            if($route)
            {
               
               $this->_runStartCalls();

            // postavljanje svojstva metode(tip 'http' zahtjeva), koje se koristi u _callRoute();       
                if($_SERVER['REQUEST_METHOD'] === 'POST')       
                    $this->method = "post"; 
                else            
                    $this->method = "get";              
               
                if($this->_callRoute($route))
                {    

                //u slučaju da je url ispravan završavamo rad funkcije
                    return true;
                }
            }
        
            // ako je url neispravan prikazujemo stranicu sa greskom(404 error) 
            $this->render("errors/404.php", [
                'error' => 'ta stranica ne postoji'
            ]);
        }//run()
        
        /**
         * privatna metoda za parsiranje url-a
         * @return [string] vraća očišćeni url
         */
        private function _parseRoute()
        {
            $url = isset($_GET['url']) ? $_GET['url'] : null;

            if($url)
            {            
                $url = rtrim($url, "/");
                $url = filter_var($url, FILTER_SANITIZE_URL);
            }
             
            return $url;
        }   
    
        /**
         * metoda za pozivanje odgovarajućeg routa, također sprema url posljednje 
         *  posjećene stranice iz sessiona i sprema trenutno pozvani url u session
         *  
         * @param  [string] $route_path, parsiranji url
         * @return [bool]  vraća true ili false
         */
        private function _callRoute($route_path){

        //spremanje adrese zadnje stranice u 'bootstrap'
            $this->prevPage = Request::getSession('currentPage');
        
            $_SESSION['currentPage'] = $route_path;

            $route_data = explode('?', $route_path);    

            if( $this->_call_user_function($route_data) )  
            return true; 
            
            return false;       
        }// \_callRoute
         

        /**
         * privatna metoda za spremanje routa u odgovarajuci array,
         * poziva se iz seGet() i setPost() metoda
         * 
         * @param [string] $method   metoda za pozivanje routa(GET, POST)
         * @param [string] $path    url routa
         * @param [string] $name     ime routa
         * @param [function] $callback funkcija za route
         */
        private function _setRoute($method, $path, $name, $callback, $groups = array()){
            
        //$safePlace varijabla odreduje kojem routes polju se pristupa, post ili get
            $safePlace = $method . "Routes"; 

            $this->{$safePlace}[$path] = array('name' => $name,'callback' => $callback, 'groups' => $groups);
        }    
        
        /**
         * metoda za postavljanje get routova
         * 
         * @param [string] $path  url routa 
         * @param [string] $name  ime routa, koristi se pri trazenju njegovog url( urlZa() )
         * @param [function]      $callback funkcija koja se pokrece pri pozivanje nekog routa
         */
        public function setGet($path, $name, $callback, $groups = array())
        {                       
            $method = "get";
            $this->_setRoute($method, $path, $name, $callback, $groups); 
        }

        /**
         *  metoda za postavljanje post routova
         *  
         * @param [type] $path     url routa 
         * @param [type] $name     ime routa, koristi se pri trazenju njegovog url( urlZa() )
         * @param [type] $callback $callback funkcija koja se pokrece pri pozivanje nekog routa
         */
        public function setPost($path, $name, $callback, $groups = array())
        {
            $method = "post";
            $this->_setRoute($method, $path, $name, $callback, $groups); 
        }

        /**
         * postavljanje nove grupe u Bootstrap
         * @param [string] $name  
         * @param [array] $callbacks array funkcija
         */
        public function setGroup($name, $callbacks){

            $this->groups[$name] = $callbacks; 
        }
                    
        /**
         * prikazuje traženu stranicu(view) preko twiga, također prosljeđuje 
         *  parametre twigu ako postoje
         * 
         * @param  string $view_path  put prema traženom view-u
         * @param  array  $args       array sa argumentima koji su prosljeđeni funkciji
         * @return [void]
         */
        public function render($view_path, $args = array()){
            
            $view = $this->view;
            
            $args['strap'] = $this;// twigu prosljeđujemo sam 'Bootstrap' objekt 
            
        //spajamo argumente prosljedeni funkcija sa onima spremljenim u strap objektu
            $args = array_merge($args, $this->args);
                                   
        // ako postoji flash poruka prosljeđujemo je twigu   
            if(isset($_SESSION['flash']))  {

                $args['flash'] = $_SESSION['flash'];    
                $_SESSION['flash'] = NULL;//resetiramo flash poruku  
            }                 

            echo $view->render($view_path, $args);
        }// render()
        

        /**
         * funkcija za spremanje argumenata koji se prosljeduju u view
         * @param  [type] $argN ime argumenta
         * @param  [type] $argV vrijednost
         * @return [void]      
         */
        public function storeArg($argN, $argV){
            $this->args[$argN] = $argV;
        }

        /**
         * postavlja view objekt u klasi
         * @param [string] $view_folder put prema view folderu
         * @param [type] $callback funkcija za pozivanje prilikom postavljanja view-a, vraća objekt
         *                          koji spremamo u polje view
         */
        public function setView($view_folder, $callback){
            
            $twig = call_user_func($callback, $view_folder);
            $this->view = $twig;
        }
            
        
        /**
         * metoda za pozivanje funkcije nekog routa, treba doraditi kako bi koristila 
         *  call_user_func_array() funkciju
         * @param  [array] $func_data array sa podacima o funkciji(ime, parametri)
         * @return [bool]  vraća true ili false
         */
        private function _call_user_function($func_data)
        {           
            $route = $func_data[0];

            if($this->method == 'post') 
                $routes = $this->postRoutes;
            else                        
                $routes = $this->getRoutes;
        
            $callback = isset($routes[$route]['callback']) ? $routes[$route]['callback'] : null;
                

            if($callback)
            {   

            //kod treba doraditi(sintaksa)
                foreach($routes[$route]['groups'] as $group){
                    foreach($this->groups as $groupName => $groupCalls)
                    {
                        //ako route pripada odredenoj grupi pozivaju se njoj pripadajuce funkcije
                        if($group == $groupName)
                        {
                            foreach($groupCalls as $call) call_user_func($call);//pozivamo funkcije povezane s odredenom grupom
                        }
                    }
                }

            /*OVAJ DIO JE NEPOTREBAN
                $args = array();
                for($i=1; $i < count($func_data); $i++)
                {   
                //stvaranje asocijativnog arraya od ostatka podataka iz URL-a
                    $arg_row = explode("_", $func_data[$i]);                    
                    $args[$arg_row[0]] = clean($arg_row[1]);                        
                }
                
                if(!empty($args))
                {
                    call_user_func($callback, $args);
                }               
                else
                {
                    call_user_func($callback);  
                }
            */
           
                try{//hvatamo greske s bazom podataka

                     call_user_func($callback);
                }
                catch(PDOExcepton $e){
                    $this->_logDBError($e->getMessage());
                }

                return true;
            }//if callback
            
            return false;          
        }// _call_user_function()


        /////////////////
        //error loging //
        /////////////////

        private function _logError($e, $logPath)
        {
            $error = "time: " . time() . " line: " . $e->getLine() . ", in file " . $e->getFile() . ", with message: " . $e->getMessage() . PHP_EOL;
            error_log($error, 3, $logPath);
        }

        public function logDBError($e)
        {
            $this->_logError($e, $this->db_error_log);
        }

        public function logUserError($e){

            $this->_logError($e, $this->error_log);
        }

        /**
         * postavlja objekt za komunikaciju sa bazom podataka preko callbacka, za sam 
         * način postavljanje pogledajte start.php skriptu
         * @param [function] $callback funkcija koja stavlja objekt u 'Bootstrap'
         */
        public function setDB($callback)
        {           
            call_user_func($callback);
        }
        
        /**
         * metoda za preusmjeravanje korisnika preko header(Location),
         *  moguće joj je proslijediti flash poruku
         *  
         * @param  [type] $target [description]
         * @param  [type] $msg    [description]
         * @return [type]         [description]
         */
        public function redirect($target, $msg=null, $class=null){
            
            if($msg)
                $this->flash($msg, $class);
            
            $location = $this->baseUrl . "/" . $target;
            header("Location: $location");
        }

        /**
         * metoda flash služi za spremanje i prikaz flash poruka u i iz session array-a
         * 
         * @param  [string] $msg      [description]
         * @param  string $msgClass klasa za prikaz poruke /nije implementirano
         * @return [type]           [description]
         */
        public function flash($msg=null, $msgClass='info'){     
            
            if($msg)
            {
                
                $_SESSION['flash']      = $msg;
                $_SESSION['flashClass'] = $msgClass;
                $this->flashMsg         = $msg;         
            }
            else
            {   
                if(isset($_SESSION['flash']))
                {
                    echo $_SESSION['flash'];
                    unset($_SESSION['flash']);
                }
            }   
        }//\flash()
        
        /**
         * postavljanje funkcije koja će se pokretati prije pozivanja traženog routa
         *  (pokretanje sessiona, provjera logina korisnika i sl.)
         *  
         * @param function  $callback 
         */
        public function setInit($callback) //funkciji prosljedujemo callback funkciju
        {           
            $this->start_callbacks[] = $callback;
        }
        
        /**
         * pozivanje već postavljenih funkcija iz 'this->start_callbacks' arraya
         * @return [type] [description]
         */
        private function _runStartCalls(){
            
            $calls = $this->start_callbacks;            
            foreach($calls as $call)
            {           
                call_user_func($call);
            }
        }
        
        /**
         * renderiranje stranice sa 404 greškom/ treba dodati funkcionalnost prikaza bilo
         *  koje greške
         */
        public function notFound()
        {           
            $this->render('errors/404');
        }
        
        /**
         * dobavljanje url za zadani route
         * @param  [string] $routeName ime routa, koristi se pri pretraživanju arraya
         * @return string, bool vraca url routa ili false ako url ne postoji
         */
        public function urlZa($routeName)
        {               
            foreach($this->getRoutes as $key => $route){
                
                if($route['name'] == $routeName)
                {       
                    return  BASE_URL . "/" . $key;
                    break;
                }
            }
            return false;
        }//urlZa()
        
        public function isAdmin(){

            return false;
        }

        public function isLoggedIn()
        {
            if( !Request::getSession('loggedIn') ) 
            return false;

            return true;
        }
    }