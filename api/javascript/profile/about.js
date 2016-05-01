
    eventAssigner.addListener('about', function(){

       var aboutBtns = getByClass('aboutBtn');
        var currentInfo = getById('mainInfo');//the user information currently being shown
        var clickedBtn = aboutBtns[0];

         for(var i=0; i<aboutBtns.length; i++){

           aboutBtns[i].addEventListener('click', function(e){
                e.preventDefault();
     
                if(clickedBtn != this){

                    clickedBtn.classList.remove('clicked');

                    this.classList.add('clicked');
                    clickedBtn = this;
                }

                var data = this.getAttribute('data-content');
               
                var info = query('.info[data-content="' + data + '"]');


               currentInfo.classList.add('noneDisplay');
               info.classList.remove('noneDisplay');

               currentInfo = info;
           });
        }


        var updateBtns = getByClass('updateInfoBtn');
        var updateFields = getByClass('aboutInput');

        for(var i=0; i<updateBtns.length; i++){

            updateBtns[i].addEventListener('click', updateInfo);
        }

        for(var i=0; i<updateFields.length; i++){

            updateFields[i].addEventListener('change', function(){

                var data = this.getAttribute('data-info');
                var buttons = document.querySelectorAll('.updateInfoBtn[data-info="' + data + '"]');
                
                for(var j=0; j<buttons.length; j++){
                    var button = buttons[j];
                    if(!button.classList.contains('changed')) button.classList.add('changed');
                }

                var fields = document.querySelectorAll('.aboutInput[data-info="' + data + '"]');

                for(var j=0; j<fields.length; j++){

                    fields[j].value = this.value;
                }


            });
        }  
    });

    function updateInfo(e){

        var btn = this;
        var data = btn.getAttribute('data-info');

        var field;
        if(this.previousElementSibling.nodeName == "BUTTON")
            field = this.previousElementSibling.previousElementSibling;
        else
            field = this.previousElementSibling;

        var val = field.value;


    //ajax    
        var form = new FormData;

        form.append('value', val);
        form.append('table', data);

        var link = "http://127.0.0.1/house_of_faces/profile/update";

        ajax(link, form, function(xhr){

           var response = JSON.parse(xhr.responseText);
           
            if(response.ok)
            {
                var btns = document.querySelectorAll('.updateInfoBtn[data-info="' + data + '"]');

            //we remove the changed class from all the appropriate buttons    
                for(var j=0; j<btns.length; j++) btns[j].classList.remove('changed');


                var fields = document.querySelectorAll('.aboutInput[data-info="' + data + '"]');

                for(var i=0; i<fields.length; i++){
                   fields[i].value = val;             
                }
           }
        });
    }

   