<?php
namespace YTP\Admin; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class YT_Player_PromoBanner {
	private $notice_id = 'ytp_black_friday_2026_notice';
	private $option_key = 'ytp_black_friday_2026_dismissed';
	private $start_date = '2026-11-01 00:00:00';
	private $end_date   = '2026-12-05 23:59:59';
	private $dashboard_url = 'https://bplugins.com/products/yt-player/pricing/?utm_source=admin&utm_medium=dashboard&utm_campaign=bfcm2026';
	private $plugins_url   = 'https://bplugins.com/products/yt-player/pricing/?utm_source=admin&utm_medium=plugins&utm_campaign=bfcm2026';

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'display_banner' ) );
		add_action( 'admin_head', array( $this, 'admin_head_css' ) );
		add_action( 'wp_ajax_ytp_dismiss_black_friday_notice', array( $this, 'ajax_dismiss_notice' ) );
		add_filter( 'plugin_action_links', array( $this, 'add_action_links' ), 10, 2 );
	}

	private function is_pro() {
		if ( function_exists( 'ytp_fs' ) && ytp_fs()->can_use_premium_code() ) {
			return true;
		}
		if ( defined( 'YTP_HAS_PRO' ) && YTP_HAS_PRO ) {
			return true;
		}
		if ( file_exists( dirname( __FILE__ ) . '/../admin/premium-files' ) || file_exists( dirname( __FILE__ ) . '/premium-files' ) ) {
			return true;
		}
		return false;
	}

	private function is_promo_active() {
		if ( $this->is_pro() ) {
			return false;
		}
		$current_time = current_time( 'timestamp' );
		$start_time   = strtotime( $this->start_date );
		$end_time     = strtotime( $this->end_date );

		return ( $current_time >= $start_time && $current_time <= $end_time );
	}

	private function is_dismissed() {
		$user_id = get_current_user_id();
		if ( get_user_meta( $user_id, $this->option_key, true ) ) {
			return true;
		}
		return (bool) get_option( $this->option_key, false );
	}

	private function is_target_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( function_exists( 'is_block_editor' ) && is_block_editor() ) {
			return false;
		}
		if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_SERVER['REQUEST_URI'] ) && ( strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'post-new.php' ) !== false || strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'post.php' ) !== false ) ) {
			return false;
		}

		// Strictly check if current screen belongs to YT Player
		if ( $screen ) {
			if ( 'ytplayer' === $screen->post_type ) {
				return true;
			}
			$screen_id = $screen->id ?? '';
			if ( strpos( $screen_id, 'ytplayer' ) !== false || strpos( $screen_id, 'ytp_options' ) !== false ) {
				return true;
			}
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['post_type'] ) && 'ytplayer' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {
			return true;
		}

		if ( isset( $_GET['page'] ) ) {
			$page = sanitize_key( wp_unslash( $_GET['page'] ) );
			if ( in_array( $page, array( 'dashboard', 'ytp_options' ), true ) ) {
				// Only if post_type is ytplayer or hook relates to ytplayer
				if ( ( isset( $_GET['post_type'] ) && 'ytplayer' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) || ( $screen && ( 'ytplayer' === $screen->post_type || strpos( $screen->id, 'ytplayer' ) !== false ) ) ) {
					return true;
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		return false;
	}

	public function admin_head_css() {
		if ( ! $this->is_promo_active() || $this->is_dismissed() || ! $this->is_target_screen() ) {
			return;
		}
		?>
		<style id="ytp-promo-banner-override-css">
		#<?php echo esc_attr( $this->notice_id ); ?>,
		#<?php echo esc_attr( $this->notice_id ); ?>.notice,
		div#<?php echo esc_attr( $this->notice_id ); ?>.notice.notice-info,
		#<?php echo esc_attr( $this->notice_id ); ?>.ytp-bf-promo-banner {
			display: block !important;
			position: relative !important;
			top: auto !important;
			left: auto !important;
			width: auto !important;
			height: auto !important;
			opacity: 1 !important;
			visibility: visible !important;
			margin: 16px 20px 20px 0 !important;
			padding: 0 !important;
			z-index: 99 !important;
			clear: both !important;
		}
		</style>
		<?php
	}

	public function display_banner() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( ! $this->is_promo_active() ) {
			return;
		}
		if ( $this->is_dismissed() ) {
			return;
		}
		if ( ! $this->is_target_screen() ) {
			return;
		}

		$logo_url = defined( 'YTP_PLUGIN_DIR' ) ? YTP_PLUGIN_DIR . 'assets/images/promo-icon.png' : plugins_url( 'assets/images/promo-icon.png', dirname( __FILE__ ) );
		$nonce    = wp_create_nonce( 'ytp_dismiss_bf_notice_nonce' );
		?>
		<div id="<?php echo esc_attr( $this->notice_id ); ?>" class="notice inline notice-info ytp-bf-promo-banner" style="position: relative; padding: 0; border: none; border-radius: 8px; margin: 16px 20px 20px 0; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.06); overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; display: block !important; opacity: 1 !important; visibility: visible !important;">
			<div style="display: flex; align-items: stretch; min-height: 120px;">
				<div style="background: linear-gradient(135deg, #e11d48 0%, #1e1124 100%); display: flex; align-items: center; justify-content: center; padding: 0 30px; flex-shrink: 0; min-width: 140px;">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="YT Player Logo" style="width: 72px; height: 72px; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.2));" />
				</div>
				<div style="padding: 16px 45px 16px 24px; flex-grow: 1; display: flex; flex-direction: column; justify-content: center;">
					<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
						<span style="font-size: 15.5px; font-weight: 700; color: #111827; letter-spacing: -0.2px;">YT Player's Biggest Black Friday Sale is Live – Up to 45% OFF!</span>
					</div>
					<p style="margin: 0 0 10px 0; color: #4b5563; font-size: 12.5px; line-height: 1.5; max-width: 950px;">
						The biggest deal of the year is here! This is your once-a-year opportunity to upgrade to YT Player Pro at an exclusive price.<br>
						Unlock floating sticky player, custom brand logo overlay, custom thumbnails, and everything you need for YouTube videos!
					</p>
					<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-bottom: 12px; font-size: 12px; color: #374151;">
						<span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 500;"><span style="color: #10b981; font-weight: 800;">✓</span> Floating &amp; Sticky Player</span>
						<span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 500;"><span style="color: #10b981; font-weight: 800;">✓</span> Custom Brand Logo Overlay</span>
						<span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 500;"><span style="color: #10b981; font-weight: 800;">✓</span> Custom Banner Thumbnails</span>
						<span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 500;"><span style="color: #10b981; font-weight: 800;">✓</span> Hide YouTube UI &amp; Branding</span>
					</div>
					<div style="display: flex; align-items: center; gap: 12px;">
						<a href="<?php echo esc_url( $this->dashboard_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; justify-content: center; background: #e11d48; color: #fff; font-weight: 600; font-size: 12.5px; padding: 7px 18px; border-radius: 6px; text-decoration: none; box-shadow: 0 2px 6px rgba(225, 29, 72, 0.35); transition: background 0.2s ease;">
							Unlock PRO Features &rarr;
						</a>
					</div>
				</div>
			</div>
			<button type="button" class="ytp-promo-dismiss" style="position: absolute; top: 12px; right: 14px; background: transparent; border: none; cursor: pointer; color: #9ca3af; font-size: 18px; line-height: 1; padding: 4px;" title="Dismiss notice">
				<span class="dashicons dashicons-no-alt" style="font-size: 18px; width: 18px; height: 18px;"></span>
			</button>
		</div>

		<script>
		(function() {
			var banner = document.getElementById('<?php echo esc_js( $this->notice_id ); ?>');
			if (!banner) return;
			var dismissBtn = banner.querySelector('.ytp-promo-dismiss');
			if (!dismissBtn) return;
			dismissBtn.addEventListener('click', function(e) {
				e.preventDefault();
				banner.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
				banner.style.opacity = '0';
				banner.style.transform = 'translateY(-6px)';
				setTimeout(function() {
					banner.remove();
				}, 260);

				var formData = new FormData();
				formData.append('action', 'ytp_dismiss_black_friday_notice');
				formData.append('nonce', '<?php echo esc_js( $nonce ); ?>');
				fetch(ajaxurl, {
					method: 'POST',
					body: formData
				});
			});
		})();
		</script>
		<?php
	}

	public function ajax_dismiss_notice() {
		check_ajax_referer( 'ytp_dismiss_bf_notice_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Unauthorized', 403 );
		}

		$user_id = get_current_user_id();
		update_user_meta( $user_id, $this->option_key, true );
		update_option( $this->option_key, true );

		wp_send_json_success();
	}

	public function add_action_links( $links, $file ) {
		if ( ! $this->is_promo_active() ) {
			return $links;
		}

		$target_files = array(
			'yt-player/yt-player.php',
			'yt-player-premium/yt-player.php',
			'yt-player-pro/yt-player.php',
		);

		if ( in_array( $file, $target_files, true ) ) {
			$promo_link = sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer" style="color: #e11d48; font-weight: 700;">%s</a>',
				esc_url( $this->plugins_url ),
				esc_html__( 'Black Friday Sale – 45% OFF', 'yt-player' )
			);
			$links['ytp_black_friday'] = $promo_link;
		}

		return $links;
	}
}

new YT_Player_PromoBanner();
