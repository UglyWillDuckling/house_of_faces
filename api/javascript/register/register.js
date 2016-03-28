    
    window.onload = function(){

          var nameI = document.getElementById('name');

         nameI.onblur = () => { if(nameI.value.length > 2) checkUsername(nameI.value); }

         var slika = getById('slikaKorisnika'); //slika korisnika
         var imageBtn = document.getElementById('imgBtn');
         var fajl  = document.getElementById('fajl');


         imageBtn.onclick = () => { fajl.click(); return false; }

         fajl.onchange    = () => { uploadUserImage(fajl.files, slika); }



         var email = getById('email');
         email.onblur = () => { if(email.value.length > 6) checkEmail(email.value); }
    }



   
        