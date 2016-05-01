


    eventAssigner.addListener('controls', function(){
    
        var controls =  getByClass('controlLink'),
            content = getByClass('content'),
            selected = controls[0];


        var currentContent = content[0];

        for(var i=0; i<controls.length; i++){

            controls[i]['number'] = i;


            controls[i].addEventListener('click', function(){

                selected.classList.remove('clicked');

                this.classList.add('clicked');
                selected = this;


                var link = this.getAttribute('data-link');
                
                var n = this['number'];

                if(content[n] != currentContent){

                    currentContent.className += " noneDisplay";//we remove the content being shown

                    content[n].className = content[n].className.replace('noneDisplay', ''); //we show the selected content

                    currentContent = content[n];    
                }
       
                return 0;
            })
        }
    });