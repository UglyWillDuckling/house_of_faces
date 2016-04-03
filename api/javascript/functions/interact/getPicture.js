
    function getPicture(id, back){

        var form = new FormData;
        var link = 'http://127.0.0.1/house_of_faces/profile/getPicture';

        form.append('id', id);
        
        ajax(link, form, back);
    }

