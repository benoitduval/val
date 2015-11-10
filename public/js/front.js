if ($.cookie("theme_csspath")) {
    $('link#theme-stylesheet').attr("href", $.cookie("theme_csspath"));
}

$(function () {

    animations();
    sliders();
    fullScreenContainer();
    utils();
    sliding();
    map();
    counters();
    parallax();
    slider();
    form();
    adminDate();
    modal();
});

$(window).load(function () {
    windowWidth = $(window).width();
    $(this).alignElementsSameHeight();

    masonry();

});
$(window).resize(function () {

    newWindowWidth = $(window).width();

    if (windowWidth !== newWindowWidth) {
    setTimeout(function () {
        $(this).alignElementsSameHeight();
        fullScreenContainer();
        waypointsRefresh();
    }, 205);
    windowWidth = newWindowWidth;
    }

});

/* =========================================
 *  animations
 *  =======================================*/

function animations() {
    if (Modernizr.csstransitions) {
        delayTime = 0;
        $('[data-animate]').css({opacity: '0'});
        $('[data-animate]').waypoint(function (direction) {
            delayTime += 150;
            $(this).delay(delayTime).queue(function (next) {
            $(this).toggleClass('animated');
            $(this).toggleClass($(this).data('animate'));
            delayTime = 0;
            next();
            //$(this).removeClass('animated');
            //$(this).toggleClass($(this).data('animate'));
            });
        },
            {
                offset: '95%',
                triggerOnce: true
            });
        $('[data-animate-hover]').hover(function () {
            $(this).css({opacity: 1});
            $(this).addClass('animated');
            $(this).removeClass($(this).data('animate'));
            $(this).addClass($(this).data('animate-hover'));
        }, function () {
            $(this).removeClass('animated');
            $(this).removeClass($(this).data('animate-hover'));
        });
    }
}

/* =========================================
 * sliding 
 *  =======================================*/

 function sliding() {
     $('body').on('click', '.scrollTo, #navigation a', function (event) {
        event.preventDefault();
        var full_url = this.href;
        var parts = full_url.split("#");
        var trgt = parts[1];

        $('body').scrollTo($('#' + trgt), 800, {offset: -80});

     });
 }

/* =========================================
 * sliders 
 *  =======================================*/

function sliders() {
    if ($('.owl-carousel').length) {

    $(".customers").owlCarousel({
        items: 6,
        itemsDesktopSmall: [990, 4],
        itemsTablet: [768, 2],
        itemsMobile: [480, 1]
    });
    $(".testimonials").owlCarousel({
        items: 4,
        itemsDesktopSmall: [1170, 3],
        itemsTablet: [970, 2],
        itemsMobile: [750, 1]
    });
    }

}

/* =========================================
 * counters 
 *  =======================================*/

function counters() {

    $('.counter').counterUp({
    delay: 10,
    time: 1000
    });

}

/* =========================================
 * parallax 
 *  =======================================*/

function parallax() {

    $('.text-parallax').parallax("50%", 0.1);
    
}

/* =========================================
 *  masonry 
 *  =======================================*/

function masonry() {

    $('#references-masonry').css({visibility: 'visible'});

    $('#references-masonry').masonry({
    itemSelector: '.reference-item:not(.hidden)',
    isFitWidth: true,
    isResizable: true,
    isAnimated: true,
    animationOptions: {
        duration: 200,
        easing: 'linear',
        queue: true
    },
    gutter: 30
    });
    scrollSpyRefresh();
    waypointsRefresh();
}

/* =========================================
 * filter 
 *  =======================================*/

$('#filter a').click(function (e) {
    e.preventDefault();

    $('#filter li').removeClass('active');
    $(this).parent('li').addClass('active');

    var categoryToFilter = $(this).attr('data-filter');

    $('.reference-item').each(function () {
        if ($(this).data('category') === categoryToFilter || categoryToFilter === 'all') {
            $(this).removeClass('hidden');
        }
        else {
            $(this).addClass('hidden');
        }
    });

    if ($('#detail').hasClass('open')) {
        closeReference();
    }
    else {
        $('#references-masonry').masonry('reloadItems').masonry('layout');

    }

    scrollSpyRefresh();
    waypointsRefresh();
});

