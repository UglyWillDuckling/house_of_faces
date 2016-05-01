
     function replyRequest(answer, id, daNe, userid){

        var link = "http://127.0.0.1/house_of_faces/interact/request";
        var form = new FormData();

        form.append('id', id);
        form.append('answer', answer);

        ajax(link,form, function(xhr){

            var data = JSON.parse(xhr.responseText);

            if(data['ok'])
            {
                //delete the request               
                daNe.innerHTML = answer ? 'accepted' : 'declined';
                updateRequestNumber();

            //this part is for the profile page only    
                var interactions = getById('profileInteractions');

                if(interactions.getAttribute('data-userid') == userid){
                    interactions.innerHTML = 
                        answer ? '<small>request accepted</small>' : '<small>request declined</small>'
                    ;
                }
            }
        });
     };

     function updateRequestNumber(){

        var numbers = getByClass('requestNumber');

        var numOfRequests = parseInt(numbers[0].innerHTML);

        if(numOfRequests){

            var newNumber = numOfRequests -1;
            
            var l = numbers.length;
            var i=0;
            if(newNumber)
            {
                for(var i=0; i<numbers.length; i++)
                {
                    numbers[i].innerHTML = newNumber;                   
                }
            }
            else{
                while(numbers[0]){

                    numbers[0].innerHTML = 0;
                    numbers[0].className = 'noneDisplay';
                }
            }   
        }
     }

     eventAssigner.addListener('requests', function(){

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
                var requestId = this.getAttribute('data-id');
                var userId = this.getAttribute('data-userid');
                var type;

                if(this.className.indexOf('acceptRequest') !== -1){//ako je gumb klase 'acceptRequest'
                    type=1;
                }
                else{
                    type=0;
                }

                replyRequest(type, requestId, yesNo, userId);//odgovoramo na zahtjev za prijateljstvo
            }
        }
     });