<?php

namespace YTP\Base; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

class EnqueueAssets
{

    public function register()
    {
        add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
    }

    function enqueue_block_assets()
    {
        wp_register_script('plyrIoJS', YTP_PLUGIN_DIR . 'public/js/plyr-v3.7.8.js', [], YTP_PLUGIN_VERSION, true);
        wp_register_style('plyrIoCSS', YTP_PLUGIN_DIR . 'public/css/plyr-v3.7.8.css', [], YTP_PLUGIN_VERSION);

        wp_register_script('plyrio', YTP_PLUGIN_DIR . 'public/js/plyr-v3.7.8.js', [], YTP_PLUGIN_VERSION, true);
        wp_register_style('plyrio', YTP_PLUGIN_DIR . 'public/css/plyr-v3.7.8.css', [], YTP_PLUGIN_VERSION);

        wp_enqueue_style('plyrIoCSS');
        wp_enqueue_script('plyrIoJS');

        wp_register_style('ytp-blocks', YTP_PLUGIN_DIR . 'dist/blocks.css', ['plyrIoCSS'], YTP_PLUGIN_VERSION);

        wp_register_style('ytp-public', YTP_PLUGIN_DIR . 'dist/public.css', ['plyrIoCSS'], YTP_PLUGIN_VERSION);
        wp_register_script('ytp-public', YTP_PLUGIN_DIR . 'dist/public.js', ['plyrIoJS', 'react', 'react-dom'], YTP_PLUGIN_VERSION, true);
    }



    public function pluginsLoaded()
    {
        $imported_version = get_option('ytp_import_ver', 0);
        if ($imported_version < '1.0.0') {
            Import::meta();
            update_option('ytp_import_ver', '1.0.0');
        }
    }
}
