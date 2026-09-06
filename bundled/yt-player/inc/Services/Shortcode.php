<?php
namespace YTP\Services; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

use YTP\Helper\Utils;


class Shortcode {

    public function register(){
        add_shortcode('ytp', [$this, 'ytp']);
        add_shortcode('ytplayer', [$this, 'ytplayer']);
    }

    public function ytp($atts, $content){
        extract( shortcode_atts( array(
            'url' => null,
            'autoplay' => false
        ), $atts ) ); 

        wp_enqueue_style('ytp-style');
        wp_enqueue_script('ytp-frontend');

        Ob_start(); 
    
        $raw_content = trim(wp_strip_all_tags($content));
        $video_id = '';
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/s]{11})/', $raw_content, $matches)) {
            $video_id = $matches[1];
        } else if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $raw_content)) {
            $video_id = $raw_content;
        }

        $selector = uniqid();
        
        $width = Utils::getOptionDeep('ytp_option','width',['width' => 100, 'unit' => '%']);
        $controls = Utils::getOptionDeep('ytp_option', 'controls', []);
        $hideYoutubeUI = Utils::getOptionDeep('ytp_option', 'hideYoutubeUI', false);

        $data_options = [
            'controls' => is_array($controls) ? array_values($controls) : [],
            'autoplay' => (bool) $autoplay
        ];
        
        ?>
        <style>
            <?php echo esc_attr("#player$selector"); ?>{
                width: <?php echo esc_html($width['width'].$width['unit']); ?>;
                max-width: 100%;
            }
            <?php if($hideYoutubeUI){ ?>
                <?php echo esc_attr("#player$selector"); ?> .plyr__video-wrapper iframe{
                    position: absolute !important;
                    top: -50% !important;
                    height: 200% !important;
                }
            <?php } ?>
        </style>    
        <div>
            <div id="player<?php echo esc_attr($selector); ?>" class="ytp-player" data-options="<?php echo esc_attr(wp_json_encode($data_options)); ?>">
                <div class="plyr__video-embed embed-container" id="player">
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>"
                        allowfullscreen
                        allowtransparency
                        allow="autoplay"
                    ></iframe>
                </div>
            </div>
        </div>
        <?php 
        $output=ob_get_clean(); return $output; 
    }

    public function ytplayer($atts){
        extract( shortcode_atts( array(
            'id' => null,
        ), $atts ) ); 

        if (empty($id)) {
            return false;
        }

        $post = get_post($id);
        if ($post && has_blocks($post->post_content)) {
            return do_blocks($post->post_content);
        }

        $option = get_post_meta($id, '_ytp', true);
        if(!is_array($option)){
            return false;
        }
        
        Ob_start(); 
        $is_premium = function_exists('ytp_fs') && ytp_fs()->can_use_premium_code();
        if (isset($option['controls']) && is_array($option['controls'])) {
            $option['controls'] = $is_premium ? $option['controls'] : array_values(array_diff($option['controls'], ['restart', 'rewind', 'fast-forward']));
        }
        $width = $option['width']['width']. $option['width']['unit'];
       
        wp_enqueue_style('ytp-style');
        wp_enqueue_script('ytp-frontend');
        ?>
        <style>
            <?php echo esc_attr("#player$id"); ?>{
                width: <?php echo esc_html($width); ?>
                /* margin: 0 auto */
            }
            <?php if(isset($option['hideYoutubeUI']) && $option['hideYoutubeUI']){ ?>
                <?php echo esc_attr("#player$id"); ?> .plyr__video-wrapper iframe{
                    position: absolute !important;
                    top: -50% !important;
                    height: 200% !important;
                }
            <?php } ?>
        </style>
        <div>
            <?php 
                $data_options = [
                    'controls' => isset($option['controls']) && is_array($option['controls']) ? array_values($option['controls']) : [],
                    'hideControls' => isset($option['hideControls']) ? (bool) $option['hideControls'] : true,
                    'seekTime' => isset($option['seekTime']) ? (int) $option['seekTime'] : 10,
                    'clickToPlay' => isset($option['clickToPlay']) ? (bool) $option['clickToPlay'] : true,
                    'disableContextMenu' => isset($option['disableContextMenu']) ? (bool) $option['disableContextMenu'] : true,
                    'autoplay' => isset($option['autoplay']) ? (bool) $option['autoplay'] : false,
                    'muted' => isset($option['muted']) ? (bool) $option['muted'] : false,
                    'loop' => ['active' => isset($option['loop']) ? (bool) $option['loop'] : false]
                ];
            ?>
            <div id="player<?php echo esc_attr($id); ?>" class="ytp-player" data-options="<?php echo esc_attr(wp_json_encode($data_options)) ?>">
                <div class="plyr__video-embed embed-container" id="player">
                    <iframe
                        src="https://www.youtube.com/embed/<?php echo esc_attr($option['source']); ?>"
                        allowfullscreen
                        allowtransparency
                        allow="autoplay"
                    ></iframe>
                </div>
               
            </div>
        </div>
        <?php $output=ob_get_clean(); return $output; 
    }
}