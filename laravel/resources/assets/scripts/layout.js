var Layout = function () {

     // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;
    var isIE11 = false;

    var responsive = true;

    var responsiveHandlers = [];

    var handleInit = function() {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !! navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !! navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !! navigator.userAgent.match(/MSIE 10.0/);
        isIE11 = !! navigator.userAgent.match(/MSIE 11.0/);
        
        if (isIE10) {
            jQuery('html').addClass('ie10'); // detect IE10 version
        }
        if (isIE11) {
            jQuery('html').addClass('ie11'); // detect IE11 version
        }
    }

    var handleIEFixes = function() {
        //fix html5 placeholder attribute for ie7 & ie8
        if (isIE8 || isIE9) { // ie8 & ie9
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            jQuery('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {

                var input = jQuery(this);

                if (input.val() == '' && input.attr("placeholder") != '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function () {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function () {
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    }
	
	var handleDefault = function (){
		$('.header-nav .nav').localScroll(800);
		$("#sectionLogo").animate({height: $(window).height()-62}, 600);
	}
	
	var handleParallax = function (){
		//.parallax(xPosition, speedFactor, outerHeight) options:
		//xPosition - Horizontal position of the element
		//inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
		//outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport
		
		//section logo
		$('#sectionLogo').parallax("50%", 0.1);
		
		//section offer
		$('#offer').parallax("50%", 0.2);
		
		//section team
		$('#team').parallax("50%", 0.1);
	}

    var handleFancybox = function () {
        if (!jQuery.fancybox) {
            return;
        }
        
        jQuery(".fancybox-fast-view").fancybox();

        if (jQuery(".fancybox-button").size() > 0) {            
            jQuery(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });

            $('.fancybox-video').fancybox({
                type: 'iframe'
            });
        }
    }
	
	var handleProject = function (){
		// big img project 
		$("#projectListLarge").on("click", function(e){
			e.preventDefault();
			$("#portfolioProject").find("li.col-md-2").addClass('col-md-3').removeClass('col-md-2');
			$("#portfolioProject").find("li.col-md-4").addClass('col-md-6').removeClass('col-md-4');
		});	
		
		// small img project
		$("#projectListStandard").on("click", function(e){
			e.preventDefault();
			$("#portfolioProject").find("li.col-md-3").addClass('col-md-2').removeClass('col-md-3');
			$("#portfolioProject").find("li.col-md-6").addClass('col-md-4').removeClass('col-md-6');
		});	
	}

    var handleTheme = function () {
    
        var panel = $('.color-panel');
    
        // handle theme colors
        var setColor = function (color) {
            $('#style-color').attr("href", "assets/css/themes/" + color + ".css");
        }

        $('.icon-color', panel).click(function () {
            $('.color-mode').show();
            $('.icon-color-close').show();
        });

        $('.icon-color-close', panel).click(function () {
            $('.color-mode').hide();
            $('.icon-color-close').hide();
        });

        $('li', panel).click(function () {
            var color = $(this).attr("data-style");
            setColor(color);
            $('.inline li', panel).removeClass("current");
            $(this).addClass("current");
        });
    }
	
    return {
        init: function () {
            // init core variables
            handleTheme();
            handleDefault();
            handleParallax();
            handleProject();
            handleFancybox();
            handleInit();
            handleIEFixes();
        }

    };
}();