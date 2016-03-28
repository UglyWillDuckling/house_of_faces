


        window.onload = function() {

            var opinions   = document.getElementsByClassName('opinionArea');
            var replies    = document.getElementsByClassName('replyArea');

            var statusGumb = document.getElementById("statusBtn");
            var userStatus = document.getElementById("userStatus");
            var replyBtns  = document.getElementsByClassName("replyBtn");

            var statusPics = document.getElementById('statPics');


        //adding the event listener for comments(on enter key)
            for(var i=0; i<opinions.length; i++)
            {
                opinions[i].addEventListener("keypress", function(e){

                    if(e.keyCode == 13)
                    {
                        sendComment(this, 'comment');//comment je callback
                    }
                });
            //autoresize za textarea komentara
                opinions[i].addEventListener('keyup', resize, false); 
            } 



            for(var i=0; i<replies.length; i++)
            {
            //dodajemo isti eventLister i na replyArea ali drugi argument kojeg prosljedujemo funkciji ima vrijednost 'reply'  
                replies[i].addEventListener("keypress", function(e){

                    if(e.keyCode == 13)
                    {   
                        sendComment(this, 'reply');
                    }
                }); 

                replies[i].addEventListener('keyup', resize, false);   
            }
           

            var likeBtns = document.getElementsByClassName('likeBtn');

            for(var i=0; i<likeBtns.length; i++){

                likeBtns[i].addEventListener("click", function(e){

                    meLikey(this);            
                });
            }

             for(var i=0; i<replyBtns.length; i++){

                replyBtns[i].addEventListener("click", function(e){
                    focusOnText(this);            
                });
            }

    //////////////////////////////////////////////////
    //dobavi ime korisnika i put do korisnicke slike//
    //////////////////////////////////////////////////
    
        var userImage    = document.getElementById('userImage').src,
            userNickName = document.getElementById('username').innerHTML;

//update status
        statusGumb.onclick = function(e) {
            if(userStatus.value){
                statusUpdate(userStatus.value, userNickName, userImage, statusPics);
            }
        };

        var fajlBtn = document.getElementById('fajlBtn');

        fajlBtn.addEventListener( 'change', (e) => { pictureUpload(fajlBtn, statusPics); } );


    //preko custom gumba aktiviramo 'fajlBtn'
        document.getElementById('photoButton').onclick = (e) => {
            fajlBtn.click(); return false; 
        };  

    //search
        var 
            search = getById('search'),
            searchButton = getById('searchButton'),
            userList = getById('userList'),
            clickDiv = getById('clickDiv');

            search.onkeyup = function(){
                
             find(search, userList, clickDiv);    
            };

            search.onfocus = function(){

                if(userList.firstChild)
                {
                    userList.className = "userList";               
                    find(search, userList, clickDiv);
                }   
                //clickDiv.className = "";           
            }

    //close the search       
        clickDiv.onclick = function(e){
            deleteElement(clickDiv);
            deleteElement(userList);  //we close both the clickDiv and the userList
        }


        searchButton.onclick = function(){
            var wanted =  search.value;

            if(wanted.length > 2){
                var target = searchButton.getAttribute('data-target');
                    target = target + "?wanted=" + wanted;
                    
                window.location = target;
            }    
        }

        var friendA = getById('friendAnchor');
        var requests = getById('requests');
        var requestBs = getByClass('requestBtn');

        friendA.onclick = function()
        {
            if(requests.className == "noneDisplay") {

                if(requests.innerHTML.trim())//ako postoje neodgovoreni zahtjevi
                    requests.className = "requests";
            }
            else {requests.className = "noneDisplay";}
        };


        for(var i=0; i < requestBs.length; i++){


            requestBs[i].onclick = function(e){ 


                var yesNo = this.parentNode;
                var requestId = yesNo.getAttribute('data-id');
                var type;

                if(this.className.indexOf('acceptRequest') !== -1){//ako je gumb klase 'acceptRequest'
                    type=1;
                }
                else{
                    type=0;
                }

                replyRequest(type, requestId, yesNo);//odgovoramo na zahtjev za prijateljstvo
            }
        }

     }//the end  



