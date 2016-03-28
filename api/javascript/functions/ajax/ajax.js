   
    function ajax(link, form, callB){
     
        var xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function(){

            if(xhttp.readyState == 4 && xhttp.status == 200)
            {
                if(JSON.parse(xhttp.responseText)['csrfToken'])
                {
                 regenerateCsrf(JSON.parse(xhttp.responseText)['csrfToken']);                 
                }

                callB(xhttp);  
            }
        };

        xhttp.open("post", link);

        form.append('csrfToken', getCsrf());//we append the csrf token to the form
        xhttp.send(form);             
    }