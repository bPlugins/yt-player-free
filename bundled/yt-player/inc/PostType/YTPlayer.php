<?php
namespace YTP\PostType; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

class YTPlayer{
    protected static $_instance = null;
    protected $post_type = 'ytplayer';

    /**
     * construct function
     */
    public function register(){
        add_action('init', [$this, 'init']);
        add_action('init', [$this, 'register_field'], 0);
        if ( is_admin() ) {
            add_filter( 'post_row_actions', [$this, 'remove_row_actions'], 10, 2 );
            add_action('edit_form_after_title', [$this, 'edit_form_after_title']);
            add_filter('manage_ytplayer_posts_columns', [$this, 'columns_head_only'], 10);
            add_action('manage_ytplayer_posts_custom_column', [$this, 'column_content'], 10, 2);
            add_filter('post_updated_messages', [$this, 'updated_messages']);

            add_action('admin_head-post.php', [$this, 'hide_publish_actions']);
            add_action('admin_head-post-new.php', [$this, 'hide_publish_actions']);	
            add_filter( 'gettext', [$this, 'pdfp_change_publish_button'], 10, 2 );

            add_filter( 'admin_footer_text',[$this, 'ytp_admin_footer']);

            
            add_filter( 'filter_block_editor_meta_boxes', [$this, 'remove_metabox'] );
            add_action('use_block_editor_for_post', [$this, 'forceGutenberg'], 10, 2);

            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

            
        }
    }

    public function enqueue_admin_scripts() {
        global $typenow;
        if ($typenow === 'ytplayer') { 
            wp_enqueue_script(
                'ytp-admin-copy',
                YTP_PLUGIN_DIR . 'build/post.js',
                ['jquery'],
                '1.0',
                true
            );
        }
    }

    function register_field(){
        if (class_exists('\CSF')) {
            $prefix = '_ytp';
            \CSF::createMetabox($prefix, array(
                'title'     => 'YouTube Player Options',
                'post_type' => 'ytplayer',
                'data_type' => 'serialize',
                'context'   => 'normal',
                'priority'  => 'high',
                'nav'       => 'normal',
                'theme'     => 'light',
            ));
            
            $this->configure($prefix);
            // $this->controls();
            // $this->branding();
            // $this->endscreen();
        }
    }


