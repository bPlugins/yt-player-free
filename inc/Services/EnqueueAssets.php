<?php
namespace YTP\Services; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

class EnqueueAssets {
    
    public function register(){
        add_action('admin_enqueue_scripts', [$this, 'adminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'publicAssets']);
        add_action('enqueue_block_editor_assets', [$this, 'editorAssets']);
    }

    public function editorAssets(){
        wp_enqueue_style( 'ytp-style', YTP_PLUGIN_DIR . 'public/css/plyr-v3.7.8.css', array(), YTP_PLUGIN_VERSION, 'all' );
        wp_enqueue_script( 'ytp-js', YTP_PLUGIN_DIR  . 'public/js/plyr-v3.7.8.js',[], YTP_PLUGIN_VERSION, true );
    }

    public function adminAssets(){
        $page = get_current_screen();

        if($page && (isset($page->post_type) && $page->post_type === 'ytplayer' || isset($page->base) && $page->base === 'plugins')){
            wp_enqueue_style('ytp-admin', YTP_PLUGIN_DIR.'assets/css/admin.css', [], YTP_PLUGIN_VERSION);
            wp_enqueue_script('ytp-admin', YTP_PLUGIN_DIR.'assets/js/script.js', [], YTP_PLUGIN_VERSION, true);

            wp_localize_script( 'ytp-admin', 'ytpAdmin', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce( 'ytp-nonce' ),
            ]);
        }

    }

    public function publicAssets(){
        // Have to uncomment this for shortcode view
        
        wp_enqueue_style( 'ytp-style', YTP_PLUGIN_DIR . 'public/css/plyr-v3.7.8.css', array(), YTP_PLUGIN_VERSION, 'all' );
        wp_enqueue_script( 'ytp-js', YTP_PLUGIN_DIR  . 'public/js/plyr-v3.7.8.js',[], YTP_PLUGIN_VERSION, true );
        wp_enqueue_script( 'ytp-frontend', YTP_PLUGIN_DIR  . 'build/frontend.js', ['ytp-js'], YTP_PLUGIN_VERSION, true );
    }

}