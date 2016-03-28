<?php   namespace Tricky\Json;

    use Tricky\helpers\Request\Request;

    class Json{

        public $message;

        private $useCsrf;
        private $csrfCode;
        private $strap;

        public function __construct($strap, $useCsrf=true)
        {
            $this->useCsrf = $useCsrf;
            $this->strap = $strap;

            $this->storeCsrfToken();//spremamo csrfToken iz sessiona
        }

        /**
         * slanje json objekta
         * @return [type] [description]
         */
        public function send()
        {
            $msg = $this->message;

            if($this->useCsrf){ 
                $msg['csrfToken'] = regenerateCsrf(); 
            }
            echo json_encode($msg);
        }

        /**
         * uključivanje i isključivanje csrf funkcionalnosti
         * @param bool $c
         */
        public function setCsrf(bool $c){
            $this->useCsrf = $c;
            return $this;
        }

        public function storeCsrfToken(){
            $this->csrfCode = Request::getSession('csrfToken');
        }

        /**
         * stvaranje nove vrijednosti u svojstvu message ili modificiranje postojeće
         * @param [string] $name [description]
         * @param [mix] $val  [description]
         */
        public function setValue($name, $val){
            $this->message[$name] = $val;
            return $this;
        }

        /**
         * uklanjanje zadanog polja iz message svojstva
         * @param  [string] $name [description]
         * @return [this]       [description]
         */
        public function removeValue($name){
            unset($this->message[$name]);
            return $this;
        }

        public function setMessage($msg){
            $this->message = $msg;
        }
    }