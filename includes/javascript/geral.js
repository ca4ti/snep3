/* 
    Document   : geral.js
    Created on : Apr 03, 2012, 03:32:25 PM
    Author     : Iago Uilian Berndt, Rodrigo Cavichioli
    Description: General scripts
*/

var loc = window.location;
var pathName = loc.pathname.substring(0, loc.pathname.indexOf('index.php'));


/*services footer*/

var servicesState = 0;

function servicesSlider(){
   
    var right = jQuery('#statusbar');
    var opened = jQuery(window).width() - jQuery(right).width() + 20;
    var closed = jQuery(window).width() - 50;
    console.log(opened);
    console.log(closed);
    if (servicesState == 1){
        servicesRefresh();
        right.stop().animate({'left': opened}, 400);
    }else{
        servicesRefresh();
        right.stop().animate({'left': closed}, 400);
    }
}

function servicesRefresh(){
    var get = jQuery.get(pathName+'index.php/systemstatus/', function(data) {
        jQuery('#statusbar_content').html(data);
    });
}

function servicesReposition(){    
    var left = jQuery(window).width() - 50;
    var statusbar = jQuery('#statusbar');
    statusbar.css({'left': left});
}

jQuery(document).ready(function(){
    jQuery('#statusbar').click(function(){

        if (servicesState == 0){
            servicesState = 1;
        }else{
            servicesState = 0;
        }
        servicesSlider();
    });
    jQuery("#statusbar_content").mouseenter(function(){
        jQuery(this).stop().fadeTo(400, 1);
    }).mouseleave(function(){
        jQuery(this).stop().fadeTo(400, 0.8);
    }).fadeTo(250, 0.8);
});

jQuery(document).ready(servicesRefresh);
jQuery(document).ready(servicesReposition);
jQuery(window).resize(servicesReposition);

/*end of services footer*/


// var imgtrue = pathName+"images/true.png";
// var imgfalse = pathName+"images/false.png";

jQuery(document).ready(function(e) {
    /* tolabel*/
    jQuery('.tolabel').each(function(index, element) {
        var line = jQuery(this).parents(".line").eq(0).addClass('linetolabel');
        jQuery(".input", line).addClass('tolabel').prependTo(jQuery("label", line).parent());
        jQuery('p', line).css({'display':'block', 'width': '100%', 'float': 'left'}).appendTo(jQuery("label", line).parent());
    });
    /* lineleft*/
    jQuery('.lineleft').each(function(index, element) {
        jQuery(this).removeClass('lineleft');
        jQuery(this).parents(".line").eq(0).addClass('lineleft');
    });
    /*new checkbox*/
    jQuery('input[type=checkbox].newcheck').each(function(index, element) {
        var name = jQuery(this).attr('name').replace(/[^a-zA-Z 0-9]+/g,'');
        //jQuery(this).css('opacity', 0);
        jQuery(this).before('<a href="javascript:void(0)" class="check_a" id="check_a_'+ name +'"><img class="checkbox_" id="check_img_'+ name +'"/></a>');
        var div = jQuery('#check_img_'+name, jQuery(this).parent());
        var a = jQuery('#check_a_'+name, jQuery(this).parent());
        
        if(jQuery(this).attr('checked')) div.attr('src', imgtrue);
        else div.attr('src', imgfalse);
        var check = jQuery(this);
        var change = function(){
            if(check.attr('checked')){
                div.attr('src', imgfalse);
                check.attr('checked', false);
            }else{
                div.attr('src', imgtrue);
                check.attr('checked', true);
            }
        };
        a.click(change);
        a.keypress(change);
        check.change(function(){
            if(check.attr('checked'))div.attr('src', imgtrue);
            else div.attr('src', imgfalse);
        });
    });
    if(jQuery().multiselect){
        jQuery(".multiselect").css({'width': 710, 'height': 200}).multiselect({sortable: false, searchable: true});
        jQuery(".bigMultiselect").css({'width': 710, 'height': 400}).multiselect({sortable: false, searchable: true}).addClass('multiselect');
    }
    
    //Select limit page
    jQuery('.barTop .html form #campo').change(function(){jQuery('.barTop .html form #submit').click();});
});

function changeNewCheck(check, value){
    
    if(value && check.attr('src') == imgfalse) check.click();
    else if(!value && check.attr('src') == imgtrue) check.click();
}

//Masks
/*
jQuery(document).ready(function(){
    jQuery('.maskCode').setMask('maskCode');
    jQuery('.maskDate').setMask('maskDate');
    jQuery('.maskTime').setMask('maskTime');
}); 

//Keyfilters
jQuery(document).ready(function(){
    jQuery('.maskCurrency').keyfilter(/[\d\.]/);
    jQuery('.maskPhone').keyfilter(/[\d]/);
    jQuery('.maskMinutes').keyfilter(/[\d]/);
    jQuery('.maskInt').keyfilter(/[\d]/);
    jQuery('.maskRange').keyfilter(/[\d\;\-]/);
});
*/
//subform
function subForm(select, values){
    
    var elements = jQuery(".subform");
    select = jQuery("#"+select);
    var actual = select.val();
    for(var i in values)if(values[i] != null)if(values[i] != actual)elements.eq(i).css('display', 'none');
    select.change(function(){
        var ia = 0, ip = 0;
        for(var i in values)if(values[i] != null){
            if(values[i] == select.val()) ip = i; 
            else if(values[i] == actual) ia = i;
        }
        elements.eq(ia).slideUp(400, function(){elements.eq(ip).slideDown(600);});
        actual = select.val();
    });
}

