
    function createLikeButton(liked, id){
        var likeBtn = createEl('button', 'likeBtn noLike');

            likeBtn.setAttribute('data-contentid', id);
            likeBtn.addEventListener('click', function(){
                meLikey(this);
            });

                var spanner = createEl('span');

                likeBtn.appendChild(spanner);

        return likeBtn;
    }

    function createReplyBtn(id){
        var replyBtn = createEl('button', 'replyBtn');

            replyBtn.setAttribute('data-contentid', id);
            replyBtn.addEventListener('click', function(){
                focusOnText(this);
            });
            replyBtn.innerHTML = "reply";

        return replyBtn;
    }

    function createInteraction(liked, id, commentId){

        var commentInt = createEl('div', 'commentInteraction');

        commentInt.appendChild( createLikeButton(liked, id) );
        commentInt.appendChild( createReplyBtn(commentId) );

        return commentInt;
    }



    function makeReply(content_id, tekst, user, userPic, liked, commentId){

        var reply = document.createElement('div');
        reply.className = "reply";

            var userImage = document.createElement('div');
            userImage.className = "userImage";

            reply.appendChild(userImage);

                var img = document.createElement('img');
                img.src = userPic;

                userImage.appendChild(img);


            var commentInfo = document.createElement('div');
            commentInfo.className = "commentInfo";

            reply.appendChild(commentInfo);

            var username = document.createElement('div');
            username.className = "userName";

            username.innerHTML = user;

            commentInfo.appendChild(username);


            var comment_content = document.createElement('div');
            comment_content.className = "comment_content";

            commentInfo.appendChild(comment_content);

            var sadrzaj = document.createElement('div');
            sadrzaj.className = "tekst";

            sadrzaj.innerHTML = tekst;

            comment_content.appendChild(sadrzaj);


            var sadrzaj = document.createElement('div');
            sadrzaj.className = "tekst";

            commentInfo.appendChild(createInteraction(liked, content_id, commentId));

        return reply;
    }//\makeReply


    function makeComment(tekst, username, image, ownId, liked, replies){

            var opinion = document.createElement('div');
            opinion.className = 'opinion';

            var userImage = document.createElement('div');
            userImage.className = 'userImage';

            opinion.appendChild(userImage);

            var img = document.createElement('img');
            img.src=image;
         //we add the users image   
            userImage.appendChild(img);

        
            var commentInfo = document.createElement('div');
            commentInfo.className = 'commentInfo';

            opinion.appendChild(commentInfo);

            var userName = document.createElement('div');
            userName.className = 'userName';            
            userName.innerHTML = username;


            commentInfo.appendChild(userName);

            var commentContent = document.createElement('div');
                commentContent.className = 'commentContent';

            commentInfo.appendChild(commentContent);

            var commentInteraction = createInteraction(liked, ownId, ownId);
            commentInfo.appendChild(commentInteraction);

            if(replies) //ako postoje odgovori na ovaj komentar
            {
                var repondres = createEl('div', 'replies');                 
                opinion.appendChild(repondres);
                    repondres.setAttribute('data-contentid', ownId);//we attach the comments content_id

                for(var i=0; i<replies.length; i++)
                {
                    var r = replies[i];

                    var rep = makeReply(
                        r['ownId'], 
                        r['tekst'], 
                        r['username'], 
                        r['userImage'],
                        r['liked'],
                        r['content_id']
                    );

                    repondres.appendChild(rep);
                }

                var userReply = createEl('div', 'reply hide');
                repondres.appendChild(userReply);

                    var replyUserImage = createEl('div', 'userImage');
                    userReply.appendChild(replyUserImage);

                        var replyImg = createEl('img');
                        replyUserImage.appendChild(replyImg);

                            replyImg.src = getById('userImage').src;//we add current users image


                    var replyAreaContainer = createEl('div', 'replyAreaContainer');
                    userReply.appendChild(replyAreaContainer);

                        var replyArea = createEl('textarea', 'replyArea');
                        replyAreaContainer.appendChild(replyArea);

                            replyArea.setAttribute('data-contentid', ownId);//here we store the id of the comment 
                            replyArea.addEventListener('keypress', function(e)
                            {
                                if(e.keyCode == 13)
                                {   
                                    sendComment(this, 'reply');
                                }
                            });
            }

            var commentText = document.createElement('div');
            commentText.className = 'commentText';                
            commentText.innerHTML = tekst; //we put in the text of the submitted comment

            commentContent.appendChild(commentText);

            return opinion;
    }

    function addReply(comment_id, tekst, user, image){

        var comment = document.querySelector('.replies[data-contentid="' + comment_id + '"]');


        var opinion = makeReply(comment_id, tekst, user, image);

        comment.insertBefore(opinion, comment.firstChild);
    }


    function addComment(content_id, tekst, user, image, alias, ownId, memberImage)
    {

        var container = query('.comments[data-contentid="' + content_id + '"]');
        var opinion = makeComment(tekst, user, image, ownId, memberImage);

        container.insertBefore(opinion, container.firstChild);
    }


    var sendComment = function(opinion, type, memberImage) {



    //saljemo komentar php skripti za komentiranje preko json-a
        if(opinion.value){
            var tekst = opinion.value;

            var content_id = opinion.getAttribute('data-contentid');
            var alias = opinion.getAttribute('data-alias');

            console.log(opinion);

            var link = "/house_of_faces/comment";
            var form = new FormData();

            form.append('content_id', content_id);
            form.append('content', tekst);
            form.append('type', type);


            ajax(link, form, function(xhr){

                var data = JSON.parse(xhr.responseText);

                var image    = data.userImage;
                var username = data.nickname;
                var comment_id = data.comment_id;

                opinion.value = "";

                if(type=="comment")
                 addComment(content_id, tekst, username, image, alias, comment_id);
                else
                 addReply(content_id, tekst, username, image);                     
            });
        }
    }

    function focusOnText(anchor){

        if(anchor.className == "replyBtn"){

            var id = anchor.getAttribute('data-contentid');
            var select = ".replyArea[data-contentid='" + id + "']";
            
            var area = document.querySelector(select);
            var deda = area.parentNode.parentNode;

            if(deda.className.indexOf('show') == (-1))
            {
                deda.className += " show";
                area.focus();
            }
            else{
               deda.className = deda.className.replace("show", ""); 
               window.focus();
            }
            return false;
        }

        var post_id = anchor.getAttribute('data-postId');

        var opinionArea = document.querySelector('textarea[data-contentid="' + post_id + '"]');
        opinionArea.focus();

        return false;
    };

    ///////////////////////////////////////////////////////
    //dodajemo novi eventListener u eventAssigner objekt //
    ///////////////////////////////////////////////////////
        
    eventAssigner.addListener('commenting', function(){

        var opinions = document.getElementsByClassName('opinionArea');
        var replies  = document.getElementsByClassName('replyArea');
        var replyBtns  = document.getElementsByClassName("replyBtn");


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

    //listener za otkrivanje reply polja
        for(var i=0; i<replyBtns.length; i++){

            replyBtns[i].addEventListener("click", function(e){
                focusOnText(this);            
            });
        }
    });
      