 

    window.onload = function(){
        var e = eventAssigner;

        e.currentLikeBtn = getById('currentImgLikeBtn'); //like button for the image being shown          
        e.thumbsContainer = getById('thumbs');

        e.thumbs = getByClass('userThumb'); //thumbs for all of this users pictures
        e.carrousel = getById('imgCarrousel');
        
        e.commentContainer = getById('pictureComments');
        e.commentArea = getById('pictureCommentArea');

        e.currentImage = getById('currentImage'); //the picture being currently shown
        e.memberImage = getById('userImage').src; //the current users image


        for(var i=0; i<eventAssigner.functions.length; i++)
        {
           eventAssigner.functions[i](); 
        }
    };
