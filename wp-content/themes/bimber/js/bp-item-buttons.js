// BuddyPress Profile: get the first item-button before the dropdown
(function ($) {
    'use strict';

    $(document).ready(function () {
        $('#item-buttons .g1-drop').each(function() {
            var $drop = $(this);

            // Get the first element and place it before the dropdown.
            var firstItem = $drop.find('.menu-item:first').detach();
            if ( firstItem.length ) {
                // Adjust the HTML markup.
                firstItem.removeClass('menu-item').find('a').addClass('g1-button g1-button-simple g1-button-m');
                // Insert before the drop.
                firstItem.insertBefore($drop);
            }

            // Remove empty dropdown.
            if ( ! $drop.find('.menu-item').length ) {
                $drop.remove();
            }

        });
    } );
})(jQuery);
