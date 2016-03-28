<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\helpers\Hash\Hash;
    use SwiftMailer\SwiftMailer;


    $strap->setPost('auth/register', 'auth/register', function() use($strap) {

        $val = $strap->validate;
        $db  = $strap->db;


        $name     = Request::getPost('name');
        $lastName = Request::getPost('lastName');
        $email    = Request::getPost('email');

        $password = Request::getPost('password');
        $password_confirm = Request::getPost('password_confirm');

        $nickname = Request::getPost('nickname');
        $quote    = Request::getPost('quote');

        $image = Request::getFile('image');
        $tmp_name = $image['tmp_name'];

        $val->validate([
            'name' => [$name, 'required|min(2)|required|max(40)'],
            'lastName' => [$lastName, 'required|min(2)|required|max(40)'],
            'email'    => [$email, 'required|email|unique(users, email)'],

            'password' => [ $password,"password"],

            'image' => [$tmp_name, 'image(10)'],

            'password_confirm' => [$password_confirm, 'required|matches(password)'],
            'nickname' => [$nickname, 'min(3)|max(99)|unique(users, nickname)'],
            'quote' => [$quote, 'min(5)']
        ]);

        if($val->passes()){
       
            try{

                $password = password_hash($password, $strap->config['password']['algo']);//ime algoritma se nalazi u config datoteci
                $code = bin2hex(openssl_random_pseudo_bytes(64)); //stvaramo kod za konfirmacijski email

                $db->startTransaction();             
                $db->setTable('users');

                $db->add([
                 'name'     => $name,
                 'last_name' => $lastName,
                 'email'    => $email,
                 'password' => $password,
                 'nickname' => $nickname,
                 'quote'    => $quote,
                 'activated' => 0,
                 'activation_code' => $code
                ]);

                $user_id = $db->lastInsertId();


                if($image){//ako je slika uploadana spremamo je u tablicu i na server 

                    require INC_FOLDER . "/api/functions/saveImage.php";    

                    $relPath = "/public/images/users/";
                    $jibberJabber = bin2hex(openssl_random_pseudo_bytes(32));//nasumični string

                    $relPath = $relPath . $jibberJabber . "." . pathinfo($image['name'], PATHINFO_EXTENSION);   
                    $path = INC_FOLDER . $relPath; //absolute path
                    saveImage($path, $relPath, $db, $image, $user_id);
                }

            //send the activation email  
                require INC_FOLDER . "/api/functions/sendActivationEmail.php";           
                sendActivationEmail($email, $name, $code);
                    
                              
                $db->commit();//potvrdujemo sve učinjene promjene u bazi podataka

                $strap->render('activation/activation.twig', [
                    'email' => $email,
                ]);                
            }
            catch(PDOException $e){

                if( isset($path) ) unlink($path);//u slučaju da je slika spremljena brisemo ju     

                $db->rollback();

                $strap->logDbError($e->getMessage());
                $db->redirect('home', 'greska u bazi podataka.');
            }
            catch(Exception $e){
                if( isset($path) ) unlink($path);//u slučaju da je slika spremljena brisemo ju

                $db->rollback();               

                $strap->logUserError($e->getMessage());
                $db->redirect('home','ispričavamo se, došlo je do greške prilikom registracije. Pokušajte ponovno.');              
            }
        }
        else {
            //die($val->errors()->first('password_confirm'));
            $strap->render('home/home_register.twig', [ //prikazujemo registracijsku stranicu te prosljeđujemo greske twigu
                'errors' => $val->errors()
            ]);             
        }
    }, ['csrf', 'createCsrf']);// csrf je grupa za provjeru valjanosti csrf tokena, takoder moramo stvoriti novi token za slučaj d a moramo ponovno prikazati registracijski obrazac 

