/* global document */
/* global jQuery */
/* global confirm */

(function ($) {

    'use strict';

    var replacements = [];

    $(document).ready(function(){
        $('.mace-replace-start-button').on('click', function(e){
            $('.mace-replace-start-button').prop('disabled', true);
            e.preventDefault();
            $('.image-replace-status').html('');
            setupImages();
        });
        $('#media-items').bind("DOMSubtreeModified",function(){
            if ($('.media-item', this).length > 0 && $('.progress', this).length < 1){
                $('.mace-replace-start-button').prop('disabled', false);
            }
          });
    });

    var setupImages = function() {
        replacements = [];

        var requestIds = $('#mace-request-ids').val().split(',');
        var uploadedIds = [];
        $('#media-items .media-item .edit-attachment').each(function(){
            var href = $(this).attr('href');
            href = href.match(/post=(\d*)/);
            uploadedIds.push(href[1]);
        });
        $('.image-replace-status').html('Matching...');
        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':           'mace_bulk_replace_get_replacements_array',
                'security':         $('#mace-image-bulk-replace-nonce').val(),
                'replace_ids':          requestIds,
                'replaceWith_ids':      uploadedIds
            }
        });
        xhr.always(function (res) {
            if ('success' === res.status) {
                console.log(res);
                replacements = res.args.replacements;
                if ( replacements !== false ) {
                    replacements = $.map(replacements, function(value, index) {
                        return [value];
                    });
                    replaceNextImage();
                } else {
                    $('.image-replace-status').html('No matches found');
                }
            } else {
                $('.image-replace-status').html('Something went wrong');
            }
        });
    };

    var replaceNextImage = function() {
        console.log(replacements);
        var msg = $('.image-replace-status').html();
        if ( replacements.length < 1) {
            msg += '<br>Done!';
            $('.mace-replace-start-button').prop('disabled', false);
            $('.image-replace-status').html(msg);
            return;
        }
        var nextImage = replacements.shift();

        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':           'mace_bulk_replace',
                'security':         $('#mace-image-bulk-replace-nonce').val(),
                'replace':          nextImage.leftId,
                'replaceWith':      nextImage.rightId
            }
        });

        xhr.always(function (res) {
            if ('success' === res.status) {
                msg += '<br>' + nextImage.leftName + ' replaced with ' + nextImage.rightName;
                $('.image-replace-status').html(msg);
            } else {
                msg += '<br>' + nextImage.leftName + ' Something went wrong.';
                $('.image-replace-status').html(msg);
            }
            replaceNextImage();
        });

    };


})(jQuery);
