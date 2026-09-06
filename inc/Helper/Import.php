<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound
namespace YTP\Helper;

use YTP\Helper\Utils;

class Import{

    public static function meta(){
        $players = new \WP_Query([
            'post_type' => 'ytplayer',
            'posts_per_page' => -1
        ]);
        
        foreach($players->posts as $player){
            if(get_post_meta($player->ID, '_ytp', true)){
                break;
            }
            $oldMeta = self::getOldMeta($player->ID);
            $data = [
                'source' => $oldMeta['source'],
                'width' => in_array($oldMeta['width'], ['', '0', ' ', 0]) ? ['width' => 100, 'unit' => '%'] : ['width' => $oldMeta['width'], 'unit' => 'px'],
                'controls' => self::getControls($oldMeta),
                'autoplay' => $oldMeta['autoplay'],
                'muted' => '0',
                'seekTime' => $oldMeta['seekTime'],
                'startTime' => $oldMeta['startTime'],
                'disableContextMenu' => $oldMeta['disableContextMenu'],
                'hideControls' => $oldMeta['hideControls'],
                'clickToPlay' => $oldMeta['clickToPlay'],
                'hideYoutubeUI' => '0'
            ];

            update_post_meta($player->ID, '_ytp', $data);
        }

    }

    public static function option(){
        if ( ytp_fs()->is__premium_only() ) {
            if(get_option('ytp_option')){
                return false;
            }
    
            $brand = get_option('ytp_color', ['color_two' => '#00affa']);
    
            $oldOption = self::getOldOptions();
            $oldOption['width'] = in_array($oldOption['width'], ['', '0', ' ', 0]) ? ['width' => 100, 'unit' => '%'] : ['width' => $oldOption['width'], 'unit' => 'px'];
            $oldOption['controls'] = self::getControls($oldOption);
            $oldOption['brandColor'] = $brand['color_two'];
    
            update_option( 'ytp_option', $oldOption );
        }

    }

    public static function getOldMeta($id){
        $data = [
            'source' => self::getMeta($id, '_ytp_video_id', '0'),
            'width' => self::getMeta($id, '_ytp_video_width', '0'),
            'controls' => [
                'play-large' => self::getMeta($id, '_ytp_large_play', '0'),
                'play' => self::getMeta($id, '_ytp_play', 'off'),
                'progress' => self::getMeta($id, '_ytp_progress_bar', 'off'),
                'duration' => self::getMeta($id, '_ytp_duration', 'off'),
                'mute' => self::getMeta($id, '_ytp_mute_button', 'off'),
                'volume' => self::getMeta($id, '_ytp_volume_control', 'off'),
                'settings' => self::getMeta($id, '_ytp_setting', 'off'),
                'fullscreen' => self::getMeta($id, '_ytp_video_fs', 'off'),
            ],
            'autoplay' => self::getMeta($id, '_ytp_autoplay', 'off'),
            'clickToPlay' => self::getMeta($id, '_ytp_click2play', 'off'),
            'disableContextMenu' => self::getMeta($id, '_ytp_disableContextMenu', 'off'),
            'seekTime' => self::getMeta($id, '_ytp_seektime', 'off'),
            'startTime' => self::getMeta($id, '_ytp_start_time', 'off'),
            'hideControls' => self::getMeta($id, '_ytp_hide_control', 'off'),
        ];

        if ( ytp_fs()->is__premium_only() ) {
            $data['controls'] = [
                'play-large' => self::getMeta($id, '_ytp_large_play', '0'),
                'restart' => self::getMeta($id, '_ytp_restart', 'off'),
                'rewind' => self::getMeta($id, '_ytp_rewind', 'off'),
                'play' => self::getMeta($id, '_ytp_play', 'off'),
                'fast-forward' => self::getMeta($id, '_ytp_fast_forward', 'off'),
                'progress' => self::getMeta($id, '_ytp_progress_bar', 'off'),
                'duration' => self::getMeta($id, '_ytp_duration', 'off'),
                'current-time' => self::getMeta($id, '_ytp_current_time', 'off'),
                'mute' => self::getMeta($id, '_ytp_mute_button', 'off'),
                'volume' => self::getMeta($id, '_ytp_volume_control', 'off'),
                'settings' => self::getMeta($id, '_ytp_setting', 'off'),
                'fullscreen' => self::getMeta($id, '_ytp_video_fs', 'off'),
            ];
        }

        return $data;

    }

    public static function getOldOptions(){
        if ( ytp_fs()->is__premium_only() ) {
            $oldOption = get_option('ytp_quick', []);
            return [
                'width' => Utils::isset($oldOption, 'width', '0'),
                'controls' => [
                    'play-large' => Utils::isset($oldOption, 'large_play', 'off'),
                    'restart' => Utils::isset($oldOption, 'restart_button', 'off'),
                    'rewind' => Utils::isset($oldOption, 'rewind_button', 'off'),
                    'play' => Utils::isset($oldOption, 'play_button', 'off'),
                    'fast-forward' => Utils::isset($oldOption, 'fast_forward_button', 'off'),
                    'progress' => Utils::isset($oldOption, 'progress_bar', 'off'),
                    'duration' => Utils::isset($oldOption, 'duration', 'off'),
                    'current-time' => Utils::isset($oldOption, 'current_time', 'off'),
                    'mute' => Utils::isset($oldOption, 'mute_button', 'off'),
                    'volume' => Utils::isset($oldOption, 'volume_hide', 'off'),
                    'settings' => Utils::isset($oldOption, 'settings', 'off'),
                    'fullscreen' => Utils::isset($oldOption, 'fullscreen', 'off'),
                ],
                'autoplay' => Utils::isset($oldOption, 'autoplay', 'off') === 'on' ? '1' : '0',
                'clickToPlay' => Utils::isset($oldOption, 'click2play', 'off') === 'on' ? '1' : '0',
                'disableContextMenu' => Utils::isset($oldOption, 'disableContextMenu', 'off') === 'on' ? '1' : '0',
                'seekTime' => Utils::isset($oldOption, 'seek_time', 10),
                'hideControls' => Utils::isset($oldOption, 'hide_control', 'off') === 'on' ? '1' : '0',
            ];
        }

        
    }

    public static function getControls($meta){
        $controls = [];
        foreach($meta['controls'] as $key => $control){
            if($control !== 'on'){
                $controls[] = $key;
            }
        }
        return $controls;
        return wp_parse_args( ['pipe', 'airplay'], $controls );
    }

    public static function getMeta($id, $meta, $default = false){
        $meta = get_post_meta($id, $meta, true);
        return $meta;
    }
}