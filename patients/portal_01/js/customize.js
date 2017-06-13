/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
     // highlighted which you are slected
    $('#menu .menugroup ul li a').each(function(index,element){
        var currentparms = (window.location.href).split("?")[1];
        if((element.getAttribute("href").split("?")[1]) == currentparms){
            element.className = "selected";
            $(element).parents('.topmenu').addClass('selected');
            $('#menu .home').removeClass("selected");
            return false;
        }else{
            $('#menu .home').addClass("selected");
        }
    })
})


