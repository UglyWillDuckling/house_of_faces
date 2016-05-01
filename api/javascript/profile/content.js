



    eventAssigner.addListener('content', function(){

    //adding new content to the database(movies, songs, etc.)
        var addContentBtns = getByClass('addContentBtn');
        var addContainer = getById('addContainer');
        var addBtn = getById('addBtn');
        var addField = getById('add');
        var click = getById('clickDiv');

        for(var i=0; i<addContentBtns.length; i++){

            addContentBtns[i].addEventListener('click', function(){

                addBtn.setAttribute('data-table', this.getAttribute('data-table'));
                addBtn.setAttribute('data-info', this.getAttribute('data-info'));

                addContainer.classList.remove('noneDisplay');
                click.classList.remove('noneDisplay');
            }); 
        }

        addBtn.addEventListener('click', function(){

            var table = this.getAttribute('data-table');
            var info = this.getAttribute('data-info');

            var value = addField.value;

            var form = new FormData();

            form.append('value', value);
            form.append('table', table);


            var link = "http://127.0.0.1/house_of_faces/profile/addContent";

            ajax(link, form, function(xhr){

                var data = JSON.parse(xhr.responseText);

                if(data.id)
                {
                    addField.value = "";
                    addContainer.classList.add('noneDisplay');

                    var selector = '.aboutInput[data-info="' + info + '"]'
                    var selectInfos = document.querySelectorAll(selector);                  

                             
                    for(var i=0; i<selectInfos.length; i++){
                        var option = makeOption(data.id, value, true);

                        selectInfos[i].appendChild(option);
                        selectInfos[i].value=data['id'];
                    }

                    var updateBtn = query('.updateInfoBtn[data-info="' + info + '"]');
                    console.log(updateBtn);


                    var ev = new Event('click');
                    updateBtn.dispatchEvent(ev);
                }
            });

        });

        var closeAdd = getById('closeAdd');

        closeAdd.addEventListener('click', function(){

            addContainer.classList.add('noneDisplay');
            click.classList.add('noneDisplay');
        });
    });

    
    function makeOption(id, name, selected){

        var opt = createEl('option');
            opt.setAttribute('value', id);
            //if(selected) { opt.setAttribute('selected', 'selected'); }
           
            opt.innerHTML = name;

        return opt;
    }

    

