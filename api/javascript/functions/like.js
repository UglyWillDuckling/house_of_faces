
        function meLikey(likeButton){

            var content_id = likeButton.getAttribute('data-contentId');

            if(content_id)
            {
                var myRequest = new XMLHttpRequest();

                //notify user if this doesnt work
                var form = new FormData();

                form.append('content_id', content_id);

                var link = "/house_of_faces/like";
                myRequest.open('POST', link);


                ajax(link, form, function(xhr){

                    //ako je sve proslo u redu promjeni klasu like buttona 
                        var data = JSON.parse(xhr.responseText);
                        console.log(data);
                        if(data['ok']){
                            //change the button class
                            var klasa = likeButton.className;   

                        //kad indexOf() ne nade dani mu string vraca -1
                            if( klasa.indexOf('liked') !== -1 ){
       
                                likeButton.className = klasa.replace('liked', 'noLike');    
                            } 
                            else {
                                likeButton.className = klasa.replace('noLike', 'liked');
                            }
                        }
                        else {
                            //display message, something is wrong with db
                            console.log('db malfunction.');
                        }
                });                 
            }            
        }