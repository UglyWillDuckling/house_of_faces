

    function find(search, list, click){

        var word = search.value;

        if(word.length > 2){

            var form = new FormData;

            form.append('word', word);

            var link = eventAssigner.baseUrl + "/find";

            ajax(link, form, function(xhr){
                
              //show the recieved results             
                var data = JSON.parse(xhr.responseText);

                if(data['error']){
                    //show error message
                }else{
                    var users = data['users'];

                    list.innerHTML = "";

                    var user;
                    var i = 0;
                    for(user in users)
                    {
                        var person = users[i];

                        var item = makeItem(person.name, person['user_id'], person['userImage']);
                        list.appendChild(item);

                        i++;
                    }

                    if(i){ //ako je bilo rezultata prikazujemo listu
                        list.className = "userList"; 
                        click.className = "";
                    } 
                    else { 
                        list.className = "noneDisplay"; 
                        click.className = "noneDisplay";
                    }
                }
             });
        } 
        else{
            { list.className = "noneDisplay"; }
        }    
    }


    function makeItem(username, id, imagePath){

        var liItem = createEl('li');

        var ankor = createEl('a');

            ankor.href = eventAssigner.baseUrl + '/profile?id='+ id;

            liItem.appendChild(ankor);

        var littleUser = createEl('div', 'littleUser');
                 
        ankor.appendChild(littleUser);    

        var userImage = createEl('img', 'userImage'); 
            userImage.src = imagePath ? ('http://127.0.0.1/house_of_faces/' + imagePath) : 'http://127.0.0.1/house_of_faces/public/images/default/defaultUser.png'; 
            userImage.setAttribute('alt', 'userImage');

        littleUser.appendChild(userImage);


        var info = createEl('div', 'userInfo');

            littleUser.appendChild(info);

        var name = createEl('div', 'userName');
            name.innerHTML = username;

        info.appendChild(name);


        return liItem;
    }

    function close(field){
        field.value = "";
        console.log('hey');
    }



    var lastExecution;
//assign the search event listeners
    eventAssigner.addListener('search', function(){
         //search
        var 
            search = getById('search'),
            searchButton = getById('searchButton'),
            userList = getById('userList'),
            clickDiv = getById('clickDiv');


            var lastValue;
            setInterval(function(){//stvaramo svojevsrni custom event, koji provjerava vrijednost u search polju svakih 1000ms
                if(lastValue != search.value){

                    if(search.value.length > 2){
                        console.log(search.value);
                        find(search, userList, clickDiv);//ako je doslo do promjene saljemo ajax zahtjev pomocu find()funkcije
                        lastValue = search.value;
                    }
                }
            
            }, 1000);
   
            search.onfocus = function(){

                if(userList.firstChild)
                {
                    userList.className = "userList";;
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
    });