    function ytp_admin_footer( $text ) {
        if ( 'ytplayer' == get_post_type() ) {
            $url = 'https://wordpress.org/support/plugin/yt-player/reviews/#new-post';
            /* translators: 1: Review URL */
            $text = sprintf( wp_kses_post( __( 'If you like <strong>YT Player</strong> please leave us a <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'yt-player' ) ), esc_url( $url ) );
        }
        return $text;
    }


    /**
     * init
     */
    public function init(){
        register_post_type( 'ytplayer',
            array(
                'label' => __('YT Player', 'yt-player'),
                'labels' => array(
                    'name' => __( 'YT Players', 'yt-player'),
                    'singular_name' => __( 'YT Player', 'yt-player' ),
                    'menu_name' => __('YT Player', 'yt-player'),
                    'all_items' => __('ShortCode Generator', 'yt-player'),
                    'add_new' => __('Add New ShortCode', 'yt-player'),
                    'add_new_item' => __( 'Add new shortCode', 'yt-player' ),
                    'edit_item' => __( 'Edit', 'yt-player' ),
                    'new_item' => __( 'New', 'yt-player' ),
                    'view_item' => __( 'View', 'yt-player' ),
                    'search_items'       => __( 'Search', 'yt-player'),
                    'not_found' => __( 'Sorry, we couldn\'t find any item you are looking for.', 'yt-player' )
                ),
                'public' => false,
                'show_ui' => true, 									
                'publicly_queryable' => true,
                'exclude_from_search' => true,
                'menu_position' => 14,
                'menu_icon' => set_url_scheme( YTP_PLUGIN_DIR . 'assets/img/icon.png' ),
                'has_archive' => false,
                'hierarchical' => false,
                'capability_type' => 'page',
                'rewrite' => array( 'slug' => 'ytplayer' ),
                'show_in_rest' => true,
                'supports' => array( 'title', 'editor' ),
                'template' => [
                    ['yt-player/parent']
                ],
                'template_lock' => 'all',
            )
        );
    }

    /**
     * Remove Row
     */
    function remove_row_actions( $idtions ) {
        global $post;
        if( $post->post_type == $this->post_type ) {
            unset( $idtions['view'] );
            unset( $idtions['inline hide-if-no-js'] );
        }
        return $idtions;
    }

    function edit_form_after_title() {
        global $post;
        if ($post->post_type == $this->post_type) {
            $id = $post->ID;
            $shortcode = "[ytplayer id='" . esc_attr($id) . "']";
        ?>
        <div class="ytp_shortcode_box_after_title">
            <label><?php esc_html_e('Copy and paste this shortcode into your posts, pages and widget', 'yt-player'); ?></label>
            <div class="shortcode_area">
                <button class="button button-bplugins button-large ytp_shortcode_copy_btn"
                        data-shortcode="<?php echo esc_attr($shortcode) ?>"><span class="copy-text"><?php echo esc_html($shortcode); ?></span></button>
                <svg class='ytp_shortcode_copy_btn' data-type="icon"
                     data-shortcode='<?php echo esc_attr($shortcode) ?>'
                     width='22px' height='22px' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <path
                            d='M8 4V16C8 17.1046 8.89543 18 10 18L18 18C19.1046 18 20 17.1046 20 16V7.24162C20 6.7034 19.7831 6.18789 19.3982 5.81161L16.0829 2.56999C15.7092 2.2046 15.2074 2 14.6847 2H10C8.89543 2 8 2.89543 8 4Z'
                            stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/>
                    <path d='M16 18V20C16 21.1046 15.1046 22 14 22H6C4.89543 22 4 21.1046 4 20V9C4 7.89543 4.89543 7 6 7H8'
                          stroke='#000000' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/>
                </svg>
            </div>
        </div>
        <?php
        }
    }
    
    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    function columns_head_only($defaults) {
        unset($defaults['date']);
        $defaults['shortcode'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }

    function column_content($column_name, $post_ID)
{
    if ($column_name == 'shortcode') {
        echo '
        <div id="bPlAdminShortcode-' . esc_attr($post_ID) . '" class="ytp_front_shortcode" style="position: relative; display: inline-block;">
            <input 
                readonly 
                style="text-align: center; border: none; outline: none; background-color: #1e8cbe; color: #fff; padding: 4px 10px; border-radius: 3px; cursor: pointer;" 
                value="[ytplayer id=' . esc_attr($post_ID) . ']" 
                onclick="copyBPlAdminShortcode(' . esc_attr($post_ID) . ')" 
            />
            <span class="tooltip" style="position: absolute; top: -30px; left: 50%; transform: translateX(-50%); background: #000; color: #fff; padding: 3px 6px; border-radius: 3px; font-size: 12px; visibility: hidden; opacity: 0; transition: opacity 0.3s;">
                Copy to Clipboard
            </span>
        </div>';
    }
}
    
    
    function updated_messages( $messages ) {
        $messages[$this->post_type][1] = __('updated ', 'yt-player');
        return $messages;
    }

    public function hide_publish_actions(){
        global $post;
        if($post->post_type == $this->post_type){
            echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                    .csf-field-button_set .csf-fieldset > div,
                    .csf-field-button_set ul {
                        display: flex !important;
                        flex-wrap: wrap !important;
                        gap: 8px !important;
                    }
                    .csf-field-button_set label {
                        margin: 0 !important;
                        border-radius: 4px !important;
                    }
                </style>
            ';
        }
    }

    function remove_metabox($metaboxs) {
        global $post;
        $screen = get_current_screen();

        if($screen->post_type === $this->post_type){
            return false;
        }
        return $metaboxs;
    }

    public function forceGutenberg($use, $post) {
        if ($this->post_type === $post->post_type) {
            $gutenberg = (bool) \YTP\Helper\Utils::getOptionDeep('ytp_option', 'gutenbergEnabled', false);
            if (!$gutenberg) {
                remove_post_type_support($this->post_type, 'editor');
                return false;
            } 
            return true;
        }
        return $use;
    }

    function pdfp_change_publish_button( $translation, $text ) {
        if ( $this->post_type == get_post_type())
        if ( $text == 'Publish' )
            return 'Save';
        return $translation;
    }

    private function get_pro_badge($text = 'PRO') {
        return ' <span style="background:#00b2ff;color:#fff;padding:0px 5px;border-radius:4px;font-size:10px;font-weight:bold;margin-left:5px;vertical-align:middle;display:inline-block;line-height:1.4;">' . esc_html($text) . '</span>';
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

    public function configure($prefix){

        \CSF::createSection($prefix, array(
            'title'  => 'General',
            'icon'   => 'fas fa-cog',
            'fields' => array(
                array(
                    'id' => 'source',
                    'title' => 'Video URL/ID',
                    'type'  => 'text',
                    'desc' => 'Enter a valid YouTube video URL or video ID (e.g., https://www.youtube.com/watch?v=... or dQw4w9WgXcQ).'
                ),
                array(
                    'id' => 'width',
                    'type' => 'dimensions',
                    'title' => 'Player Width',
                    'desc' => 'Set the width of the video player container in percentage (%) or pixels (px).',
                    'height' => false,
                    'default' => [
                        'unit' => '%',
                        'width' => 100,
                    ]
                ),
                array(
                    'type'    => 'content',
                    'content' => $this->pro_feature_html(array(
                        'Video Loop & Auto Play'      => 'Automatically loop playback and auto-start video when loaded.',
                        'Default Muted & Seek Time'   => 'Mute audio output by default and customize rewind/fast-forward seek duration.',
                        'Auto-Hide Controls & Click'  => 'Hide control bar automatically during playback and enable click-to-play.',
                        'Hide YouTube UI'             => 'Hide default YouTube interface elements for a cleaner video presentation.',
                    ))
                )
            )
        ));

        \CSF::createSection($prefix, array(
            'title'  => 'Branding' . $this->get_pro_badge(),
            'icon'   => 'fas fa-paint-brush',
            'fields' => array(
                array(
                    'type'    => 'content',
                    'content' => $this->pro_feature_html(array(
                        'Custom Brand Logo Overlay' => 'Display your custom brand logo on top of the video with flexible positioning, width, and border radius.',
                        'Custom Video Thumbnail'    => 'Replace default YouTube poster images with your own custom uploaded thumbnail banner.',
                    ))
                )
            )
        ));

        \CSF::createSection($prefix, array(
            'title'  => 'Controls & UI',
            'icon'   => 'fas fa-sliders-h',
            'fields' => array(
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
                    'type'    => 'content',
                    'content' => $this->pro_feature_html(array(
                        'Restart, Rewind & Fast Forward Controls' => 'Add restart, rewind, and fast-forward buttons to the control bar.',
                        'Pause State Controls'                    => 'Display custom thumbnail image or hide control bar when video is paused.',
                        'Custom Player & Button Styling'          => 'Set custom corner radius for the player and adjust play button color, icon size, padding, and border radius.',
                    ))
                )
            )
        ));
    }
    

}
