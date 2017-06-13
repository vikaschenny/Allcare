;(function($){     
    $.fn.showDrieFrame = function(options){
    
        var settings = $.extend({
            framewith : "80%",
            frameheight : "80%",
            driveimage:"../../patient_file/summary/driveassets/driveimages/drive.png"
        }, options);
        $(this).on("click",function(event){
            event.preventDefault();
            var url = $(this).data('url');
            $(".drieFrameoverlay").remove();
            $('body').append('<div class="drieFrameoverlay">\n\
                                <div class="frameloader"><div class="drive">\n\
                                    <img src="'+settings.driveimage+'" width="58px"/>\n\
                                </div>\n\
                                <div class="circle">\n\
                                </div>\n\
                            </div>\n\
            </div>');
            
            $('.drieFrameoverlay').append("<iframe class='driveframe' src='"+url+"' frameborder='0'></iframe>");
            $(".driveframe").css({width:settings.framewith,height:settings.frameheight});
            $(".drieFrameoverlay").fadeIn("slow");
            $('body').css("overflow","hidden");
            $(".drieFrameoverlay").click($.fn.showDrieFrame.close);
            $(".drieFrameoverlay iframe").load(function(){
                $('.frameloader').remove();
            })
        });
        
        return this;
    }
    $.fn.showDrieFrame.close = function(){
        $('.drieFrameoverlay').fadeOut("fast",function(){
               $(this).remove();$('body').css("overflow","visible");
        });
    }
    
    $.fn.extend({
        treed: function (o) {

          var openedClass = 'glyphicon-minus-sign';
          var closedClass = 'glyphicon-plus-sign';

          if (typeof o != 'undefined'){
            if (typeof o.openedClass != 'undefined'){
            openedClass = o.openedClass;
            }
            if (typeof o.closedClass != 'undefined'){
            closedClass = o.closedClass;
            }
          };

            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            tree.find('li').has("ul").each(function () {
                var branch = $(this); //li with children ul
                branch.prepend("<i class='indicator glyphicon " + closedClass + "'></i>");
                branch.addClass('branch');
                branch.on('click', function (e) {
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().toggle();
                    }
                })
                branch.children().children().toggle();
            });
            //fire event from the dynamically added icon
          tree.find('.branch .indicator').each(function(){
            $(this).on('click', function () {
                $(this).closest('li').click();
            });
          });
            //fire event to open branch if the li contains an anchor instead of text
            tree.find('.branch>a').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
            //fire event to open branch if the li contains a button instead of text
            tree.find('.branch>button').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
        }
    });

    
})(jQuery)