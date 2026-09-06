<?php
namespace YTP\Page; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound
class Dashboard{

  public function register(){
    add_action( 'admin_enqueue_scripts', [$this, 'dashboardEnqueueScripts'] );
      add_action('admin_menu', [$this, 'admin_menu']);
  }

  function dashboardEnqueueScripts( $hook ) {
  
    if( str_contains( $hook, 'ytplayer' ) ){
      wp_enqueue_style( 'yt-player-dashboard-style', YTP_PLUGIN_DIR . 'build/dashboard.css', [], YTP_PLUGIN_VERSION );

      wp_enqueue_script( 'yt-player-dashboard-script', YTP_PLUGIN_DIR . 'build/dashboard.js', [ 'react', 'react-dom',  'wp-components', 'wp-i18n', 'wp-api', 'wp-util' ,'lodash', 'wp-media-utils' ,'wp-data','wp-core-data','wp-api-request' ], YTP_PLUGIN_VERSION, true );
    }
  }

  public function admin_menu(){
      add_submenu_page( 'edit.php?post_type=ytplayer', 'Demo & Help', 'Demo & Help', 'manage_options', 'dashboard', [$this, 'dashboard_page_callback'], 15 );
  }


  function dashboard_page_callback() {
      ?>
          <div id="ytPlayerDashboard"
                data-info="<?php echo esc_attr(wp_json_encode([
                'version'=>YTP_PLUGIN_VERSION,
                'isPremium' => false,
                'hasPro'               => false,
                'licenseActiveNonce'   => wp_create_nonce('bPlLicenseActivation'),
                'adminUrl'             => admin_url(),
              ]))?>"></div>
      <?php }
}