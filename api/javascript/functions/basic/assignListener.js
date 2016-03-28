   

    var eventAssigner = {

        functions : [],

        addListener : function(listenerName, funk){          
            this.functions[this.functions.length] = funk;
        }    
    };
