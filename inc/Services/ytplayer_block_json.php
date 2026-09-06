<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$block = [ // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
    'blockName' => "yt-player/video",
    'attrs' => [
        "options" => [
            "controls" => $meta('controls', ['play-large', 'play', 'progress', 'duration', 'current-time', 'mute', 'volume', 'fullscreen']),
            "repeat" => $meta('loop', false, true),
            "autoplay" => $meta('autoplay', false, true),
            "muted" =>  $meta('muted', false, true),
            "clickToPlay" =>  $meta('clickToPlay', true, true),
            "disableContextMenu" => $meta('disableContextMenu', true, true),
            "resetOnEnd" => $meta('resetOnEnd', true, true),
            "autoHideControl" => $meta('hideControls', true, true),
            "seekTime" => (int) $meta('seekTime', 10),
            "ratio" => $meta('ratio', '16:9'),
            "speed" => [
                "selected" => 1,
                "options" => $meta('speed', [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 4])
            ]
        ],
        "presetID" => "",
        "source" =>  $meta('source'),
        "thumbInPause" => $meta('showThumbnailOnPause', false, true),
        "thumbStyle" => "default",
        "brandLogo" => [
            "isBrandLogo" => $meta('brandLogo', false, true),
            "brandUrl" => $meta('logoSource', ''),
            "brandPosition" => $meta('brandLogoPosition', 'top-right'),
        ],
        "brandWidth" => [
            "width" => [
                "desktop" => $meta('brandSize', 100, false, 'width') . $meta('brandSize', 'px', false, 'unit'),
                "tablet" =>  $meta('brandSize', 100, false, 'width') . $meta('brandSize', 'px', false, 'unit'),
                "mobile" => $meta('brandSize', 100, false, 'width') . $meta('brandSize', 'px', false, 'unit')
            ]
        ],
        "brandBorder" => [
            "radius" => $meta('radius', 50, false, 'width') . $meta('radius', '%', false, 'unit')
        ],

        "customBanner" => [
            "isCustomBanner" => $meta('customThumbnail', false, true),
            "bannerUrl" => $meta('thumbnailSource', ''),
        ],

        "endScreen" => [
            "enabled" => false,
            "text" => "",
            "link" => ""
        ],
        "startTime" => 0,
        "hideYoutubeUI" => $meta('hideYoutubeUI', false, true),
        "saveState" => true,
        "plyrStyle" => [
            "borderRadius" => $meta('roundCorner', 3, false, 'width') . $meta('roundCorner', 'px', false, 'unit'),
            "plyr__control--overlaid" => [
                "padding" => [
                    "top" => $meta('playButtonPadding', 15, false, 'width') . $meta('playButtonPadding', 'px', false, 'unit'),
                    "right" => $meta('playButtonPadding', 15, false, 'width') . $meta('playButtonPadding', 'px', false, 'unit'),
                    "bottom" => $meta('playButtonPadding', 15, false, 'width') . $meta('playButtonPadding', 'px', false, 'unit'),
                    "left" => $meta('playButtonPadding', 15, false, 'width') . $meta('playButtonPadding', 'px', false, 'unit')
                ],
                "borderRadius" => $meta('playButtonCorner', 50, false, 'width') . $meta('playButtonCorner', '%', false, 'unit'),
                "background" => $meta('background', "#00b2ff")
            ],
            "plyr__control--overlaid svg" => [
                "height" => $meta('playIconSize', 25, false, 'height') . $meta('playIconSize', 'px', false, 'unit'),
                "width" => $meta('playIconSize', 25, false, 'width') . $meta('playIconSize', 'px', false, 'unit')
            ]
        ],
        "hideControlsWhenPause" => $meta('hideControlsWhenPause', false, true),
        "watermark" => [
            "enabled" => false,
            "text" => "Enter your watermark text",
            "color" => "#fff",
            "backgroundColor" => "#303030",
            "opacity" => 70,
            "position" => "top-right",
            "selector" => "watermark"
        ],
        'ytWrapperStyle' => [
            'width' => $meta('width', 100, false, 'width') . $meta('width', '%', false, 'unit'),
        ]
    ]
];