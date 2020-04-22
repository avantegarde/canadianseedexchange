/* global jQuery */
/* global document */

(function($) {

    'use strict';

    $(document).ready(function(){
        $('.g1-socials-image-picker').each(function() {
            imageUploadControl($(this));
        });
        $(document).on('widget-updated', function(event, widget){
            $('.g1-socials-image-picker', widget).each(function() {
                imageUploadControl($(this));
            });
        });
        $(document).on('widget-added', function(event, widget){
            $('.g1-socials-image-picker', widget).each(function() {
                imageUploadControl($(this));
            });
        });
    });

    var imageUploadControl = function($el) {
        var $addLink    = $el.find('.g1-add-image');
        var $deleteLink = $el.find('.g1-delete-image');
        var $imageId    = $el.find('input');
        if ( $imageId.val().length > 0 ) {
            $addLink.hide();
            $deleteLink.show();
        } else {
            $addLink.show();
            $deleteLink.hide();
        }

        $addLink.on('click', function(e) {
            e.preventDefault();
            var $that = $(this);
            openMediaLibrary(function(imageObj) {
                var url = imageObj.url;
                var $parent = $that.closest('p');
                $('input', $parent).val(url);
                $addLink.hide();
                $deleteLink.show();
                $('input', $parent).trigger('change');
            });
        });

        $deleteLink.on('click', function(e) {
            e.preventDefault();
            if ( ! confirm( 'Are you sure?' ) ) {
                return;
            }
            var $parent = $(this).closest('p');
            $('input', $parent).val('');
            $addLink.show();
            $deleteLink.hide();
            $('input', $parent).trigger('change');
        });
    };

    var openMediaLibrary = function(callback) {
        var frame = wp.media({
            'title':    'Select an image',
            'multiple': false,
            'library':  {
                'type': 'image'
            },
            'button': {
                'text': 'Insert'
            }
        });

        frame.on('select',function() {
            var objSelected = frame.state().get('selection').first().toJSON();

            callback(objSelected);
        });

        frame.open();
    };

})(jQuery);
