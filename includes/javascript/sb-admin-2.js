jQuery.noConflict();
jQuery(function() {

    jQuery('#side-menu').metisMenu();

});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
jQuery(function() {
    jQuery(window).bind("load resize", function() {
        topOffset = 0;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        
        if (width < 768) {
            jQuery('div.navbar-collapse').addClass('collapse');
            topOffset = 0; // 2-row-menu
        } else {
            jQuery('div.navbar-collapse').removeClass('collapse');
        }
        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            jQuery("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = jQuery('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('').parent().parent().addClass('in').parent();

    if (element.is('li')) {
        element.addClass('active');
    }
});
