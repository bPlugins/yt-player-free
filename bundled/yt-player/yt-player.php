<?php
/**
 * Plugin Name: Video Player for YouTube – Embed Videos Your Visitors Will Love to Watch
 * Plugin URI:  http://bplugins.com
 * Description: A simple, accessible, fully customizable & user friendly YouTube Video Player for Wordpress.
 * Version: 2.1.1
 * Author: bPlugins
 * Author URI: http://abuhayatpolash.com
 * License: GPLv3
 * Text Domain: yt-player
 * Domain Path:  /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if (function_exists('ytp_fs')) {
    ytp_fs()->set_basename(true, __FILE__);
} else {
    /*Some Set-up*/
    if (!defined('YTP_PLUGIN_DIR')) {
        define('YTP_PLUGIN_DIR', plugin_dir_url(__FILE__));
    }
    if (!defined('YTP_DIR_PATH')) {
        define('YTP_DIR_PATH', plugin_dir_path(__FILE__));
    }
    if (!defined('YTP_PLUGIN_VERSION')) {
        define('YTP_PLUGIN_VERSION', (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost') ? time() : '2.1.1');
    }
    if (!defined('YTP_IMPORT_VER')) {
        define('YTP_IMPORT_VER', '1.0.0');
    }

    if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
        require_once(dirname(__FILE__) . '/vendor/autoload.php');
    }

    if (!function_exists('ytp_fs')) {
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
        function ytp_fs() {
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
            global $ytp_fs;

            if (!isset($ytp_fs)) {
                if (file_exists(dirname(__FILE__) . '/vendor/freemius/wordpress-sdk/start.php')) {
                    require_once dirname(__FILE__) . '/vendor/freemius/wordpress-sdk/start.php';
                } else if (file_exists(dirname(__FILE__) . '/vendor/freemius/start.php')) {
                    require_once dirname(__FILE__) . '/vendor/freemius/start.php';
                }

                if (function_exists('fs_dynamic_init')) {
                    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
                    $ytp_fs = fs_dynamic_init(array(
                        'id'                  => '5836',
                        'slug'                => 'yt-player',
                        'type'                => 'plugin',
                        'public_key'          => 'pk_829fc74e7bb67d3d555c68048933d',
                        'is_premium'          => false,
                        'premium_slug'        => 'yt-player-pro',
                        'premium_suffix'      => 'Pro',
                        'has_premium_version' => true,
                        'has_addons'          => false,
                        'has_paid_plans'      => true,
                        'menu'                => array(
                            'slug'           => 'edit.php?post_type=ytplayer',
                            'first-path'     => 'edit.php?post_type=ytplayer&page=dashboard',
                            'support'        => false,
                            'contact'        => false,
                        ),
                    ));
                } else {
                    return null;
                }
            }

            return $ytp_fs;
        }

        if (file_exists(dirname(__FILE__) . '/vendor/freemius/wordpress-sdk/start.php') || file_exists(dirname(__FILE__) . '/vendor/freemius/start.php')) {
            ytp_fs();
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
            do_action('ytp_fs_loaded');
        }
    }

    require_once plugin_dir_path(__FILE__) . '/youtube-player.php';
}

// Auto-deactivate Pro version if Free version is activated
add_action('activated_plugin', function ($plugin) {
    if ($plugin === plugin_basename(__FILE__)) {
        if (!function_exists('deactivate_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $pro_plugins = array(
            'yt-player-pro/yt-player.php',
            'yt-player-pro/yt-player-pro.php',
            'yt-player-premium/yt-player.php',
            'yt-player-premium/yt-player-premium.php',
        );
        foreach ($pro_plugins as $pro_plugin) {
            if (is_plugin_active($pro_plugin)) {
                deactivate_plugins($pro_plugin);
            }
        }
    }
});


add_action('plugins_loaded', function () {
    // load_plugin_textdomain('yt-player', false, dirname(plugin_basename(__FILE__)) . '/languages'); // Removed because WP automatically loads translations since 4.6

    if (!class_exists('CSF')) {
        require_once(__DIR__ . '/vendor/codestar-framework/codestar-framework.php');
    }

    if (class_exists('YTP\\Init')) {
        YTP\Init::register_services();
    }
});

add_action('init', function () {
    // import data
    if (get_option('ytp_import_ver', '0') < YTP_IMPORT_VER) {
        $players = new WP_Query(array(
            'post_type' => 'ytplayer',
            'post_status' => 'any',
            'posts_per_page' => -1
        ));

        while ($players->have_posts()) {
            $players->the_post();
            $id = get_the_ID();
            if (!get_post_meta($id, 'isGutenberg', true)) {
                update_post_meta($id, 'isGutenberg', true);
            }
        };
    }
});

if (!function_exists('ytp_import_btn')) {
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    function ytp_import_btn($links) {
        array_unshift($links, '<a id="ytp_import_btn" href="#">Import</a>');
        return $links;
    }
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'ytp_import_btn');
