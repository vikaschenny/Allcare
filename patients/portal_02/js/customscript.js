// page init
jQuery(function(){
    $('#menulist li a').each(function(index,element){
            var currentparms = (window.location.href).split("?")[1];
            var elementhref = element.getAttribute("href").split("?")[1];
            if((elementhref == currentparms) && (elementhref !='page=home') && (elementhref !=undefined)){
                element.parentNode.className = "active";
                $(element).parents('li').addClass('open');
                $("li[id='home']").removeClass("active open");  
                return false;
            }else{
                $("li[id='home']").addClass("active");  
                $("li[id='home']").addClass("open");
            }
        });
	initAccordion();
	$('#dashboardBlk').css('display','');
	$("#menulist li a").click(function() {
            $("#menulist li.active").removeClass("active");
            $(this).parent("li").addClass('active');
        });
        if($(window).width()<=768)
          $('.container-content').prepend($('.container-content #sidebar'));
        $(window).resize(function(){
            if($(window).width()<=768)
              $('.container-content').prepend($('.container-content #sidebar'))
            else
              $('.container-content').append($('.container-content #sidebar'));
        })
});
// accordion init
function initAccordion() {
	jQuery('ul.add-nav').each(function() {
		var nav = jQuery(this);
		var btnExpand = nav.prev('.expand').find('a');
		var expandActiveClass = 'active';
		var activeClass = 'open';
		var selSlide = '>div.block';
		var items = nav.find(':has(' + selSlide + ')');
		var slides = items.find(selSlide);
		var animSpeed = 300;
		var animated = false;
		
		nav.slideAccordion({
			activeClass:activeClass,
			opener:'>a.opener',
			slider:selSlide,
			collapsible:true,
			animSpeed: animSpeed
		});
		
		btnExpand.click(function() {
			if(!animated) {
				animated = true;
				if(btnExpand.hasClass(expandActiveClass)) {
					btnExpand.removeClass(expandActiveClass);
					slides.slideUp({
						duration: animSpeed,
						complete: function() {
							animated = false;
							items.removeClass(activeClass);
						}
					});
				}
				else {
					btnExpand.addClass(expandActiveClass);
					items.addClass(activeClass);
					slides.slideDown({
						duration: animSpeed,
						complete: function() {
							animated = false;
						}
					});
				}
			}
			return false;
		});
	});
}


;(function($){
	$.fn.slideAccordion = function(o){
		// default options
		var options = $.extend({
			addClassBeforeAnimation: false,
			activeClass:'active',
			opener:'.opener',
			slider:'.slide',
			animSpeed: 300,
			collapsible:true,
			event:'click'
		},o);

		return this.each(function(){
			// options
			var accordion = $(this);
			var items = accordion.find(':has('+options.slider+')');

			items.each(function(){
				var item = $(this);
				var opener = item.find(options.opener);
				var slider = item.find(options.slider);
				opener.bind(options.event, function(){
					if(!slider.is(':animated')) {
						if(item.hasClass(options.activeClass)) {
							if(options.collapsible) {
								slider.slideUp(options.animSpeed, function(){
									item.removeClass(options.activeClass);
								});
							}
						} else {
							var _levelItems = item.siblings('.'+options.activeClass);
							item.addClass(options.activeClass);
							slider.slideDown(options.animSpeed);
						
							// collapse others
							_levelItems.find(options.slider).slideUp(options.animSpeed, function(){
								_levelItems.removeClass(options.activeClass);
							})
						}
					}
					return false;
				});
				if(item.hasClass(options.activeClass)) slider.show(); else slider.hide();
			});
		});
	}
}(jQuery));