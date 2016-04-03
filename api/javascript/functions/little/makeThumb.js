   
   function makeThumb(source, id){

        var thumb = createEl('div', 'userThumb');
            thumb.setAttribute('data-picid', id);

            thumb.addEventListener('click', function(event){
                var e = eventAssigner;

                showImage(
                    this, 
                    e.commentContainer, 
                    e.commentArea,
                    e.currentImage
                );
            });
            thumb.style.marginRight = "3px";


            var image = createEl('img');
                image.src = source;

            thumb.appendChild(image);

        return thumb;
    }
