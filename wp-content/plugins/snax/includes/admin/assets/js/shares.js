/* global document */
/* global jQuery */

(function ($) {

    'use strict';

    var networksSorting = function() {

        $('.snax-share-networks.sortable').sortable({
            cursor: "move", // Cursor during dragging.
            update: function(e, ui) {
                var $networks = $(ui.item).parents('.snax-share-networks');
                var $order    = $networks.parent().find('.snax-share-networks-order');

                var order = [];

                $networks.find('.snax-share-network').each(function() {
                    order.push($(this).val());
                });

                // Update networks order.
                $order.val(order.join(','));
            }
        });
    };

    $(document).ready(function () {

        // Allow networks sorting.
        networksSorting();

    });

})(jQuery);
