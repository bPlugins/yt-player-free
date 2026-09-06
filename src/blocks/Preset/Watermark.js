import { FormToggle, PanelBody, PanelRow, RangeControl, SelectControl, TextControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import React from "react";
import { BColor } from "../../../../wp-utils/v1/components";

const Watermark = ({ setWatermark, watermark }) => {
  const { enabled, text, position, color, backgroundColor, opacity } = watermark;

  return (
    <PanelBody title={__("Watermark", 'yt-player')} initialOpen={false}>
      <PanelRow>
        <label className="label" htmlFor="enabled">
          {__("watermark", 'yt-player')}
        </label>
        <FormToggle id="enabled" checked={enabled} onChange={() => setWatermark({ enabled: !enabled })} />
      </PanelRow>
      {enabled && (
        <>
          <PanelRow>
            <label className="label" htmlFor="text">
              {__("Text", 'yt-player')}
            </label>
            <TextControl id="text" value={text} onChange={(text) => setWatermark({ text })} />
          </PanelRow>
          <PanelRow>
            <label>{__("Color", 'yt-player')}</label>
            <BColor value={color} onChange={(color) => setWatermark({ color })} />
          </PanelRow>
          <PanelRow>
            <label>{__("Background", 'yt-player')}</label>
            <BColor value={backgroundColor} onChange={(backgroundColor) => setWatermark({ backgroundColor })} />
          </PanelRow>
          <PanelRow>
            <label>{__("Position", 'yt-player')}</label>
            <SelectControl
              value={position}
              options={[
                { label: "Top Right", value: "flex-end" },
                { label: "Top Left", value: "flex-start" },
                // { label: "Random", value: "random" },
              ]}
              onChange={(position) => setWatermark({ position })}
            />
          </PanelRow>
          <RangeControl label="Opacity" value={opacity * 100} onChange={(opacity) => setWatermark({ opacity: opacity / 100 })} step={1} min={0} max={100} />
        </>
      )}
    </PanelBody>
  );
};

export default Watermark;
