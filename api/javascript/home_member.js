


        window.onload = function() {

            var statusPics = document.getElementById('statPics');

        //adding the event listeners
            for(var i=0; i<eventAssigner.functions.length; i++)
            {
               eventAssigner.functions[i](); 
            }

    //////////////////////////////////////////////////
    //dobavi ime korisnika i put do korisnicke slike//
    //////////////////////////////////////////////////
    
        var userImage    = document.getElementById('userImage').src,
            userNickName = document.getElementById('username').innerHTML;
            
    }//the end  



