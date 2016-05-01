   

    var eventAssigner = {

        functions : [],

        addListener : function(listenerName, funk){          
            this.functions[this.functions.length] = funk;
        },

        baseUrl: "http://127.0.0.1/house_of_faces"
    };
