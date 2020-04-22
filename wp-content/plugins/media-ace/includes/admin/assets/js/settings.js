/* global document */
/* global jQuery */

(function ($) {

    'use strict';

    var isSettingsPage = function(id) {
        return $('body').hasClass('settings_page_' + id);
    };

    var hideSaveButton = function() {
        $('.submit .button-primary').hide();
    };

    var gifSettingsPage = function() {
        var $gifStorage = $('#mace_gif_storage');

        var gifStorageChanged = function() {
            var storage = $gifStorage.val();
            var $s3 = $('.mace-s3-storage-config').parents('tr');

            if ('s3' === storage) {
                $s3.show();
            } else {
                $s3.hide();
            }
        };

        $gifStorage.on('change', gifStorageChanged);
        gifStorageChanged();
    };

    var imageSizesSettingsPage = function() {
        hideSaveButton();
    };

    var regenerateThumbsSettingsPage = function() {
        hideSaveButton();
    };

    var watermarksSettingsPage = function() {
        var $adv = $('#mace-watermarks-advanced-options').parents('tr');

        $adv.nextAll('tr').hide();

        $adv.on('click', function(e) {
            e.preventDefault();

            $adv.nextAll('tr').toggle();
        });
    };

    var loadToggleModuleControl = function() {
        var $enabled = $('.mace-toggle-module');

        var changed = function() {
            var isEnabled = $enabled.is(':checked');

            var $rel = $enabled.parents('tr').nextAll('tr');

            if (isEnabled) {
                $rel.show();

                // Hide Advanced options.
                $('#mace-watermarks-advanced-options').parents('tr').nextAll('tr').hide();
            } else {
                $rel.hide();
            }
        };

        $enabled.on('change', changed);
        changed();
    };

    var loadMediaLibraryControl = function() {
        $('.mace-media-library-image').each(function() {
            var $wrapper        = $(this);
            var $preview        = $wrapper.find('.mace-preview');
            var $id             = $wrapper.find('.mace-image-id');
            var $selectButton   = $wrapper.find('.mace-select-image');
            var $removeButton   = $wrapper.find('.mace-remove-image');

            $selectButton.on('click', function() {
                openMediaLibrary({
                    'onSelect': function(obj) {
                        $id.val(obj.id);

                        var thumb = '';
						if( obj.sizes.thumbnail !== undefined ){
							thumb = obj.sizes.medium;
						} else {
							thumb = obj.sizes.full;
						}

                        var $image = $('<img />');
                        $image.attr('width', thumb.width + 'px');
                        $image.attr('height', thumb.height + 'px');
                        $image.attr('src', thumb.url);

                        $preview.prepend($image);

                        $wrapper.addClass('mace-image-set');
                        $wrapper.removeClass('mace-image-not-set');

                        $removeButton.show();
                        $selectButton.hide();
                    }
                });
            });

            $removeButton.on('click', function() {
                $id.val('');
                $preview.find('img').remove();

                $wrapper.removeClass('mace-image-set');
                $wrapper.addClass('mace-image-not-set');

                $removeButton.hide();
                $selectButton.show();
            });

            // Init.
            if ($wrapper.is('.mace-image-set')) {
                $selectButton.hide();
            } else {
                $removeButton.hide();
            }
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
                'text': 'Use'
            }
        });

        frame.on('select',function() {
            var objSelected = frame.state().get('selection').first().toJSON();

            callbacks.onSelect(objSelected);
        });

        frame.open();
    };

    $(document).ready(function() {
        loadMediaLibraryControl();
        loadToggleModuleControl();

        if (isSettingsPage('mace-gif-settings')) {
            gifSettingsPage();
        }

        if (isSettingsPage('mace-image-sizes-settings')) {
            imageSizesSettingsPage();
        }

        if (isSettingsPage('mace-regenerate-thumbs-settings')) {
            regenerateThumbsSettingsPage();
        }

        if (isSettingsPage('mace-watermarks-settings')) {
            watermarksSettingsPage();
        }
    });

})(jQuery);
