import { __experimentalBoxControl as BoxControl, __experimentalNumberControl as NumberControl, Panel, PanelBody, ToggleControl, __experimentalUnitControl as UnitControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

import { BColor } from "../../../../wp-utils/v1/components";
import { Notice } from "../../../../bpl-tools/Components";
import BControls from "./BControls";

const Options = ({ preset, setPreset, isPremium }) => {
  const { options, plyrStyle = {}, thumbInPause, hideYoutubeUI, hideControlsWhenPause } = preset?.preset || {};
  const { autoplay, muted, resetOnEnd, seekTime, autoHideControl } = options || {};

  const setOptions = (option) => {
    const newOptions = { ...options, ...option };
    setPreset({ options: newOptions });
  };

  return (
    <>
      <Panel>
        <BControls preset={preset} setPreset={setPreset} isPremium={isPremium} />
        <PanelBody title={__("Settings", 'yt-player')} initialOpen={false} className="bPlPanelBody">
          {isPremium ? (
            <>
              <ToggleControl
                className="mt10"
                label={__("Repeat", 'yt-player')}
                checked={resetOnEnd}
                onChange={() => setOptions({ resetOnEnd: !resetOnEnd })}
              />

              <ToggleControl
                className="mt10"
                label={__("Autoplay", 'yt-player')}
                checked={autoplay}
                onChange={() => setOptions({ autoplay: !autoplay })}
              />

              <ToggleControl
                className="mt10"
                label={__("Muted", 'yt-player')}
                checked={muted}
                onChange={() => setOptions({ muted: !muted })}
              />

              <ToggleControl
                className="mt10"
                label={__("Show thumbnail on pause", 'yt-player')}
                checked={thumbInPause}
                onChange={() => setPreset({ thumbInPause: !thumbInPause })}
              />

              <ToggleControl
                className="mt10"
                label={__("Auto Hide Control", 'yt-player')}
                checked={autoHideControl}
                onChange={() => setOptions({ autoHideControl: !autoHideControl })}
              />

              <NumberControl
                className="mt10"
                label={__("Seek Time", 'yt-player')}
                isShiftStepEnabled={true}
                onChange={(seekTime) => setOptions({ seekTime: parseInt(seekTime, 10) })}
                shiftStep={1}
                value={parseInt(seekTime, 10) || 0}
              />

              <ToggleControl
                className="mt10"
                label={__("Hide youtube UI (Experimental)", 'yt-player')}
                checked={hideYoutubeUI || false}
                onChange={() => setPreset({ hideYoutubeUI: !hideYoutubeUI })}
              />

              <ToggleControl
                className="mt10"
                label={__("Hide controls in pause", 'yt-player')}
                checked={hideControlsWhenPause}
                onChange={(v) => setPreset({ hideControlsWhenPause: v })}
              />
            </>
          ) : (
            <Notice status="premium" isIcon={true}>
              {__(
                "Pro settings options like Repeat, Autoplay, Muted, Custom Seek Time, and Hide YouTube UI are available in the Pro version.",
                "yt-player"
              )}
            </Notice>
          )}
        </PanelBody>

        <PanelBody title={__("Style", 'yt-player')} initialOpen={false}>
          <UnitControl
            step="1"
            value={plyrStyle?.borderRadius}
            onChange={(borderRadius) => setPreset({ plyrStyle: { ...plyrStyle, borderRadius } })}
            label={__("Round Corners", 'yt-player')}
            labelPosition="side"
          />
        </PanelBody>

        <PanelBody title={__("Large Play Button Style", 'yt-player')}>
          {isPremium ? (
            <>
              <UnitControl
                className="mt10"
                step="1"
                style={{ marginBottom: "20px" }}
                labelPosition="side"
                label={__("Corner Round", 'yt-player')}
                value={plyrStyle?.["plyr__control--overlaid"]?.borderRadius}
                onChange={(borderRadius) => setPreset({ plyrStyle: { ...plyrStyle, "plyr__control--overlaid": { ...plyrStyle?.["plyr__control--overlaid"], borderRadius } } })}
              />

              <BoxControl
                values={plyrStyle?.["plyr__control--overlaid"]?.padding}
                onChange={(padding) => setPreset({ plyrStyle: { ...plyrStyle, "plyr__control--overlaid": { ...plyrStyle?.["plyr__control--overlaid"], padding } } })}
                label={__("Padding", 'yt-player')}
              />

              <UnitControl
                labelPosition="side"
                style={{ marginTop: "20px" }}
                label={__("Icon Size", 'yt-player')}
                value={plyrStyle?.["plyr__control--overlaid svg"]?.height}
                onChange={(height) =>
                  setPreset({ plyrStyle: { ...plyrStyle, "plyr__control--overlaid svg": { ...plyrStyle?.["plyr__control--overlaid svg"], height, width: height } } })
                }
              />

              <BColor
                label={__("Background", 'yt-player')}
                value={plyrStyle?.["plyr__control--overlaid"]?.background}
                onChange={(background) => setPreset({ plyrStyle: { ...plyrStyle, "plyr__control--overlaid": { ...plyrStyle?.["plyr__control--overlaid"], background } } })}
              />
            </>
          ) : (
            <Notice status="premium" isIcon={true}>
              {__(
                "Custom play button corner radius, padding, icon size, and background color are available in the Pro version.",
                "yt-player"
              )}
            </Notice>
          )}
        </PanelBody>
      </Panel>
    </>
  );
};

export default Options;