/* =========================================
 *  open reference 
 *  =======================================*/

$('.reference-item').click(function (e) {
    e.preventDefault();

    var element = $(this);
    var title = element.find('.reference-title').text();
    var description = element.find('.reference-description').html();

    images = element.find('.reference-description').data('images').split(',');

    if (images.length > 0) {
    slider = '';
    for (var i = 0; i < images.length; ++i) {
        slider = slider + '<div class="item"><img src=' + images[i] + ' alt="" class="img-responsive"></div>';
    }
    }
    else {
    slider = '';
    }

    $('#detail-title').text(title);
    $('#detail-content').html(description);

    openReference();

});

function openReference() {

    $('#detail').addClass('open');
    $('#references-masonry').animate({opacity: 0}, 300);
    $('#detail').animate({opacity: 1}, 300);

    setTimeout(function () {
    $('#detail').slideDown();
    $('#references-masonry').slideUp();

    }, 300);

    setTimeout(function () {
    $('body').scrollTo($('#detail'), 1000, {offset: -80});
    }, 500);

}

function closeReference() {

    $('#detail').removeClass('open');
    $('#detail').animate({'opacity': 0}, 300);

    setTimeout(function () {
    $('#detail').slideUp();
    $('#references-masonry').slideDown().animate({'opacity': 1}, 300).masonry('reloadItems').masonry();

    }, 300);

    setTimeout(function () {
    $('body').scrollTo($('#filter'), 1000, {offset: -110});
    }, 500);


    setTimeout(function () {
    $('#references-masonry').masonry('reloadItems').masonry();
    }, 800);

}

$('#detail .close').click(function () {
    closeReference(true);
})

/* =========================================
 * full screen intro 
 *  =======================================*/

function fullScreenContainer() {

    var screenWidth = $(window).width() + "px";
    var screenHeight = '';
    if ($(window).height() > 500) {
    screenHeight = $(window).height() + "px";
    }
    else {
    screenHeight = "500px";
    }


    $("#intro, #intro .item").css({
    width: screenWidth,
    height: screenHeight
    });
}

/* =========================================
 *  map 
 *  =======================================*/

function map() {
    if (($("#map").length > 0)) {
        var styles = [{"featureType": "landscape", "stylers": [{"saturation": -100}, {"lightness": 65}, {"visibility": "on"}]}, {"featureType": "poi", "stylers": [{"saturation": -100}, {"lightness": 51}, {"visibility": "simplified"}]}, {"featureType": "road.highway", "stylers": [{"saturation": -100}, {"visibility": "simplified"}]}, {"featureType": "road.arterial", "stylers": [{"saturation": -100}, {"lightness": 30}, {"visibility": "on"}]}, {"featureType": "road.local", "stylers": [{"saturation": -100}, {"lightness": 40}, {"visibility": "on"}]}, {"featureType": "transit", "stylers": [{"saturation": -100}, {"visibility": "simplified"}]}, {"featureType": "administrative.province", "stylers": [{"visibility": "off"}]}, {"featureType": "water", "elementType": "labels", "stylers": [{"visibility": "on"}, {"lightness": -25}, {"saturation": -100}]}, {"featureType": "water", "elementType": "geometry", "stylers": [{"hue": "#ffff00"}, {"lightness": -25}, {"saturation": -97}]}];
        map = new GMaps({
        el: '#map',
        lat: 48.5500328,
        lng: 3.2948152,
        zoomControl: true,
        zoomControlOpt: {
            style: 'SMALL',
            position: 'TOP_LEFT'
        },
        panControl: false,
        streetViewControl: false,
        mapTypeControl: false,
        overviewMapControl: false,
        scrollwheel: false,
        draggable: true,
        styles: styles
        });

        var image = 'img/marker.png';

        map.addMarker({
        lat: 48.5500328,
        lng: 3.2948152,
        icon: image/* ,
         title: '',
         infoWindow: {
         content: '<p>HTML Content</p>'
         }*/
        });
    }
}

