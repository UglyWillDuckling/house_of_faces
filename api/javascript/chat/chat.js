

     eventAssigner.addListener('chat', function(){

        var ea = eventAssigner;

        var userList = getByClass('chat-list')[0];
            chat_messages = getById('chat-messages'),        
            chatWrapper = getById('chatAnimationWrapper'),        
            showChat = getById('showChat'),        
            chat_talk = getById('chat-talk'),        
            messageArea = getById('messageArea'),
            identifier = getById('identifier').value,
            closeTalkButton = getById('closeTalk'),
            chosenUser = "",
            username,
            userImage,
            scrollMessages = function(){

                chat_messages.scrollTop = chat_messages.scrollHeight;
            },
            closeTalk = function()
            {
                if(!chat_talk.classList.contains('noneDisplay')) { chat_talk.classList.add('noneDisplay'); }
            },

            showHideChat = function(){

                if( chatWrapper.classList.contains('visible') )
                {
                    chatWrapper.classList.remove('visible');
                    return 1;
                }
                chatWrapper.classList.add('visible');
            },

            showTalk = function(){
            
               chat_talk.classList.remove('noneDisplay');        
            },

            changeUser = function(e){
                var user = this,
                    id = user.getAttribute('data-uniqueId');

                if(
                    typeof friends[id] !== "undefined" && 
                    messageArea.getAttribute('data-uniqueId') != id
                ){
                    chat_messages.innerHTML = "";//remove the former messages

                    for(var i=0; i<friends[id].messages.length; i++)
                    {
                    //list all messages
                        var message = createMessage(friends[id].messages[i]);
                        chat_messages.appendChild(message);
                    }

                    if(chosenUser)
                        chosenUser.classList.remove('clicked');

                //show that the user clicked on a friend    
                    user.classList.add('clicked');
                    chosenUser = user;

                    messageArea.setAttribute('data-uniqueId', id);
                    scrollMessages();
                }


                user.classList.remove('active');
                showTalk();//if the conversation area isnt being displayed we show it
            },
            createMessage = function(message){
                var msg = createEl('div', 'message'),
                    user;

                    if(!message.friend_id)
                    {
                        user = createUser(message.username, message.userImage);
                    }
                    else{//this is this users message
                        user = createUser(username, userImage);
                    }
                   
                    content = createEl('div', 'message-content');

                    msg.appendChild(user);
                    msg.appendChild(content);

                    content.textContent=message.message;

                return msg;
            },
            createUser = function(username, pic, uniqueId, listener){

                var user = createEl('div', 'chat-user'),
                    img = createEl('img'),
                    title = createEl('h4');

                    user.appendChild(img);
                    user.appendChild(title);
                    
                    img.setAttribute('src', pic);
                    title.textContent = username;


                user.setAttribute('data-uniqueId', uniqueId);
                if(listener){
                    user.addEventListener('click', changeUser);
                }

                return user;
            },           
            friends = {};    

            //start chatting

            showChat.addEventListener('click', showHideChat);

            try{

                var socket = io.connect('http://127.0.0.1:8080');
            }
            catch(e){
                console.log("error while connecting");
                return 0; //we end the script here
            }


            var checkFriends = setInterval(function(){
                 socket.emit('checkFriends'); 
            }, 5000); //we check for friends that are online every 5 seconds


            var simba = new buzz.sound( "public/sounds/lion.mp3"); //the sound for the time when the users recieves a new message
            simba.load();

/*LISTENERS*/
        //sending the new users uniqueId to the server    
            socket.emit('new_user', { uniqueId : identifier });

        //close the messaging area
            closeTalkButton.addEventListener('click', closeTalk);   

        //send a message to a friend
            messageArea.addEventListener('keydown', function(e){

                var area = this;

            //if the user pressed enter
                if(e.which == 13 && event.shiftKey === false){

                    var friend_id = area.getAttribute('data-uniqueId'),
                        message = {
                            user_id: identifier,
                            friend_id: friend_id,
                            username : username,
                            message: area.value
                    };

                    //send the message with socket io
                    socket.emit('message', message);
                    messageArea.value = '';
                    messageArea.textContent = '';

                    //add the users message into the messages array
                    friends[friend_id].messages.push({
                        message: message
                    });

                    //display the message 
                    chat_messages.appendChild(createMessage(message));
                    scrollMessages();
                }
            });

/*LISTENERS*/

        //receiving a message from a friend 
            socket.on('output', function(data){

                //save the new message
                var user_id  = data.user_id, //friends uniqueId
                    message  = data.message; //the message itself


                var msg = {
                    'message' : message,
                    'user_id' : user_id,
                    'username' : friends[user_id].nickname,
                    'userImage' : friends[user_id].userImage
                };  

                friends[user_id]["messages"].push(msg);

                if(messageArea.getAttribute('data-uniqueId') == user_id)
                {         
                    chat_messages.appendChild(createMessage(msg));//add the message directly into the message list
                    scrollMessages();   //we scroll to the bottom of the messages div



                }else{
                    simba.play();


                    var user = query(".chat-user[data-uniqueId='" + user_id + "']");

                    if(!user.classList.contains('active')) { user.classList.add('active'); }                  
                }  
            });

            socket.on('onlineFriends', function(data){

                var onlineFriends = data.onlineFriends;

                for(var j=0; j<friends.length; j++){

                    if(onlineFriends[friends[j].uniqueId] === "undefined"){

                        delete friends[j];

                        userList.removeChild(query(".chat-user[data-uniqueId='" + friends[j].uniqueId + "']"));
                    }
                }

                if(onlineFriends.length){
                    for(var i=0; i<onlineFriends.length; i++)
                    {
                        var uniqueId = onlineFriends[i].uniqueId;
                        if(typeof friends[uniqueId] === "undefined")
                        {
                            onlineFriends[i].messages = [];
                            friends[onlineFriends[i].uniqueId] = onlineFriends[i];

                             var user = createUser(
                                onlineFriends[i].nickname,                      
                                onlineFriends[i].userImage, 
                                onlineFriends[i].uniqueId,  
                                true
                            );

                            userList.appendChild(user);
                        }        
                    } 
                }
                else{
                    userList.innerHTML = "none of your friends are currently online. Sorry.";
                }


                if(data.username)//if the function is ran on startup the data will also contain this users info
                {
                    username = data.username;
                    userImage = data.userImage;
                }   
            });
    });