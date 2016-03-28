

    function makePost(status, data, username, userPic)
    {
        var post = document.createElement('div');
        post.className = "post";

        post.setAttribute('data-post', data['id']);

        var userInfo = createEl('div');
        userInfo.className = "userInfo";
        post.appendChild(userInfo);


        var userImage = createEl('div');
        userImage.className = "userImage";
        userInfo.appendChild(userImage);


    //korisnicka slika
        var img = createEl('img');
        img.src = userPic;
        userImage.appendChild(img);


        var userName = createEl('div');
        userName.className = "userName";
        userName.innerHTML = username;

        userInfo.appendChild(userName);


        var postContent = createEl('div', 'post_content');
        post.appendChild(postContent); //dodajemo post content u div 'post'

        var postPics = createEl('div', 'postPics');
        postContent.appendChild(postPics);


        if(data['pics']){

            var pictures = data['pics'];
            for(var i=0; i<pictures.length; i++){

                var slika = createEl('img', 'postPic');
                slika.src = pictures[i];

                postPics.appendChild(slika);
            }

        }


        var tekst = createEl('div', 'tekst');
        tekst.innerHTML = status; //dodajemo novi status

        postContent.appendChild(tekst);


        var interact = createEl('div', 'interact');

        postContent.appendChild(interact);

        ////////////////////////
    //dodaj event handler //
        ////////////////////////
        var commentButton = createEl('button', 'postBtn commentBtn');       
        commentButton.setAttribute('data-postId', data['id']);//ubacujemo id novog posta

        commentButton.addEventListener('click', function(e){
            focusOnText(commentButton);
        });

        interact.appendChild(commentButton);

        var inline = createEl('span');
        inline.innerHTML = "comment";

        commentButton.appendChild(inline);

        //stvaramo prostor za komentiranje
        var komentisi = createEl('div', 'komentisi');

        post.appendChild(komentisi);

        var op = createEl('div', 'opinion');
        komentisi.appendChild(op);

        var userImageContainer = createEl('div', 'userImage');

        op.appendChild(userImageContainer);

        var img2 = createEl('img');
        img2.src = userPic;

        userImageContainer.appendChild(img2);

        var commentInfo = createEl('div', 'commentInfo');

        op.appendChild(commentInfo);

        var textSpace = createEl('textarea', 'opinionArea');
        textSpace.setAttribute('placeholder', 'komentiraj...');
        textSpace.setAttribute('data-postId', data['id']);

        textSpace.addEventListener("keypress", function(e){

            if(e.keyCode == 13)
            {
                sendComment(this);
            }
        });

        commentInfo.appendChild(textSpace);

        return post;
    }

    function statusUpdate(status, nick, pic, statusPics){

        var data = new FormData();
        data.append('status', status);

        var link = "/house_of_faces/statusUpdate";
        
        ajax(link, data, function(xhr){

            //napravi novi post i dodaj ga u posts div
                var dataResponse = JSON.parse(xhr.responseText);
                //console.log(dataR);

                if(dataResponse['id']){
                    
                    var id = dataResponse['id'];
                    var posts = document.getElementById('posts');

                    var update = makePost(status, dataResponse, nick, pic);

                    posts.insertBefore(update, posts.firstChild);
                    statusPics.innerHTML = "";
               }
               else{

                //show message
               }
        });
    }

    function pictureUpload(fajl, statusPics){

        var obrazac = new FormData;
        var link = "/house_of_faces/tempPicture";

        obrazac.append('image', fajl.files[0]);

        ajax(link, obrazac, function(xhr){

            var data = JSON.parse(xhr.responseText);

            if(data['path']){

                var imgContainer = document.createElement('div');
                    imgContainer.className = "statPicContainer";

                var image = document.createElement('img');
                    image.className = "statPic";
                    image.src = data['path'];

                imgContainer.appendChild(image);


                var btn = document.createElement('button');
                    btn.className = "removeStatPic";
                    btn.innerHTML = "x";
                    btn.onclick = function(e){

                        var tata = this.parentNode;

                        removeStatPic(tata);  
                    };

                imgContainer.appendChild(btn);


                statusPics.appendChild(imgContainer);
            }
            else{

               alert("error during picture upload.");
            }
       });
    }

    function removeStatPic(dad){

        var source = dad.firstChild.src;
        var form = new FormData;

        form.append('source', source);

        var link = "/house_of_faces/removeTmpPicture";

        ajax(link, form, xhr => {

            var data = JSON.parse(xhr.responseText);           

            if(data['ok']){
            //image successfully removed
                dad.parentNode.removeChild(dad);
            }
            else{
                //show error message
            }
        });
    }

    eventAssigner.addListener('statusUpdate', function(){

        var userStatus   = document.getElementById("userStatus");
        var statusGumb   = document.getElementById("statusBtn");
        var userImage    = document.getElementById('userImage').src;
        var userNickName = document.getElementById('username').innerHTML;
        var statusPics   = document.getElementById('statPics');

        //update status
        statusGumb.onclick = function(e) {
            if(userStatus.value){
                statusUpdate(userStatus.value, userNickName, userImage, statusPics);
                userStatus.value="";
            }
        };

        document.getElementById('photoButton').onclick = (e) => { //preko custom gumba aktiviramo 'fajlBtn'
            fajlBtn.click(); return false; 
        };  

        var fajlBtn = document.getElementById('fajlBtn');
        fajlBtn.addEventListener( 'change', (e) => { pictureUpload(fajlBtn, statusPics); } );


        userStatus.addEventListener('keyup', resize, false); 
    });

