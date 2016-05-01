

    
    eventAssigner.addListener('photo', function()
    {

        var changeBtn = getById('chooseUserPhotoBtn');
        var changePhotoBtn = getById('changePhotoBtn');
        var closeBtn = getById('closeChangePhoto');
        var Container = getById('changePhotoContainer');

        var changeThumbs = getByClass('changeThumb');


        var selected;

        changePhotoBtn.addEventListener('click', function(){
            Container.classList.remove('noneDisplay');
        });

        for(var i=0; i<changeThumbs.length; i++){

            changeThumbs[i].addEventListener('click', function()
            {
                if(!selected){
                    changeBtn.classList.add('chosen');
                }
                else{
                    selected.classList.remove('selected');
                }
                
                this.classList.add('selected');

                selected = this;              
            });
        }

        changeBtn.addEventListener('click', function(){

            if(selected){
                changeUserImage(selected);
            }
        });

         closeBtn.addEventListener('click', function(){
            Container.classList.add('noneDisplay');
        });
    });

    function changeUserImage(image){

        var id = image.getAttribute('data-picid');

       

        var link = eventAssigner.baseUrl + "/profile/changeProfilePicture";

        var form = new FormData;

        form.append('id', id);

        ajax(link, form, function(xhr){

            var response = JSON.parse(xhr.responseText);
            
            if(response['path']){

                var userImage = getById("profilePhoto");

                userImage.src = eventAssigner.baseUrl + "/" + response['path'];
            }
        });
    }