/* =========================================
 *  UTILS
 *  =======================================*/

function utils() {

    /* tooltips */

    $('[data-toggle="tooltip"]').tooltip();

    /* external links in new window*/

    $('.external').on('click', function (e) {

    e.preventDefault();
    window.open($(this).attr("href"));
    });
    /* animated scrolling */

}

$.fn.alignElementsSameHeight = function () {
    $('.same-height-row').each(function () {

    var maxHeight = 0;
    var children = $(this).find('.same-height');
    children.height('auto');
    if ($(window).width() > 768) {
        children.each(function () {
        if ($(this).innerHeight() > maxHeight) {
            maxHeight = $(this).innerHeight();
        }
        });
        children.innerHeight(maxHeight);
    }

    maxHeight = 0;
    children = $(this).find('.same-height-always');
    children.height('auto');
    children.each(function () {
        if ($(this).height() > maxHeight) {
        maxHeight = $(this).innerHeight();
        }
    });
    children.innerHeight(maxHeight);
    });
}

/* refresh scrollspy */
function scrollSpyRefresh() {
    setTimeout(function () {
    $('body').scrollspy('refresh');
    }, 1000);
}

/* refresh waypoints */
function waypointsRefresh() {
    setTimeout(function () {
        $.waypoints('refresh');
    }, 1000);
}

/* refresh waypoints */
function slider() {
    var element = $('.img-slider');
    if ($("#one").length > 0) {
        images = element.data('images').split(',');

        if (images.length > 0) {
            slider = '';
            for (var i = 0; i < images.length; ++i) {
                slider = slider + '<div class="item"><img src=' + images[i] + ' alt="" class="img-responsive"></div>';
            }
        } else {
            slider = '';
        }

        $('#detail-slider').html(slider);
        $('#detail-slider').owlCarousel({
            slideSpeed: 500,
            paginationSpeed: 600,
            autoPlay: 4000,
            stopOnHover: true,
            singleItem: true,
            afterInit: ''
        });
    }
}

function form() {
    $('body').on('change', '#weekDay', function (event) {
        $( "#weekDay option:selected" ).each(function() {
            if ($( this ).text() == 'Lundi') {
                var day = 'Lun';
            } else {
                var day = 'Mar';
            }

            $.each($('#date option'), function( index, element ) {
                if (!element.text.match("^(Date|" + day + ").*")) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
        $('#date').removeAttr('disabled');
    });

    $('body').on('change', '#date', function (event) {
        $('#time').attr('disabled', 'disabled');
        $("#date option:selected").each(function() {
            var url = '/calendar/' + $(this).val();
            var el = $('#time');
            $('#icon-time').html('<i class="fa fa-refresh fa-spin"></i>');
            var request = $.ajax({
              type: "GET",
              url: url
            }).done(function(times) {
                var times = jQuery.parseJSON(times);
                el.empty();
                $.each(times, function(value, key) {
                    var option = $('<option value="' + value + '">' + key + '</option>');
                    el.append(option);
                });
                $('#icon-time').html('<i class="fa fa-angle-double-down">');
                $('#time').removeAttr('disabled');
            });
        });
    });
}

function adminDate() {
    $('#full-date').datetimepicker({
        format: "dd/mm/yyyy hh:ii",
        language: "fr",
        autoclose: true,
        orientation: "top left",
        minuteStep: 60,
        daysOfWeekDisabled: [0, 3, 4, 5, 6]
    });
}

function modal() {
    $('.modal').modal('toggle');
}