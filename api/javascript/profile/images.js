
    function showImage(thumb, commentContainer, commentArea, image)
    {

        var id = thumb.getAttribute('data-picid');

        var prevThumb = thumb.previousElementSibling;        
        var nextThumb = thumb.nextElementSibling;

        store_pictureId_in_chevron(prevThumb, nextThumb);//we change the ids in the left and right arrows

        var pic = getPicture(id, function(xhr){

            var data = JSON.parse(xhr.responseText);
      
            var picture = data['picture'];

            image.src    = picture['path'];//we replace the current image with the new one
            var comments = picture['comments'];


            commentContainer.innerHTML = ""; //we delete the former comments
            for(var i=0; i<comments.length; i++){

                var c = comments[i];
                var komentar = makeComment(
                    c['tekst'], 
                    c['username'], 
                    c['userImage'],
                    c['ownId'],
                    
                    c['liked'],  
                    c['replies']                
                );
                
                commentContainer.insertBefore(komentar, commentContainer.firstChild);        
            }

            changeLikeBtn(
                eventAssigner.currentLikeBtn, 
                picture['liked'], 
                picture['content_id'],
                'likeBtn noneDisplay'
            );
            
        //changing the ids to the id of the current image
            commentArea.setAttribute('data-contentid', picture['content_id']);
            commentContainer.setAttribute('data-contentid', picture['content_id']);

            removeClass(eventAssigner.carrousel, 'noneDisplay');//in the end we finally show the image
        });
    }


    /**
     * the function takes the ids from the given former and latter elements and assigns them 
     *                                                             to the left and right arrrows
     * @param  {[node]} prev previous thumb 
     * @param  {[node]} next next thumb
     * @return {[void]}     
     */
    function store_pictureId_in_chevron(prev, next){

        var chevronLeft = getById('arrowLeft');
        var chevronRight = getById('arrowRight');

        var prevId = prev ? prev.getAttribute('data-picid') : null;//if the attribute exists we assign it
        var nextId = next ? next.getAttribute('data-picid') : null;

//storing the new picture id-s in the left and right arrows
        chevronLeft.setAttribute('data-picid', prevId);
        chevronRight.setAttribute('data-picid', nextId);       
    }

    /**
     * change the class and the contentid attribute of the like button 
     *                                        based on the liked parameter passed to it
     * @param  {[node]} btn        the like button
     * @param  {[bool]} liked      [description]
     * @param  {[string]} id         [description]
     * @param  {[string]} basicClass [description]
     * @return {[type]}            [description]
     */
    function changeLikeBtn(btn, liked, id, basicClass){

        btn.className = basicClass;

        var likeClass = liked ? ' liked' : ' noLike';

        btn.className += likeClass;

        btn.setAttribute('data-contentid', id);      
    }



    ////////////////////
    //Event assigning //
    ////////////////////

    eventAssigner.addListener('images', function(){

        var mod = eventAssigner;

        var thumbs = getByClass('userThumb');
        var carrousel = getById('imgCarrousel');
        var currentImage = getById('currentImage');
        var memberImage = getById('userImage').src;//the current users image

        //eventAssigner.currentLikeBtn = getById('currentImgLikeBtn');


        var commentsContainer = getById('pictureComments');
        var area = getById('pictureCommentArea');

        var chevrons = getByClass('arrow');


        for(var i=0; i<thumbs.length; i++){

            thumbs[i].addEventListener('click', function(){

                showImage(
                    this,  
                    commentsContainer, 
                    area,
                    currentImage                                        
                );

                removeClass(carrousel, 'noneDisplay');
            });
        }

        
       
        for(var i=0; i <chevrons.length; i++){

            chevrons[i].addEventListener('click', function()
            {
                var pictureId = this.getAttribute('data-picid');
                if(pictureId){//ako chevron se sadrzi id, znaci da je ovo prva ili posljednja slika

                    var selector = '.userThumb[data-picid="' + pictureId + '"]';

                    var thumb = document.querySelector(selector);
                    if(thumb) { thumb.click(); }
                }   
            });
        }


        var closeCarr = getById('closeCarrousel');

        closeCarr.addEventListener('click', function(){

            carrousel.className += " noneDisplay";
        });


    /******ADD NEW IMAGE ******/

        var addButton = getById('addImageBtn');
        var fileBtn = getById('newImageFile');


        addButton.addEventListener('click', function(){
            fileBtn.click();
        }); 

        fileBtn.addEventListener('change', uploadImage);


    /*****DELETE IMAGE*****/
    
        var deleteBtns = getByClass('deleteBtn');

        for(var i=0; i<deleteBtns.length; i++){
            deleteBtns[i].addEventListener('click', deleteImage);
        }         
    });


    var deleteImage = function (e) 
    {
        var link = 'http://127.0.0.1/house_of_faces/profile/deleteImage';
        var form = new FormData;

        var id = e.target.getAttribute('data-picid');

        console.log(id);

        form.append('id', id);

        ajax(link, form, removeImage(id));    
    }


    function removeImage(id){

        var thumb = document.querySelector('.userThumb[data-picid="' + id + '"]');


        thumb.remove();
    }