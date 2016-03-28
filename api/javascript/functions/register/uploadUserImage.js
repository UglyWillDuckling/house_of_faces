

     function uploadUserImage(files, image) {

        var formular = new FormData();

        formular.append('image', files[0]);


        var link = "http://localhost/house_of_faces/uploadRegistrationImage";

        ajax(link, formular, function(xhr){

            var reponse = JSON.parse(xhr.responseText);

            var source;
            if(reponse['src'])
            {
                image.src=reponse['src'];
            }
            else{
                console.log( reponse['error']);
            }        
        });
    }