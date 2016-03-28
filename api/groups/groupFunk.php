<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


    /**
     * provjera postojanja i ispravnosti csrf tokena
     * @var function
     */
    $checkCsrf = function() use($strap){

        //preCode($_POST);die;

        $sessionToken = Request::getSession('csrfToken');
        $postToken    = Request::getPost('csrfToken');

        if(!$sessionToken || !$postToken || $sessionToken !== $postToken)//provjera postojanja i ispravnosti tokena
        {   
            $strap->flash(' bugger off Mate!!! ', 'error');
            $strap->redirect('home');
            die;
        } else {
            unset($_SESSION['csrfToken']);
            unset($_POST['csrfToken']);
        }
    };

    /**
        *createCsrf stvara nasumično generirani hash na temelju stringa kojeg stvara klasa  
     * @var function
     */ 
    $createCsrf = function() use($strap){
    
        $rand  = $strap->random;
        $token = hash( 'sha256', $rand->generateString(128) );

        $_SESSION['csrfToken'] = $strap->csrfToken = $token; //ovu varijablu koristimo u obrascima
        $strap->Json->storeCsrfToken(); //spremamo token i u json objekt   
    };

    /**
     * provjerava je li korisnik admin
     * @var function
     */
    $adminCheck = function() use($strap){

        if( !$strap->isAdmin() ) {
            $strap->redirect('home', 'niste administrator');
            die;
        }
       
    };

    /**
     * provjera da li je korisnik prijavljen, ako nije preusmjeravamo na pocetnu stranicu
     * @var [type]
     */
    $isMember = function() use($strap){

        if( !$strap->isLoggedIn() ){
            $strap->redirect('home', 'niste logirani');
            die;
        }     
    };

    /**
     * function for deleting temporary pictures that are uploaded when the user tries to include a picture with a post
     * @var function
     */
    $deleteStatPics = function() use($strap){

        $userId = Request::getSession('user_id');  
        if($userId)
        {
            $dir = INC_FOLDER . "/public/images/temp/users/" . $userId;
            if( file_exists($dir) ){

                $files = array_slice(scandir($dir), 2);//uzimamo sve datoteke osim prve dvije
      
                foreach($files as $file) { unlink($dir . "/" . $file); } //brisanje slika     
            }
        }         
    };
