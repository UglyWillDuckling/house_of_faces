

    function checkUsername(username){

        var form = new FormData;
        form.append('username', username);

        var url = "http://localhost/house_of_faces/auth/checkUsername";
        ajax(url, form, function(xhr){ 


        });
    }

    
    function checkEmail(email){

        var form = new FormData();
        var link = "http://localhost/house_of_faces/auth/checkEmail";

        form.append('address', email);


        ajax(link, form, function(xhr){

            var reponse = xhr.responseText;

            console.log(reponse);
        });
    }

