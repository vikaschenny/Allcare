/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function activemenu(target,contentid,title,file){
    $('#menu div').removeClass('selected');
    $('#menu div a').removeClass('selected');
    //$(target).addClass('selected');
    //$(target).parents('.topmenu').addClass('selected');
    $('#menu div a[name='+contentid+']').addClass('selected');
    $('#menu div a[name='+contentid+']').parents('.topmenu').addClass('selected');
    $('#bodycontent > div').hide();
    $('#'+contentid).show();
    $('.title h1').html(title);
    if(file != undefined)
    {
        $('#'+contentid).load(file);
    }
    return false;
}
$(function(){
    $('#bodycontent > div').hide();
    $('#bodycontent #home').show();
     // highlighted which you are slected
    /*$('#menu .menugroup ul li a').each(function(index,element){
        var currentparms = (window.location.href).split("?")[1];
        if((element.getAttribute("href").split("?")[1]) == currentparms){
            element.className = "selected";
            $(element).parents('.topmenu').addClass('selected');
            $('#menu .home').removeClass("selected");
            return false;
        }else{
            $('#menu .home').addClass("selected");
        }
    })*/
})


