<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

use YTP\Model\Presets;
use YTP\Helper\Utils;

$ytp_preset = new Presets();
$ytp_id = !empty($attributes['uniqueId']) ? $attributes['uniqueId'] : wp_unique_id('ytPlayer-');

$ytp_preset_id = !empty($attributes['presetID']) ? $attributes['presetID'] : 1;
$ytp_preset_data = $ytp_preset->get($ytp_preset_id);
if (empty($ytp_preset_data) && !empty($attributes['presetData'])) {
    $ytp_preset_data = $attributes['presetData'];
}

$ytp_brand_color = Utils::getOptionDeep('ytp_option', 'brandColor', '#00AFFA');
if (!empty($ytp_brand_color)) {
    $attributes['brandColor'] = $ytp_brand_color;
}

// phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>

<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> id='<?php echo esc_attr($ytp_id); ?>' data-attributes='<?php echo esc_attr(wp_json_encode($attributes)); ?>' data-preset="<?php echo esc_attr(wp_json_encode($ytp_preset_data ?: [])) ?>">
</div>