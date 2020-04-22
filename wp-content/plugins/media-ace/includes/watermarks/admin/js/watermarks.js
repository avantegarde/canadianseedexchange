/* global document */
/* global jQuery */

(function ($) {

    'use strict';

    var updateAttachmentActions = function($rowActions) {
        var isExcluded = 'excluded' === $rowActions.find('.mace-exclude-watermarks').attr('data-mace-status');

        var $addSpan     = $rowActions.find('.mace_add_watermarks');
        var $removeSpan  = $rowActions.find('.mace_remove_watermarks');
        var $includeSpan = $rowActions.find('.mace_include_watermarks');
        var $excludeSpan = $rowActions.find('.mace_exclude_watermarks');

        if (isExcluded) {
            $addSpan.hide();
            $removeSpan.hide();
            $includeSpan.show();
            $excludeSpan.hide();
        } else {
            $addSpan.show();
            $removeSpan.show();
            $includeSpan.hide();
            $excludeSpan.show();
        }
    };

    var handleExcludeAction = function() {
        $('a.mace-exclude-watermarks').on('click', function(e) {
            e.preventDefault();

            var $excludeLink = $(this);
            var $rowActions = $excludeLink.parents('.row-actions');

            var xhr = $.ajax({
                'type': 'POST',
                'url': ajaxurl,
                'dataType': 'json',
                'data': {
                    'action':           'mace_watermarks_exclude',
                    'mace_media_id':    $excludeLink.attr('data-mace-id')
                }
            });

            xhr.done(function (res) {
                if ('success' === res.status) {
                    $excludeLink.attr('data-mace-status', 'excluded');
                    updateAttachmentActions($rowActions);
                }
            });
        });
    };

    var handleIncludeAction = function() {
        $('a.mace-include-watermarks').on('click', function(e) {
            e.preventDefault();

            var $includeLink = $(this);
            var $rowActions  = $includeLink.parents('.row-actions');
            var $excludeLink = $rowActions.find('a.mace-exclude-watermarks');

            var xhr = $.ajax({
                'type': 'POST',
                'url': ajaxurl,
                'dataType': 'json',
                'data': {
                    'action':           'mace_watermarks_include',
                    'mace_media_id':    $includeLink.attr('data-mace-id')
                }
            });

            xhr.done(function (res) {
                if ('success' === res.status) {
                    $excludeLink.attr('data-mace-status', '');
                    updateAttachmentActions($rowActions);
                }
            });
        });
    };

    $(document).ready(function() {
        $('a.mace-exclude-watermarks').each(function() {
            var $rowActions = $(this).parents('.row-actions');

            updateAttachmentActions($rowActions);
        });

        handleExcludeAction();
        handleIncludeAction();
    });
})(jQuery);
