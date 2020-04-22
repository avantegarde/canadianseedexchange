/* global document */
/* global jQuery */
/* global confirm */

(function ($) {

    'use strict';

    var handleAddAction = function() {
        $('a.mace-image-size-add').on('click', function(e) {
            e.preventDefault();

            var $row    = $(this).parents('.mace-image-size-new-form');
            var name   = $row.find('.name-field').val();
            var width  = parseInt($row.find('.width-field').val(), 10);
            var height = parseInt($row.find('.height-field').val(), 10);
            var crop   = $row.find('.crop-field').is(':checked');
            var cropX  = $row.find('.crop-x-field').val();
            var cropY  = $row.find('.crop-y-field').val();

            if ( ! name ) {
                alert('Name is required.');
                return;
            }

            if ( ! width || ! height ) {
                alert('Width and/or height have to be a valid number.');
                return;
            }

            // Prepend with custom image size prefix.
            var prefix = $('#mace-image-size-prefix').val();

            name = prefix + name;

            saveImageSize( name, {
                'width':    width,
                'height':   height,
                'crop':     crop,
                'cropX':    cropX,
                'cropY':    cropY
            }, function(res) {
                if ('success' === res.status) {
                    window.location.href = window.location.href.replace(/&group=[^&]+/, '') + '&group=custom';
                }
            } );

        });
    };

    var handleAddNewAction = function() {
        $('a.mace-image-size-add-new').on('click', function(e) {
            e.preventDefault();

            $('.mace-image-size-new-form').toggleClass('mace-hidden');
        });
    };

    var handleFilterAction = function() {
        $('#post-query-submit').on('click', function(e) {
            e.preventDefault();

            var group = $('#filter-by-group option:selected').val();

            window.location.href = window.location.href.replace(/&group=[^&]+/, '') + '&group=' + group;
        });
    };

    var handleEditAction = function() {
        $('a.mace-image-size-edit, .width .mace-value, .height .mace-value, .crop .mace-value, .crop_x .mace-value, .crop_y .mace-value').on('click', function(e) {
            if ($(e.target).is('a')) {
                e.preventDefault();
            }

            $('.mace-editing').removeClass('mace-editing');

            var $tr = $(this).parents('tr');

            $tr.addClass('mace-editing');

            var $inlineEditRow = $('#mace-inline-edit-row');

            if ($inlineEditRow.length === 0) {
                $inlineEditRow = $(
                    '<tr id="mace-inline-edit-row">' +
                        '<td colspan="5" class="colspanchange">' +
                            '<button type="button" class="button cancel alignleft">Cancel</button>' +
                            '<button type="button" class="button button-primary save alignright">Update</button>' +
                        '</td>'+
                    '</tr>');

                $inlineEditRow.on('click', '.save', function(e) {
                    e.preventDefault();

                    saveActiveRow();
                });

                $inlineEditRow.on('click', '.cancel', function(e) {
                    e.preventDefault();

                    cancelEdition();
                });
            }

            $inlineEditRow.insertAfter($tr);
            $inlineEditRow.show();
        });
    };

    var saveActiveRow = function() {
        var $row            = $('.mace-editing');
        var $widthValue     = $row.find('.width .mace-value');
        var $widthEdit      = $row.find('.width .mace-edit input');
        var $heightValue    = $row.find('.height .mace-value');
        var $heightEdit     = $row.find('.height .mace-edit input');
        var $cropValue      = $row.find('.crop .mace-value');
        var $cropEdit       = $row.find('.crop .mace-edit input');
        var $cropXValue     = $row.find('.crop_x .mace-value');
        var $cropXEdit      = $row.find('.crop_x .mace-edit select option:selected');
        var $cropYValue     = $row.find('.crop_y .mace-value');
        var $cropYEdit      = $row.find('.crop_y .mace-edit select option:selected');

        // Save.
        var name    = $row.find('.name .mace-image-size-edit').attr('data-mace-image-size-name');
        var width   = $widthEdit.val();
        var height  = $heightEdit.val();
        var crop    = $cropEdit.is(':checked');
        var cropX   = $cropXEdit.val();
        var cropY   = $cropYEdit.val();

        saveImageSize( name, {
            'width':    width,
            'height':   height,
            'crop':     crop,
            'cropX':    cropX,
            'cropY':    cropY
        }, function(res) {
            if ('success' === res.status) {
                $row.addClass('mace-saved');
            }
        } );

        // Update preview.
        $widthValue.text(width);
        $heightValue.text(height);

        if ( crop ) {
            $cropValue.addClass('mace-checked');
        } else {
            $cropValue.removeClass('mace-checked');
        }

        $cropXValue.text(cropX);
        $cropYValue.text(cropY);

        // Switch mode.
        $row.removeClass('mace-editing');
        $('#mace-inline-edit-row').hide();

    };

    var cancelEdition = function() {
        var $row            = $('.mace-editing');
        var $widthValue     = $row.find('.width .mace-value');
        var $widthEdit      = $row.find('.width .mace-edit input');
        var $heightValue    = $row.find('.height .mace-value');
        var $heightEdit     = $row.find('.height .mace-edit input');
        var $cropValue      = $row.find('.crop .mace-value');
        var $cropEdit       = $row.find('.crop .mace-edit input');
        var $cropXValue     = $row.find('.crop_x .mace-value');
        var $cropXEdit      = $row.find('.crop_x .mace-edit select');
        var $cropYValue     = $row.find('.crop_y .mace-value');
        var $cropYEdit      = $row.find('.crop_y .mace-edit select');

        // Revert edit fields.
        $widthEdit.val(parseInt($widthValue.text(), 10));
        $heightEdit.val(parseInt($heightValue.text(), 10));

        if ($cropValue.is('.mace-checked')) {
            $cropEdit.attr('checked', 'checked');
        } else {
            $cropEdit.removeAttr('checked');
        }

        $cropXEdit.val($cropXValue.text());
        $cropYEdit.val($cropYValue.text());

        // Switch mode.
        $row.removeClass('mace-editing');
        $('#mace-inline-edit-row').hide();
    };

    var handleDeleteAction = function() {
        $('a.mace-image-size-delete, a.mace-image-size-deactivate').on('click', function(e) {
            e.preventDefault();

            if ( $(this).is('.mace-image-size-delete') && ! confirm( 'Are you sure you want to remove this image size?' ) ) {
                return;
            }

            var name = $(this).attr('data-mace-image-size-name');

            deleteImageSize( name, function(res) {
                if ('success' === res.status) {
                    window.location.reload();
                }
            } );
        });
    };

    var handleActivateAction = function() {
        $('a.mace-image-size-activate').on('click', function(e) {
            e.preventDefault();

            var name = $(this).attr('data-mace-image-size-name');

            activateImageSize( name, function(res) {
                if ('success' === res.status) {
                    window.location.reload();
                }
            } );
        });
    };

    var saveImageSize = function(name, args, callback) {
        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':       'mace_save_image_size',
                'security':     $('#mace-image-size-nonce').val(),
                'mace_name':    name,
                'mace_width':   args.width,
                'mace_height':  args.height,
                'mace_crop':    args.crop,
                'mace_crop_x':  args.cropX,
                'mace_crop_y':  args.cropY
            }
        });

        xhr.done(function (res) {
            callback(res);
        });
    };

    var deleteImageSize = function(name, callback) {
        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':       'mace_delete_image_size',
                'security':     $('#mace-image-size-nonce').val(),
                'mace_name':    name
            }
        });

        xhr.done(function (res) {
            callback(res);
        });
    };

    var activateImageSize = function(name, callback) {
        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':       'mace_activate_image_size',
                'security':     $('#mace-image-size-nonce').val(),
                'mace_name':    name
            }
        });

        xhr.done(function (res) {
            callback(res);
        });
    };

    $(document).ready(function() {

        handleAddAction();
        handleAddNewAction();
        handleEditAction();
        handleDeleteAction();
        handleActivateAction();
        handleFilterAction();

    });

})(jQuery);
