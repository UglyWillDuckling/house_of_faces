
    eventAssigner.addListener('friends', function()
    {
        var requestBtns = getByClass('sendRequestBtn');
        var controls = getByClass('friendControl');
        var clicked = getById('allFriendsBtn');        //varijabla za nadzor trenutno kliknutog gumba
        var currentField = getById('allFriends');


        for(var i=0; i<requestBtns.length; i++){
            requestBtns[i].addEventListener('click', whyCantWeBeFriends);
        }

        for(var i=0; i<controls.length; i++){
            controls[i].addEventListener('click', function(){

                if(clicked) clicked.classList.remove('clicked');

                this.classList.add('clicked');
                clicked = this;
            });

            controls[i].addEventListener('click', function(){
  
                var field = this.getAttribute('data-field');

                var field = query('.friendField[data-field="' + field + '"]');

                if(currentField != field)
                {
                    currentField.classList.add('noneDisplay');
                    field.classList.remove('noneDisplay');

                    currentField = field;
                }    
            });
        }

        var acceptBtn = getById('acceptRequest');
        var declineBtn = getById('declineRequest');

        if(acceptBtn){

            var userId = acceptBtn.parentNode.getAttribute('data-userid');

            acceptBtn.addEventListener('click', function(e){
                console.log(userId);
                //find the friend request in the navigation and click it
                var accept = query('.acceptRequest[data-userid="' + userId + '"]');
                    
               accept.click();
            });

            declineBtn.addEventListener('click', function(e){
                //find the friend request in the navigation and click it
                var decline = query(
                    '.declineRequest[data-userid="' + userId + '"]');

                console.log('.declineRequest[data-id="' + userId + '"]');

            });

        }
        
        var deleteFriendBtns = getByClass('unfriendBtn');

        for(var j=0; j<deleteFriendBtns.length; j++){

            deleteFriendBtns[j].addEventListener('click', deleteFriend);
        }
    });

    function whyCantWeBeFriends(e){
        
        var userId = this.getAttribute('data-userid');

        var form = new FormData;

        form.append('user_id', userId);

        var link = 'http://127.0.0.1/house_of_faces/profile/beMyFriend';

        ajax(link, form, function(xhr){

            var data = JSON.parse(xhr.responseText);

            if(data['ok']){

                var btn = e.target;
                btn.removeEventListener('click', whyCantWeBeFriends);
                
                var note = btn.parentNode;

                note.innerHTML = "<small>request sent</small>";
            }
        });
    }

    function deleteFriend(){

        var id = this.getAttribute('data-userid');

        var link = eventAssigner.baseUrl + "/profile/deleteFriend";

        var f = new FormData;

        f.append('id', id);

        ajax(link, f, function(xhr){

            var reponse = JSON.parse(xhr.responseText);

            if(reponse['ok']){

                alert('friend deleted.');

                eventAssigner.profileInteractions.innerHTML = "";

                var reqeuestBtn = document.createElement('button');
                    requestBtn.className = "sendRequestBtn";
                    requestBtn.setAttribute('data-userid', id);
                    requestBtn.innerHTML = "send request";
                    requestBtn.addEventListener('click', whyCantWeBeFriends);


                eventAssigner.profileInteractions.appendChild(requestBtn);
            }
        });
    }