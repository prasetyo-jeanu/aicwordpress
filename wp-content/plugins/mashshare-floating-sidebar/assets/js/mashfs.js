(function ($) {
    $(document).ready(function () {


        var isMobile = {
            Android: function () {
                return navigator.userAgent.match(/Android/i);
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i);
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i);
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i);
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i);
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
            }
        };


        var mobile_width = typeof mashfs.mobile_width !== "undefined" ? mashfs.mobile_width : 'show_it' ; // max allowed width where side bar is shown
        var mashfs_height = $('#mashfs-main').height();
        var width = $(window).width();
        var height = $(window).height();
        var mashfs_top = (height - mashfs_height) / 2;

        // Show floating side bar
        if (mobile_width === '' ||
                (mobile_width === 'show_it' && isMobile.any()) ||
                (mobile_width === 'show_it' && !isMobile.any()) ||
                (mobile_width !== 'show_it' && !isMobile.any())                
                ) 
        {
            $('#mashfs-main').css('top', mashfs_top).show();
        }


        $('.mashfs-right #mashfs-hidebtn').text('>>');
        $('.mashfs-left #mashfs-hidebtn').text('<<');

        $('#mashfs-hidebtn').click(function (e) {
            e.preventDefault();
            $('#mashfs-main').toggleClass('mashfs-hidden');
            if ($('#mashfs-main').hasClass('mashfs-hidden')) {
                $('.mashfs-right #mashfs-hidebtn').text('<<');
                $('.mashfs-left #mashfs-hidebtn').text('>>');
                $("#mashfs-main").hide("slow");
            } else {
                $('.mashfs-right #mashfs-hidebtn').text('>>');
                $('.mashfs-left #mashfs-hidebtn').text('<<');
            }
        });
        /* Hide Whatsapp button on other devices than iPhones and Androids */
        if (navigator.userAgent.match(/(iPhone)/i) || navigator.userAgent.match(/(Android)/i)) {
            $('.mashicon-whatsapp').show();
            $(".mashicon-whatsapp").css({display: "block"});
        }
    });
})(jQuery);