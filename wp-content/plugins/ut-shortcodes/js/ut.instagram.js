/* <![CDATA[ */
(function($){

    "use strict";

    /* Load Instagram Feeds
    ================================================== */
    var instagram_is_loading = false,
        load_instagram_images_on_scroll = false;

    $(window).on("load", function () {

        $(".ut-instagram-gallery-wrap").each( function(){

            $(this).height( $(this).height() );

        });

    });

    function ut_load_instagram_feeds( $gallery, $clear, atts, callback ){

        if( !atts ) {
            return;
        }

        $.ajax({

            type: 'POST',
            url: utShortcode.ajaxurl,
            data: {
                "action": "ut_get_gallery_instagram_feed",
                "atts" : JSON.stringify(atts),
            },
            success: function(response) {

                // update atts on gallery
                $gallery.data("atts", response.atts);

                // get new items
                var $newItems = $(response.feeds );

                // hide items since images are not loaded yet
                $newItems.find(".ut-image-gallery-item").hide();
                $newItems.insertBefore( $clear );

                // wait for images
                $newItems.imagesLoaded( function() {

                    // show new images
                    $newItems.find(".ut-image-gallery-item").show();

                    // animate container height
                    $gallery.parent(".ut-instagram-gallery-wrap").height( $gallery.height() );

                    // run appear for possible animations
                    $.force_appear();

                    // remove flag
                    instagram_is_loading = false;

                });

                /* restart */
                $('.ut-instagram-gallery').ut_require_js({
                    plugin: 'lightGallery',
                    source: 'lightGallery',
                    callback: function (element) {

                        element.lightGallery({
                            selector: '.ut-vc-images-lightbox',
                            exThumbImage: "data-exthumbimage",
                            download: site_settings.lg_download,
                            getCaptionFromTitleOrAlt: "true",
                            mode: site_settings.lg_mode,
                            hash: false
                        });

                    }

                });

                return false;

            },
            complete : function() {

                if (callback && typeof(callback) === "function") {
                    callback();
                }

            }

        });

    }

    $(document).on('click', '.ut-load-instagram-feeds', function(event) {

        var instagram_gallery_id = $(this).data('for'),
            $button = $(this);

        if( instagram_is_loading ) {
            return false;
        }

        // set flag
        instagram_is_loading = true;

        // hide load more button - will be replaced with a loading icon on scroll
        $button.fadeOut();

        // load feeds
        ut_load_instagram_feeds( $(instagram_gallery_id), $(instagram_gallery_id + '_clear') , $(instagram_gallery_id).data("atts"), function() {

            // remove flag
            instagram_is_loading = false;

            // activate scroll loading
            load_instagram_images_on_scroll = true;

        });

        event.preventDefault();

    });


    $(window).scroll( function(){

        if( !load_instagram_images_on_scroll || instagram_is_loading ) {
            return;
        }

        $('.ut-instagram-gallery').each(function(){

            var $this = $(this);

            if( $(window).scrollTop() >= $this.offset().top + $this.outerHeight() - window.innerHeight) {

                $this.find(".ut-instagram-module-loading").fadeIn();

                // set flag
                instagram_is_loading = true;

                // load feeds
                ut_load_instagram_feeds( $this, $('#' + $this.attr("id") + '_clear') , $this.data("atts"), function() {

                    $this.find(".ut-instagram-module-loading").fadeOut();

                });

            }

        });


    });

})(jQuery);
/* ]]> */