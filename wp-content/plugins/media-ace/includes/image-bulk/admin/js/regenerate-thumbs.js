/* global document */
/* global jQuery */
/* global confirm */

(function ($) {

    'use strict';

    var handleRegenerateAction = function() {
        $('.mace-regenerate-thumbs .mace-start').on('click', function(e) {
            e.preventDefault();

            fetchImages(function(res) {
                if ('success' === res.status) {
                    var ids = res.args.ids;

                    if (confirm('You are going to regenerate ' + ids.length + ' images. Proceed?')) {
                        runBulkRegeneration(ids);
                    }
                } else {
                    alert('Failed while fetching images to regenerate.');
                }
            });
        });
    };

    var fetchImages = function(callback) {
        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':       'mace_fetch_images_to_regenerate',
                'security':     $('#mace-image-bulk-nonce').val()
            }
        });

        xhr.done(function (res) {
            callback(res);
        });
    };

    var regenerateThumbs = function(ids, callback, finishCallback) {
        if (0 === ids.length) {
            finishCallback();
            return;
        }

        var id = ids.shift();

        var xhr = $.ajax({
            'type': 'POST',
            'url': ajaxurl,
            'dataType': 'json',
            'data': {
                'action':           'mace_regenerate_thumbs',
                'security':         $('#mace-image-bulk-nonce').val(),
                'mace_media_id':    id
            }
        });

        xhr.done(function (res) {
            if ('success' === res.status) {
                callback(res);
            }

            regenerateThumbs(ids, callback, finishCallback);
        });
    };

    var runBulkRegeneration = function(ids) {
        var $row = $('.mace-regenerate-thumbs');
        var $processedList = $row.find('.mace-processed-files');
        $processedList.empty();

        $row.addClass('mace-in-progress');
        $row.removeClass('mace-finished');

        var $toProcessCount = $row.find('.mace-to-process span').text(ids.length);
        var $processedCount = $row.find('.mace-processed span');
        var $successCount = $row.find('.mace-success span');
        var $failedCount = $row.find('.mace-failed span');

        regenerateThumbs(ids, function(res) {
            var $li = $('<li>' + res.args.filename + ' (ID: ' + res.args.id + ')</li>');

            var $toggle = $('<a href="#" class="mace-toggle-details">Toggle details</a>');

            $toggle.on('click', function(e) {
                e.preventDefault();

                $li.toggleClass('mace-expanded');
            });

            $toggle.appendTo($li);

            var $details = $('<ul class="mace-details">');

            var details     = res.args.data;
            var generated   = details.generated_sizes;
            var deleted     = details.deleted_sizes;
            var failed      = details.failed_sizes;

            $details.append('<li>Image: ' + details.image_path + '</li>');
            $details.append('<li>Generated sizes (' + generated.length + '): ' + generated.join(', ') + '</li>');
            $details.append('<li>Deleted sizes (' + deleted.length + '): ' + (deleted.length > 0 ? deleted.join(', ') : 'none') + '</li>');
            $details.append('<li>Failed sizes (' + failed.length + '): ' + (failed.length > 0 ? failed.join(', ') : 'none') + '</li>');
            $details.append('<li>Generation time: ' + details.generation_time + ' seconds</li>');

            $details.appendTo($li);

            $toProcessCount.text(parseInt($toProcessCount.text(), 10) -1);
            $processedCount.text(parseInt($processedCount.text(), 10) + 1);

            if ('success' === res.status) {
                $li.addClass('mace-success');
                $successCount.text(parseInt($successCount.text(), 10) + 1);
            } else {
                $li.addClass('mace-failed');
                $failedCount.text(parseInt($failedCount.text(), 10) + 1);
            }

            $li.appendTo($processedList);
        }, function() {
            $row.removeClass('mace-in-progress');
            $row.addClass('mace-finished');
        });
    };

    $(document).ready(function() {

        handleRegenerateAction();

        var requestAction = $('#mace-request-action').val();

        if ( 'regenerate-thumbs' === requestAction ) {
            var requestIds = $('#mace-request-ids').val().split(',');

            runBulkRegeneration(requestIds);
        }
    });

})(jQuery);
