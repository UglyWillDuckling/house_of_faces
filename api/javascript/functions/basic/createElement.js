

    function createEl(el, klasa)
    {
        if(!klasa)
            return document.createElement(el);
        else{
            var node = document.createElement(el);
            node.className = klasa;
            return node;
        }
    }
