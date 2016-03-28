
    function checkEmail(email){

        var form = new FormData();
        var link = "http://localhost/house_of_faces/auth/checkEmail";

        form.append('address', email);


        ajax(link, form, function(xhr){

            var reponse = xhr.responseText;

            console.log(reponse);
        });
    }

