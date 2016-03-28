   function removeClass(elemenat, klasa){

        var c = elemenat.className;

        elemenat.className = c.replace(klasa, '');

        return c;
    }
