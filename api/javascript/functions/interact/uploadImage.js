 var uploadImage = function(e){

        var picture = this.files[0];    

        var form = new FormData;
        var link = 'http://127.0.0.1/house_of_faces/profile/addImage';

        form.append('picture', picture);


        ajax(link, form, function(xhr){

            var data = JSON.parse(xhr.responseText);

            if(data['url'] && data['id']) //show the returned thumbnail if the request was successful
            {               
               var t = makeThumb(data['url'], data['id']);

               eventAssigner.thumbs.appendChild(t);
            }
        });
    }