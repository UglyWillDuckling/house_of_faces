 

    window.onload = function(){

        //adding all of the event listeners
        for(var i=0; i<eventAssigner.functions.length; i++){
           eventAssigner.functions[i](); 
        }

        var thumbs = getByClass('userThumb');
        var carrousel = getById('imgCarrousel');
        var currentImage = getById('currentImage');
        var memberImage = getById('userImage').src;//the current users image


        var commentsContainer = getById('pictureComments');
        var area = getById('pictureCommentArea');

        var chevrons = getByClass('arrow');


        for(var i=0; i<thumbs.length; i++){

            thumbs[i].addEventListener('click', function(){

                getPicture(
                    this, 
                    carrousel, 
                    commentsContainer, 
                    area,
                    currentImage,
                    memberImage                                        
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
    };


    function getPicture(thumb, carr, commentContainer, commentArea, image){

        var id = thumb.getAttribute('data-picid');

        var prevThumb = thumb.previousElementSibling;        
        var nextThumb = thumb.nextElementSibling;

        store_pictureId_in_chevron(prevThumb, nextThumb);


        var form = new FormData;
        var link = 'http://127.0.0.1/house_of_faces/profile/getPicture';

        form.append('id', id);

        ajax(link, form, function(xhr){

            var data = JSON.parse(xhr.responseText);
      
            var picture = data['picture'];

            image.src = picture['path'];//we replace the current image with the new one

            var comments = picture['comments'];

            commentContainer.innerHTML = ""; //we delete the former comments
            for(var i=0; i<comments.length; i++){

                var c = comments[i];

                console.log(c);

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
             
            commentArea.setAttribute('data-contentid', picture['content_id']);
            commentContainer.setAttribute('data-contentid', picture['content_id']);
        });
    }


    function store_pictureId_in_chevron(prev, next){

        var chevronLeft = getById('arrowLeft');
        var chevronRight = getById('arrowRight');

        var prevId = prev ? prev.getAttribute('data-picid') : null;//if the attribute exists we assign it
        var nextId = next ? next.getAttribute('data-picid') : null;

//storing the new picture id-s in the left and right arrows
        chevronLeft.setAttribute('data-picid', prevId);
        chevronRight.setAttribute('data-picid', nextId);       
    }
