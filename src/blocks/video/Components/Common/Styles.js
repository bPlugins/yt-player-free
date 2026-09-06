import { mobileBreakpoint, tabBreakpoint } from "../../../../../../bpl-tools/utils/data";
import { isValidCSS } from "../../../../../../bpl-tools/utils/getCSS";
import { useStyleRenderer } from "../../../../../../wp-utils/v1/hooks";

const Styles = ({ attributes, preset }) => {
  const { uniqueId, brandWidth = {}, brandBorder = {}, brandLogo = {}, ytWrapperStyle = {} } = attributes;
  const targetPreset = preset?.preset?.plyrStyle
    ? preset.preset
    : (preset?.plyrStyle
      ? preset
      : (attributes?.presetData?.preset?.plyrStyle
        ? attributes.presetData.preset
        : (attributes?.presetData?.plyrStyle
          ? attributes.presetData
          : attributes)));

  const { CSS } = useStyleRenderer(targetPreset, `#${uniqueId}`);
  const { radius } = brandBorder;
  const { brandPosition = "top-right" } = brandLogo;

  const ytWrapperSl = `#${uniqueId} .ytWrapper`;
  const brandLogoSl = `${ytWrapperSl} .custom-overlay`;

  const positionStyles = {
    "top-left": `top: 10px; left: 10px;`,
    "top-right": `top: 10px; right: 10px;`,
    "bottom-left": `bottom: 10px; left: 10px;`,
    "bottom-right": `bottom: 10px; right: 10px;`,
    "center-center": `top: 50%; left: 50%; transform: translate(-50%, -50%);`,
    "center": `top: 50%; left: 50%; transform: translate(-50%, -50%);`,
  };

  const positionStyle = positionStyles[brandPosition] || positionStyles["top-right"];

  const overlayBg = targetPreset?.plyrStyle?.["plyr__control--overlaid"]?.background
    || targetPreset?.["plyr__control--overlaid"]?.background
    || attributes?.presetData?.preset?.plyrStyle?.["plyr__control--overlaid"]?.background
    || attributes?.presetData?.plyrStyle?.["plyr__control--overlaid"]?.background;

  const brandColor = attributes?.brandColor || (typeof ytpPlayer !== "undefined" && ytpPlayer?.brandColor);

  const overlayBgOverride = overlayBg ? `
        #${uniqueId} .plyr .plyr__control--overlaid,
        #${uniqueId}.ytPlayer .plyr .plyr__control--overlaid,
        .wp-block-yt-player-video#${uniqueId} .plyr .plyr__control--overlaid {
          background: ${overlayBg} !important;
        }
  ` : "";

  const brandColorOverride = brandColor ? `
        #${uniqueId} .plyr,
        #${uniqueId}.ytPlayer .plyr,
        .wp-block-yt-player-video#${uniqueId} .plyr,
        #${uniqueId} {
          --plyr-color-main: ${brandColor} !important;
          --plyr-range-fill-background: ${brandColor} !important;
          --plyr-video-control-background-hover: ${brandColor} !important;
          --plyr-video-control-color-hover: #ffffff !important;
        }
        #${uniqueId} .plyr input[type="range"],
        #${uniqueId}.ytPlayer .plyr input[type="range"],
        .wp-block-yt-player-video#${uniqueId} .plyr input[type="range"],
        #${uniqueId} .plyr .plyr__progress input[type="range"],
        #${uniqueId} .plyr .plyr__volume input[type="range"] {
          color: ${brandColor} !important;
        }
        #${uniqueId} .plyr .plyr__control:not(.plyr__control--overlaid):hover,
        #${uniqueId}.ytPlayer .plyr .plyr__control:not(.plyr__control--overlaid):hover,
        .wp-block-yt-player-video#${uniqueId} .plyr .plyr__control:not(.plyr__control--overlaid):hover,
        #${uniqueId} .plyr .plyr__control:not(.plyr__control--overlaid)[aria-expanded="true"],
        #${uniqueId}.ytPlayer .plyr .plyr__control:not(.plyr__control--overlaid)[aria-expanded="true"],
        .wp-block-yt-player-video#${uniqueId} .plyr .plyr__control:not(.plyr__control--overlaid)[aria-expanded="true"] {
          background: ${brandColor} !important;
          color: #ffffff !important;
        }
  ` : "";

  return (
    <style
      dangerouslySetInnerHTML={{
        __html: `
        ${ytWrapperSl} {
          ${isValidCSS("width", ytWrapperStyle?.width)}
        }
        ${brandLogoSl} {
          position: absolute;
          z-index: 10;
          opacity: 0.8;
          ${positionStyle}
        }
		    ${brandLogoSl} img{
          border-radius: ${radius};
          ${isValidCSS("width", brandWidth?.width["desktop"])}
        }
        ${tabBreakpoint}{
			  ${brandLogoSl} img{
				${isValidCSS("width", brandWidth?.width["tablet"])}}
        }
        ${mobileBreakpoint}{
        ${brandLogoSl} img{
        ${isValidCSS("width", brandWidth?.width["mobile"])}}
        }
        ${brandLogoSl} img{
				${isValidCSS("width", brandWidth?.width["mobile"])}}
        
        ${CSS}
        ${overlayBgOverride}
        ${brandColorOverride}
	`,
      }}
    />
  );
};

export default Styles;

