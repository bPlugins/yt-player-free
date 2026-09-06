<?php
namespace YTP\Base; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

use YTP\Model\Presets as PresetModel;

class Presets {

    private $preset_key = 'ytp_preset_inserted';

    function register(){
        $presetInserted = get_option($this->preset_key);
        if(!$presetInserted){
            $presets = $this->getPresets();
            $presetModelObject = new PresetModel();
            foreach($presets as $preset){
                $preset['preset'] = maybe_unserialize( $preset['preset']);
                $presetModelObject->createOrUpdate($preset);
            }
            update_option($this->preset_key, true);
        }
    }

    // default presets
    function getPresets(){
        $default_options = [
            'options' => [
                'controls' => ['play-large', 'play', 'progress', 'current-time', 'duration', 'mute', 'volume', 'fullscreen'],
                'repeat' => 'false',
                'autoplay' => 'false',
                'muted' => 'false',
                'resetOnEnd' => 'false',
                'autoHideControl' => 'true',
                'seekTime' => '10',
                'speed' => [
                    'selected' => '1',
                    'options' => ['0.5', '0.75', '1', '1.25', '1.5', '1.75', '2', '4']
                ]
            ],
            'brandColor' => '#00B3FF',
            'radius' => '5px',
            'thumbInPause' => 'false',
            'thumbStyle' => 'default',
            'endScreen' => [
                'enabled' => 'false',
                'text' => '',
                'link' => ''
            ],
            'hideYoutubeUI' => 'false',
            'saveState' => 'true',
            'plyrStyle' => [
                'borderRadius' => '3px',
                'plyr__control--overlaid' => [
                    'padding' => [
                        'top' => '15px',
                        'right' => '15px',
                        'bottom' => '15px',
                        'left' => '15px'
                    ],
                    'borderRadius' => '50%',
                    'background' => '#00b2ff'
                ],
                'plyr__control--overlaid svg' => [
                    'height' => '25px',
                    'width' => '25px'
                ]
            ],
            'watermark' => [
                'enabled' => 'false',
                'text' => 'Enter your watermark text',
                'color' => '#fff',
                'backgroundColor' => '#303030',
                'opacity' => '70',
                'position' => 'top-right',
                'selector' => 'watermark'
            ],
            'hideControlsWhenPause' => 'false'
        ];

        return [
            [
                'name' => 'Default',
                'preset' => serialize($default_options)
            ]
        ];
    }



}