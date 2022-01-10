jQuery(".faq-element").click(function() {
    if (jQuery(this).hasClass('active')) {
        jQuery(this).removeClass('active')
    } else {
        jQuery(".faq-element").removeClass('active')
        jQuery(this).addClass('active')
    }
});

jQuery(window).scroll(function() {
    if (jQuery(window).scrollTop() >= 100) {
        jQuery('#top-head').addClass('fixed');
    } else {
        jQuery('#top-head').removeClass('fixed');
    }
});

jQuery(function() {
    var current = location.pathname;
    jQuery('.top-menu li a').each(function() {
        var $this = jQuery(this);
        // if the current path is like this link, make it active
        if ($this.attr('href').indexOf(current) !== -1) {
            $this.addClass('active');
        }
    })
})

jQuery(document).ready(function() {

    var sectionIds = jQuery('.top-menu a');

    jQuery(document).scroll(function() {
        sectionIds.each(function() {

            var container = jQuery(this).attr('href');
            var containerOffset = jQuery(container).offset().top;
            var containerHeight = jQuery(container).outerHeight();
            var containerBottom = containerOffset + containerHeight;
            var scrollPosition = jQuery(document).scrollTop();

            if (scrollPosition < containerBottom - 20 && scrollPosition >= containerOffset - 20) {
                jQuery(this).addClass('active');
            } else {
                jQuery(this).removeClass('active');
            }


        });
    });



});

jQuery(document).ready(function() {

    var sectionIds = jQuery('.pop-menu a');

    jQuery(document).scroll(function() {
        sectionIds.each(function() {

            var container = jQuery(this).attr('href');
            var containerOffset = jQuery(container).offset().top;
            var containerHeight = jQuery(container).outerHeight();
            var containerBottom = containerOffset + containerHeight;
            var scrollPosition = jQuery(document).scrollTop();

            if (scrollPosition < containerBottom - 20 && scrollPosition >= containerOffset - 20) {
                jQuery(this).addClass('active');
            } else {
                jQuery(this).removeClass('active');
            }


        });
    });



});

jQuery(".hamburger").click(function() {
    if (jQuery(this).hasClass('active')) {
        jQuery(this).removeClass('active');
        jQuery(".mobile-pop").removeClass('active');
        jQuery("body").removeClass('active-ham');
    } else {
        jQuery(this).addClass('active');
        jQuery(".mobile-pop").addClass('active');
        jQuery("body").addClass('active-ham');
    }
});

jQuery("ul.pop-menu li a").click(function() {
    jQuery(".hamburger").removeClass('active');
    jQuery(".mobile-pop").removeClass('active');
    jQuery("body").removeClass('active-ham');
});

var vrr = false;

jQuery.fn.isOnScreen = function() {

    if (vrr === true) {
        return false;
    } else {

        var win = jQuery(window);


        var viewport = {
            top: win.scrollTop(),
            left: win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();

        var bounds = this.offset();
        if (typeof bounds === 'undefined') {

        } else {
            bounds.right = bounds.left + this.outerWidth();
            bounds.bottom = bounds.top + this.outerHeight();

            return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
        }

        return false;
    }
};

// jQuery(document).ready(function() {
//     if (!jQuery('.animate').hasClass('right-away')) {
//         jQuery(window).scroll(function() {
//             var el = jQuery('.animate');
//             if (el.isOnScreen()) {
//                 // The element is visible, do something
//                 el.addClass('animated');
//                 vrr = true;
//                 el.each(function() {
//                     // var $this = jQuery(this);
//                     // $this.addClass('animated');
//                     // jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
//                     //     duration: 2000,
//                     //     easing: 'swing',
//                     //     step: function() {
//                     //         $this.text(Math.ceil(this.Counter));
//                     //     }
//                     // });
//                 });
//             } else {
//                 // The element is NOT visible, do something else
//             }
//         });
//     } else {
//         // jQuery('.count').each(function() {
//         //     var $this = jQuery(this);
//         //     jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
//         //         duration: 2000,
//         //         easing: 'swing',
//         //         step: function() {
//         //             $this.text(Math.ceil(this.Counter));
//         //         }
//         //     });
//         // });
//     }

// });

jQuery(window).scroll(function () {
    jQuery('.animate').each(function(i, el){
 
       if (jQuery(this).isOnScreen()) {
           jQuery(this).addClass('animated');
        //    console.log('content block is in viewport.', jQuery(this))
       } 
    })
 
 });

 jQuery('.module-video-popup .popup-content .bg-curtain').click(function(){
    if(jQuery('.module-video-popup').hasClass('active')){
        jQuery('.module-video-popup').removeClass('active');
        stopVideo();
    }
 });

 jQuery('.module-video-popup .popup-content .close-container').click(function(){
    if(jQuery('.module-video-popup').hasClass('active')){
        jQuery('.module-video-popup').removeClass('active');
        stopVideo();
    }
 });
 
 jQuery('#youtube-popup').click(function(){
    if(!jQuery('.module-video-popup').hasClass('active')){
        jQuery('.module-video-popup').addClass('active');
        startVideo();
    }
 });

