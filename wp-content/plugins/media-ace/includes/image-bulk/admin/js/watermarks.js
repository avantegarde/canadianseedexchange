/* global document */
/* global jQuery */
/* global confirm */

(function ($) {

    'use strict';

    var handleWatermarksAction = function() {
        $('.mace-watermarks .mace-start').on('click', function(e) {
            e.preventDefault();

            var $row = $(this).parents('.mace-watermarks');
            var action = 'add-watermarks';

            if ($row.hasClass('mace-remove-watermarks')) {
                action = 'remove-watermarks';
            }

            fetchImages(function(res) {
                if ('success' === res.status) {
                    var ids = res.args.ids;

                    if (confirm('You are going to process ' + ids.length + ' images. Proceed?')) {
                        runBulkAction(action, ids);
                    }
                } else {
                    alert('Failed while fetching image list.');
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
                'action':       'mace_fetch_images_to_watermark',
                'security':     $('#mace-image-bulk-nonce').val()
            }
        });

        xhr.done(function (res) {
            callback(res);
        });
    };

    var doAction = function(action, ids, callback, finishCallback) {
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
                'action':           'mace_' + action.replace('-', '_'),
                'security':         $('#mace-image-bulk-nonce').val(),
                'mace_media_id':    id
            }
        });

        xhr.done(function (res) {
            callback(res);

            doAction(action, ids, callback, finishCallback);
        });
    };

    var runBulkAction = function(action, ids) {
        var $row = $('.mace-' + action);
        var $processedList = $row.find('.mace-processed-files');

        $processedList.empty();

        $row.addClass('mace-in-progress');
        $row.removeClass('mace-finished');

        var $toProcessCount = $row.find('.mace-to-process span').text(ids.length);
        var $processedCount = $row.find('.mace-processed span');
        var $successCount = $row.find('.mace-success span');
        var $failedCount = $row.find('.mace-failed span');

        doAction(action, ids, function(res) {
            var $li = $('<li>' + res.args.filename + ' (ID: ' + res.args.id + ')</li>');

            var $toggle = $('<a href="#" class="mace-toggle-details">Toggle details</a>');

            $toggle.on('click', function(e) {
                e.preventDefault();

                $li.toggleClass('mace-expanded');
            });

            $toggle.appendTo($li);

            var $details = $('<ul class="mace-details">');

            $toProcessCount.text(parseInt($toProcessCount.text(), 10) -1);
            $processedCount.text(parseInt($processedCount.text(), 10) + 1);

            if ('success' === res.status) {
                $li.addClass('mace-success');
                $successCount.text(parseInt($successCount.text(), 10) + 1);

                var details = res.args.data;

                $details.append('<li>Original image: ' + details.image_path + '</li>');

                if ('add' === action) {
                    if (details.backup_path) {
                        $details.append('<li>Backup saved: ' + details.backup_path + '</li>');
                    } else {
                        $details.append('<li>Backup disabled. Removing watermarks is not possible.</li>');
                    }

                    if (details.watermarked) {
                        $details.append('<li>Watermarked sizes:</li>');

                        for(var i = 0; i < details.watermarked.length; i++) {
                            $details.append('<li>' + (i+1) + ') ' + details.watermarked[i] + '</li>');
                        }
                    }
                }

                if ('remove' === action) {
                    $details.append('<li>Restored from backup: ' + details.backup_path + '</li>');
                }

                $details.append('<li>Consumed time: ' + details.time + ' seconds</li>');
            } else {
                $li.addClass('mace-failed');
                $failedCount.text(parseInt($failedCount.text(), 10) + 1);

                $details.append('<li>Error: ' + res.message + '</li>');
            }

            $details.appendTo($li);

            $li.appendTo($processedList);
        }, function() {
            $row.removeClass('mace-in-progress');
            $row.addClass('mace-finished');
        });
    };

    $(document).ready(function() {

        handleWatermarksAction();

        var requestAction = $('#mace-request-action').val();

        if ( -1 !== $.inArray(requestAction, ['add-watermarks', 'remove-watermarks'] ) ) {
            var requestIds = $('#mace-request-ids').val().split(',');

            runBulkAction(requestAction, requestIds);
        }
    });
})(jQuery);
