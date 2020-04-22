/* global G1_PageBuilder */
/* global G1_ShortcodeGenerator */

(function () {

    'use strict';

    var name = 'g1_socials';

    var config = {
        fields: {
            'include': {
                'type':             'input'
            },
            'exclude': {
                'type':             'input'
            },
            'template': {
                'type':             'select',
                'options': {
                    'grid':         'grid'
                }
            },
            'icon_size': {
                'type':             'select',
                'options': {
                    '32':           '32',
                    '48':           '48',
                    '64':           '64'
                }
            },
            'icon_color': {
                'type':             'select',
                'options': {
                    'original':     'original',
                    'dark':         'dark',
                    'light':        'light'
                }
            }
        }
    };

    // register page builder element
    if (typeof G1_PageBuilder !== 'undefined') {
        var pbBlock = {
            level: 3,
            withControls: true,
            cssBlockClass: 'g1-social-icons',
            cssElementClass: 'g1-social-icons',
            toolbarSection: 'content_elements',
            label: 'Social Icons',
            description: 'List of links to popular social services.'
        };

        G1_PageBuilder.registerBlock(name, pbBlock);
        G1_PageBuilder.registerElementConfig(name, config);
    }

    // register shortcode generator element
    if (typeof G1_ShortcodeGenerator !== 'undefined') {
        G1_ShortcodeGenerator.registerElementConfig(name, config);
    }
})();