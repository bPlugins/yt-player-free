<?php 

namespace YTP\Page; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

class Settings {

    protected $prefix = 'ytp_option';
    
    public function register(){
        add_action('init', [ $this, 'action_init' ],0 );
        add_action('admin_head', [ $this, 'admin_head_css' ]);
    }

    public function admin_head_css() {
    }

    public function pro_feature_html($features, $pricing_url = '') {
        if (empty($pricing_url)) {
            $pricing_url = admin_url('edit.php?post_type=ytplayer&page=dashboard#/pricing');
        }
        $html = '
        <div class="ytp-pro-notice-box" style="margin-top: 20px; padding: 25px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
            <h4 class="ytp-pro-notice-title" style="margin: 0 0 20px 0; color: #00b2ff; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                <span>🚀</span> ' . esc_html__('Unlock Pro Features', 'yt-player') . '
            </h4>
            <ul class="ytp-pro-notice-list" style="list-style: none; padding: 0; margin: 0 0 25px 0; display: grid; grid-template-columns: repeat(1, 1fr); gap: 15px;">';
        foreach ($features as $title => $desc) {
            $html .= '
                <li style="font-size: 14px; line-height: 1.5; color: #4a5568; display: flex; align-items: baseline; gap: 10px;">
                    <span style="color: #00b2ff; font-weight: bold; font-size: 12px;">✔</span>
                    <div>
                        <strong style="color: #2d3748;">' . esc_html($title) . ':</strong> 
                        <span style="color: #718096; font-size: 13px;">' . esc_html($desc) . '</span>
                    </div>
                </li>';
        }
        $html .= '
            </ul>
            <div style="display: flex; align-items: center; gap: 15px; border-top: 1px solid #edf2f7; padding-top: 20px;">
                <a href="' . esc_url($pricing_url) . '" target="_blank" style="background: #00b2ff; color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">' . esc_html__('Upgrade to Pro Now', 'yt-player') . '</a>
            </div>
        </div>';
        return $html;
    }

    function action_init() {
        if (class_exists('\CSF')) {
            \CSF::createOptions($this->prefix, array(
                'menu_title' => 'Settings',
                'menu_slug' => 'ytp_options',
                'menu_parent' => 'edit.php?post_type=ytplayer',
                'menu_type' => 'submenu',
                'theme' => 'light',
                // 'data_type' => 'unserialize',
                'show_all_options' => false,
                'save_defaults' => true,
                // 'framework_class' => 'ytp_options',
                'framework_title' => 'Settings',
                'show_bar_menu' => false,
                // 'menu_capability' => 'edit_posts'
            ));
                $this->quickPlayer();
                $this->branding();
                $this->shortcode();
        }
    }

    /**
     * Fires after WordPress has finished loading but before any headers are sent.
     *
     */
   

    function quickPlayer(){
        \CSF::createSection($this->prefix, array(
            // 'parent' => 'ytp_playerio',
            'title' => 'Quick Player',
            'fields' => array(
                array(
                    'type' => 'content',
                    'content' => __('<div style="background-color: #e9eaec; padding: 15px 20px; margin-bottom: 20px; border-radius: 4px;">
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                                <code id="ytp-quick-shortcode" style="background: #ffffff; padding: 6px 12px; border: 1px solid #3567d6; color: #3567d6; font-size: 14px; border-radius: 2px;">[ytp]Your Video URL[/ytp]</code>
                                <button type="button" style="background: transparent; border: none; cursor: pointer; padding: 0; color: #1e1e1e; display: flex; align-items: center;" onclick="var text = \'[ytp]Your Video URL[/ytp]\'; var codeEl = document.getElementById(\'ytp-quick-shortcode\'); var showFeedback = function(){ var oldText = codeEl.innerText; codeEl.innerText = \'Copied!\'; setTimeout(function(){ codeEl.innerText = oldText; }, 2000); }; if(navigator.clipboard && navigator.clipboard.writeText){ navigator.clipboard.writeText(text).then(showFeedback).catch(function(){ var ta = document.createElement(\'textarea\'); ta.value = text; document.body.appendChild(ta); ta.select(); document.execCommand(\'copy\'); document.body.removeChild(ta); showFeedback(); }); } else { var ta = document.createElement(\'textarea\'); ta.value = text; document.body.appendChild(ta); ta.select(); document.execCommand(\'copy\'); document.body.removeChild(ta); showFeedback(); }" title="Copy to clipboard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                </button>
                            </div>
                            <span style="font-size: 14px; color: #3c434a; display: block;">Simply copy the shortcode above and place your YouTube video URL between the <strong>[ytp]</strong> and <strong>[/ytp]</strong> tags. You can paste it in any post or page.</span>
                        </div>', 'yt-player'),
                ),
                array(
                    'id' => 'controls',
                    'type' => 'button_set',
                    'title' => 'Controls',
                    'desc' => 'Select control bar elements to display in the player.',
                    'multiple' => true,
                    'options' => array(
                      'play-large' => 'Play Large',
                      'play' => 'Play',
                      'progress' => 'Progressbar',
                      'duration' => 'Duration',
                      'current-time' => 'Current Time',
                      'mute' => 'Mute Button',
                      'volume' => 'Volume Control',
                      'fullscreen' => 'Fullscreen'
                    ),
                    'default' => ['play-large', 'play', 'progress', 'duration', 'current-time', 'mute', 'volume', 'fullscreen']
                ),
                array(
                    'id' => 'width',
                    'type' => 'dimensions',
                    'title' => 'Player Width',
                    'desc' => 'Set player container width in percentage (%) or pixels (px).',
                    'height' => false,
                    'default' => [
                        'unit' => '%',
                        'width' => 100,
                    ]
                ),
                array(
                    'type'    => 'content',
                    'content' => $this->pro_feature_html(array(
                        'Restart, Rewind & Fast Forward Controls' => 'Add restart, rewind, and fast-forward buttons to the control bar.',
                        'Custom Seek Time'    => 'Set custom seek duration in seconds when rewinding or fast forwarding.',
                        'Auto Hide Control'   => 'Automatically hide player control bar during video playback.',
                        'Hide YouTube UI'     => 'Hide default YouTube interface and branding elements.',
                    ))
                )
            ),
        ));
    }

    public function branding(){
        \CSF::createSection($this->prefix, array(
            // 'parent' => 'ytp_playerio',
            'title' => 'Branding',
            'fields' => array(
                array(
                    'id' => 'brandColor',
                    'type' => 'color',
                    'title' => 'Brand Color',
                    'default' => '#00AFFA'
                ),
            ),
        ));
    }
    
    public function shortcode(){
        \CSF::createSection($this->prefix, array(
            // 'parent' => 'ytp_playerio',
            'title' => 'Shortcode',
            'fields' => array(
                array(
                    'id' => 'gutenbergEnabled',
                    'type' => 'switcher',
                    'title' => 'Gutenberg Enabled for Shortcode Generator',
                    'default' => false
                ),
            ),
        ));
    }
}