
     function replyRequest(answer, id, daNe){

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
     });