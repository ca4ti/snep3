var maskZipcode = Class.create({

    initialize: function(obj) {
        this.obj = obj;
        $(this.obj).observe('keyup', this.format.bind(this));
        $(this.obj).observe('blur', this.finish.bind(this));
    },
    format: function() {
        var length = $F(this.obj).length;

        if(length == 2) {
            $(this.obj).value = $F(this.obj) + '.'
        }

        if(length == 6) {
            $(this.obj).value = $F(this.obj) + '-'
        } 
    },
    finish: function() {
        dot = $F(this.obj).substr( $F(this.obj).length-1, $F(this.obj).length );
        if(dot == '.') {
            $(this.obj).value = $F(this.obj).substr(0, $F(this.obj).length-1 );
        }
    }
});