//subtitle form
function subTitle(select, icon){
    select.addClass("subtitle");
    select.prepend("<img src='"+icon+"'/>");
}

//dashboard confirm dialog
function dashboardConfirm(link){
    
    background = jQuery('<div/>', {'class' : 'dashTint'});
    box        = jQuery('<div/>', {'class' : 'dashBox'}).appendTo(background);
    title      = jQuery('<div/>', {'class' : 'dashTitle'}).html('Dashboard').appendTo(box);
    text       = jQuery('<div/>', {'class' : 'dashText'}).html('<span>Deseja adicionar o item à Dashboard?</span>').appendTo(box);
    submit     = jQuery('<div/>', {'class' : 'dashSubmit'}).appendTo(box);  
    
    jQuery('<a/>', {
      'href'  : link,
      'class' : 'dashSend'
    }).html('Enviar').appendTo(submit);
    
    jQuery('<a/>', {
      'href'  : '#',
      'class' : 'dashCancel'
    }).html('Cancelar').appendTo(submit);
    
    background.fadeIn(500).appendTo(jQuery('body'));
    
    jQuery('.dashCancel').click(function(){
       jQuery('.dashTint').fadeOut(500, function(){
           jQuery(this).remove();
       });
    });
}

function dashboardForm(link){
   
    background = jQuery('<form/>', {'class' : 'dashTint', 'action': link, 'method':'post'});
    box        = jQuery('<div/>', {'class' : 'dashBox'}).appendTo(background);
                 jQuery('<div/>', {'class' : 'dashTitle'}).html('Adicionar filtro à Dashboard').appendTo(box);
                 jQuery('<div/>', {'class' : 'dashText'}).html('<span>Nome:</span>').appendTo(box).append(jQuery("<input/>").attr({"type": "text", "maxlength": 15, "name":"nome"}));
                 jQuery('<div/>', {'class' : 'dashText'}).html('<span>Descrição:</span>').appendTo(box).append(jQuery("<input/>").attr({"type": "text", "maxlength": 40, "name": "descricao"}));
    submit     = jQuery('<div/>', {'class' : 'dashSubmit'}).appendTo(box);  
    
    jQuery('<a/>', {
      'href'  : '#',
      'class' : 'dashSend'
    }).html('Enviar').click(function(){background.submit();}).appendTo(submit);
    
    jQuery('<a/>', {
      'href'  : '#',
      'class' : 'dashCancel'
    }).html('Cancelar').appendTo(submit);
    
    background.fadeIn(500).appendTo(jQuery('body'));
    
    jQuery('.dashCancel').click(function(){
       jQuery('.dashTint').fadeOut(500, function(){
           jQuery(this).remove();
       });
    });
}

jQuery(document).ready(function(){
   
    jQuery('.option_dashboard').each(function(){
        var link = jQuery(this).attr('href');
        
        jQuery(this).click(function(){
            dashboardConfirm(link);
        });
        jQuery(this).attr('href', '#');
    });
    jQuery('.option_dashboardform').each(function(){
        var link = jQuery(this).attr('href');
        
        jQuery(this).click(function(){
            dashboardForm(link);
        });
        jQuery(this).attr('href', '#');
    });
});
//end of dashboard confirm dialog


// Help button
/*
jQuery(document).ready(function(){
    $(".inline").colorbox({inline:true, width:"70%"});
}); */
// end Help Button

//put cursor on first input on each screen
jQuery(document).ready(function(){
    jQuery('input[type=text]').eq(0).not(".snep-datetimepicker").focus();

    jQuery('#content td .alterar').attr('title', "Alterar");
    jQuery('#content td .configurar').attr('title', "Configurar");
    jQuery('#content td .excluir').attr('title', "Excluir");
    jQuery('#content td .listar').attr('title', "Listar");
    jQuery('#content td .duplicar').attr('title', "Duplicar");
    jQuery('#content td .membros').attr('title', "Membros");
    jQuery('#content td .download').attr('title', "Download");
    jQuery('#content td .permissao').attr('title', "Permissão");
    jQuery('#content td .vinculos').attr('title', "Vínculos");
    
    
});

jQuery(window).load(function(){jQuery("#preload").fadeOut(500);});
jQuery(window).unload(function(){jQuery("#preload").fadeIn(500);});
jQuery(window).submit(function(){jQuery("#preload").fadeIn(500);});

