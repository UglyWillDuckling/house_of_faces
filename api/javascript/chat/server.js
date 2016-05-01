

   var  mongo  = require('mongodb').MongoClient,
        client = require('socket.io').listen(8080).sockets,
        mysql = require('mysql');
        sockets = [];


    var mysqlDb = mysql.createConnection({
        host     : '127.0.0.1',
        user     : 'root',
        password : '',
        database: 'book_of_faces'
    });

    mysqlDb.connect(function(err){
        if(err) console.log(err);
    });


    mongo.connect('mongodb://127.0.0.1/chat', function(err, db){

        if(err) throw err;

        var col = db.collection('messages'),
            baseUrl = "http://127.0.0.1/house_of_faces/",
            defaultImage = baseUrl + "public/images/default/defaultUser.png";


        client.on('connection', function(socket){

            var sendStatus = function(s){
                socket.emit('status', s);
            },
            checkFriends = function(friends , checkImage){

                var onlineFriends = [];
                for(var i=0; i<friends.length; i++)
                {
                    if(typeof sockets[friends[i].uniqueId] !== "undefined") { onlineFriends.push(friends[i]); }  

                    if(checkImage){

                        if(friends[i].userImage)
                            friends[i].userImage = baseUrl +  friends[i].userImage; 
                        else
                            friends[i].userImage = defaultImage; 
                    }                                       
                }

                return onlineFriends;
            };

        //save new user
            socket.on('new_user', function(data){

                sockets[data.uniqueId] = socket; //save this socket in the array(under user_id)
                socket.user_id = data.uniqueId; //save the temporary user_id in the socket itself


                findFriends(data.uniqueId, function(friends, username, userImage){
                    socket.friends = friends; //we save this users friends in the socket for future use

                    var onlineFriends = checkFriends(friends, true);//checkFriends returns the friends that are online

                    socket.emit('onlineFriends', {
                        onlineFriends: onlineFriends,
                        username: username,
                        userImage: userImage
                    });//we also return the users image and its username
                });     
            });

            socket.on('checkFriends', function(){

                var friends = checkFriends(socket.friends);

                socket.emit('onlineFriends', {
                    onlineFriends: friends
                });
            });

            socket.on('disconnect', function(data){
                //remove from array
                sockets.splice(sockets.indexOf(socket.user_id), 1);
            });

            //wait for input
            socket.on('message', function(data){

                //console.log(sockets);
                var message = data.message,
                    friend_id = data.friend_id,
                    whitespacePattern = /^\s*$/;

                if(whitespacePattern.test(friend_id) || whitespacePattern.test(message)){
                    sendStatus('name and message is required.');
                }
                else{              

                    if(typeof sockets[friend_id] !== "undefined"){               
                        sockets[friend_id].emit('output',  //we send the message to the correct user
                        { 
                            user_id: socket.user_id,
                            message: message
                        });
                    }
                   
                   sendStatus({
                    message: "Message sent",
                    clear: true
                   }); 
                }          
            });

            socket.on('checkFriends', function(){
                socket.emit(checkFriends(socket.friends));
            });     
        });

        //function for finding a given users friends
          var findFriends = function(uniqueId, callback){


            var upit = 
                "SELECT u.id, u.name, p.path as userImage from users as u " +
                "LEFT JOIN pictures as p ON u.user_image = p.id " +
                "WHERE u.uniqueId='" + uniqueId + "'";

            mysqlDb.query(upit, function(err, userRows){

                if(err) throw err;

                var user_id = userRows[0].id,
                    username =userRows[0].name,
                    userImage = userRows[0].userImage;

                if(userImage) 
                    userImage = baseUrl + userImage;
                else
                    userImage = defaultImage;


                var friendQuery = 
                "SELECT u.name as username, u.nickname, u.uniqueId, p.path as userImage " +
                "FROM friend_requests as fr " +
                " INNER JOIN users as u ON u.id= fr.friend_id " +
                " LEFT JOIN pictures as p ON p.id=u.user_image " +
                " WHERE fr.user_id=" + user_id
                ,
                friends; 

                var query = mysqlDb.query(friendQuery, function(err, rows){

                    friendQuery = friendQuery = friendQuery.replace("friend_id", 'user_id');
                    var ind = friendQuery.lastIndexOf('user_id');

                    friendQuery = friendQuery.substring(0, ind) + "friend_id=" + user_id;

                    var query2 = mysqlDb.query(friendQuery, function(err2, rows2){

                        if(!rows)
                            friends = rows2;                       
                        else if(!rows2)
                            friends = rows;
                        else
                            friends = Object.assign(rows, rows2);

                        callback(friends, username, userImage);
                    });           
                });
            });       
        };
    });//connect to MongoDB


   