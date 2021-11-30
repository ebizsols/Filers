;( function( $, window, document, undefined ) {

    "use strict";

    var pluginName = "utSlotMachine",
        defaults = {
            blur: true
        };

    function Plugin ( element, options ) {

        this.element = element;
        this.settings = $.extend( {}, defaults, options );

        this._defaults = defaults;
        this._name = pluginName;

        this.init();

    }

    $.extend( Plugin.prototype, {
        init: function() {

            this.generateMarkup();
            this.addBlurEffect();
            this.animateMachine();


        },

        addBlurEffect() {

            if( this.settings.blur ) {

                var $ul = $('.ut-slot-machine-number ul', this.element );

                $ul.each(function (i, element) {

                    $(element).css({
                        'filter': "url('" + $ul.parent().data('filter') + "')"
                    });

                });
            }
        },

        generateSlot( $element ) {

            var all_numbers = [0,1,2,3,4,5,6,7,8,9];

            $element.append( $("<ul>") );

            $.each( all_numbers, function(i, v) {

                $element.find('ul').append( $("<li>").text(v) );

            });


        },

        generateMarkup() {

            var self = this,
                $this = $(self.element);

            $('.ut-slot-machine-number', $this ).each(function () {

                var $single_slot = $(this);

                self.generateSlot( $single_slot );

            });


        },
        animateMachine: function() {

            var self = this,
                $this = $(self.element);











                $this.ut_require_js({
                    plugin: 'anime',
                    source: 'anime',
                    callback: function( element ) {

                        element.find('.ut-slot-machine-number').each(function () {

                            var countUpValue = '',
                                feGaussianBlur = { x: 0, y: 0 };



                            anime({
                                targets: '',
                                translateY: countUpValue * -10 + '%',
                                easing: 'easeOutQuint',
                                delay: 200,
                                duration: 1200,
                                complete: function complete() {

                                }
                            });

                            if (self.settings.blur) {

                                anime({
                                    targets: feGaussianBlur,
                                    easing: 'easeOutQuint',
                                    duration: 1200,
                                    delay: 200,
                                    y: [50 + countUpValue * 10, 0],
                                    round: 1,
                                    begin: function begin() {

                                        // define elements

                                    },
                                    update: function update() {

                                        // adjust filter
                                        //  remove filter when finished

                                    }
                                });

                            }

                        });

                    }

                });

        }
    } );

    $.fn[ pluginName ] = function( options ) {

        return this.each( function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" +
                    pluginName, new Plugin( this, options ) );
            }
        });

    };

} )( jQuery, window, document );