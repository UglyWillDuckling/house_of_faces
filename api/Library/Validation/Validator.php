<?php namespace Tricky\Validation;

	use Violin\Violin;
	use Tricky\helpers\QueryObject\QueryObject;
	

	/**
	 * Klasa Validator produžuje klasu Violin(author: Alex Garrett)
	 * 'Validator' nam služi za provjeru unesenih vrijednosti od strane korisnika i sl.
	 * vrijednosti se validiraju preko funkcije validate() kojoj zadajemo ime 
	 * vrijednosti koje će  se koristiti u samom objektu, te zadajemo array 
	 * sa varijablom za provjeru i pravila koja treba primjeniti za danu vrijednost
	 * 
	 */
	class Validator extends Violin
	{

		public function __construct($boot){

			$this->addRuleMessage(
				'image', 
				'slika nije u redu.'
			);

			$this->addRuleMessage(
				'uniqueUser', 
				'korisničko ime već postoji'
			);

			$this->addRuleMessage(
				'argumenti', 
				'potrebni argumenti nisu uneseni'
			);

			$this->addRuleMessage(
				'unique',
				'{field} vec koristeno.'
			);
			$this->addRuleMessage(
				'required', 
				'morate unijeti {field}'
			);
			$this->addRuleMessage('min','{field} mora sadrzavati najmanje 3 znakova.');

			$this->addRuleMessage(
				'max', 
				"{field} ne smije imati vise od {arg0} znakova."
			);

			$this->boot = $boot;
		}
		
		public function validate_futureDate($date, $input, $args){

			$this->addRuleMessage(
					'date', 
					'datum rezervacije mora biti u budućnosti.'
			);	

			$givenTime = strtotime($date);

			$sutra = strtotime('tomorrow');

			//datum mora biti u budućnosti
			if($sutra <= $givenTime)
				return true;

			return false;
		}

		/**
		 * metoda za provjeru jednistvenosti zadanog username-a
		 * 
		 * @param  [string] $username dani username
		 * @param  [type] $input  
		 * @param  [type] $args     
		 * @return [bool] vraca true, false ovisno da li username postoji u bazi
		 */
		public function validate_unique($value, $input, $args){

			$db = $this->boot->db;

			$db->setTable($args[0]);

			$q = new QueryObject;

			$q->setRule([
				$args[1],
				'=',
				$value
			]);

			$db->where($q);

			return !($db->prvi());	//pretvaramo dobivenu vrijednost u suprotni bool
		}

		public function validate_argumenti($arguments, $input, $args){
	
			$ok = true;
			if( !empty($arguments) ){
				
				foreach($arguments as $argument)
				{
					if($argument['req'] == 'req' && strlen($argument['value']) < 1)
					{
						$ok = false;
					}
				}	
			}
			
			return $ok;	
		}

		public function validate_newArguments($arguments = array(), $input, $args){
	
			$ok = true;
			for($i=0; $i<sizeof($arguments); $i++)
			{
			
				$argument = $arguments[$i];

				$title 		= $argument->name;
				$trueName 	= $argument->trueName;
				$req 		= $argument->req;
				$refTable 	= $argument->refTable;
				$refField 	= $argument->refField;


				$ok = length(array(
					$title,
					$trueName,
				), 5);

				$refTable = $argument->refTable;
				if(
					!ctype_digit($req) 
						|| 
					!ctype_digit($refTable) 
						|| 
					!ctype_digit($refField) 
				) $ok = false;
					

				if(!$ok){

					$this->addRuleMessage(
						'newArguments',
						'argument ' . $title . ' nije ispravno ispunjen.'
					);	

					return $ok;
				}
			}		
			return $ok;		
		}//validate_uniqueArguments()

		function validate_password($value, $input, $args){


			$this->addRuleMessage(
					'password', 
					'lozinka mora biti u zadanom formatu.'
			);	
			$regEx = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/"; //jedan broj, jedno malo i jedno veliko slovo, min 6 znakova

			return preg_match($regEx, $value);
		}


      function validate_image($pic, $input, $args){

      	$maxS = 100000 * $args[0];
      	$size = filesize($pic);

        if($size < $maxS)
        {
            $trueType = !@imagecreatefromgif($pic);
            
            if(!$trueType)
            {
                $trueType = !@imagecreatefrompng($pic);

                if(!$trueType)
                    $trueType = !@imagecreatefromjpeg($pic); 
            }
            
            if($trueType)
                return true;
        }

        return false;
      }		 
}//kraj 'Validator' klase
	