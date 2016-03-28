

    function regenerateCsrf(newTokenValue){

        var csrf = document.getElementById('csrfToken');

        csrf.value = newTokenValue;
    }

    function getCsrf(){
        var csrfToken = document.getElementById('csrfToken').value;

        return csrfToken;
    }