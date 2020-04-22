/* global document */
/* global jQuery */

(function ($) {

    'use strict';

    var svgIconPreview = function() {
        var $preview = $('.wyr-reaction-icon-preview > span');

        if (!$preview.length) {
            return;
        }

        // Icon color picker.
        $('[name=icon_color].wyr-color-picker').wpColorPicker({
            'mode' : 'hsl',
            'change': function(e, ui) {
                var hex = ui.color.toString();

                $preview.css('color', hex);
            },
            'clear': function() {
                $preview.css('color', '');
            }
        });

        // Icon background color picker.
        $('[name=icon_background_color].wyr-color-picker').wpColorPicker({
            'mode' : 'hsl',
            'change': function(e, ui) {
                var hex = ui.color.toString();

                $preview.css('background-color', hex);
            },
            'clear': function() {
                $preview.css('background-color', '');
            }
        });

        // Icon type switcher.
        $('[name=icon_type]').on('change', function() {
            var type = $(this).val();

            $preview.removeClass('wyr-reaction-icon-with-text wyr-reaction-icon-with-visual');
            $preview.addClass('wyr-reaction-icon-with-' + type);
        });

        // Icon picker.
        $('.wyr-reaction-icon-sets .wyr-icon-item').on('click', function() {
            var $iconItem = $(this);
            var $icon = $iconItem.find('.wyr-reaction-icon');
            var $newPreview = $icon.clone();

            // Set up new preview icon.
            $newPreview.attr('style', $preview.attr('style'));
            $newPreview.find('.wyr-reaction-icon-text').text($('.term-name-wrap [name=name]').val());

            var iconType = $('[name=icon_type]:checked').val();
            $newPreview.removeClass('wyr-reaction-icon-with-text wyr-reaction-icon-with-visual');
            $newPreview.addClass('wyr-reaction-icon-with-' + iconType);

            $preview.replaceWith($newPreview);
            $preview = $newPreview;
        });

        // Icon name.
        $('.term-name-wrap [name=name]').on('keyup', function() {
            var name = $(this).val();

            $preview.find('.wyr-reaction-icon-text').text(name);
        });
    };

    var svgIconUpload = function() {
        $('.wyr-upload-new-icon').on('click', function(e) {
            e.preventDefault();

            openMediaLibrary({
                'onSelect': function(obj) {
                    $.ajax({
                        'type': 'POST',
                        'url': ajaxurl,
                        'dataType': 'json',
                        'data': {
                            'action':           'wyr_save_custom_icon',
                            'wyr_icon_id':      obj.id
                        }
                    }).done(function(response) {
                        location.reload();
                    });
                }
            });
        });
    };

    var openMediaLibrary = function(callbacks) {
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

            callbacks.onSelect(objSelected);
        });

        frame.open();
    };

    $(document).ready(function () {
        svgIconPreview();
        svgIconUpload();
    });

})(jQuery);
