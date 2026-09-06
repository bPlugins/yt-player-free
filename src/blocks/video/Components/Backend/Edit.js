import { useEffect } from "@wordpress/element";
import { useBlockProps } from "@wordpress/block-editor";
import { setAutoFreeze } from "immer";

import { dataParser } from "../../../../../../wp-utils/v1";
import Settings from "../Backend/Settings/Settings";
import Preset from "/src/blocks/Preset/Preset";

import { useState } from "react";
import { RotatingLines } from "react-loader-spinner";
import Styles from "../Common/Styles";
import YouTube from "../Common/YouTube";

setAutoFreeze(false);

const Edit = (props) => {
  const { attributes, clientId, setAttributes } = props;
  const { presets = [], presetID } = attributes;
  const [preset, setPreset] = useState(null);
  // eslint-disable-next-line no-unused-vars
  const [isPremium, setIsPremium] = useState(ytpPlayer.is_premium);

  useEffect(() => {
    // create unique id
    setAttributes({ uniqueId: `yt${clientId.substr(0, 8)}` });
  }, []);

  useEffect(() => {
    if (presets && presets.length) {
      const found = presets.find((item) => item.id == presetID) || presets[0] || {};
      setPreset(found);
    } else {
      setPreset({ id: presetID || "1", preset: {} });
    }
  }, [presetID, presets, attributes.presetUpdateKey]);

  useEffect(() => {
    if (presets && presets.length) {
      const presetData = dataParser(
        presets?.find((item) => item.id == presetID) || presets?.[0] || {},
      );
      if (JSON.stringify(attributes.presetData) !== JSON.stringify(presetData)) {
        setAttributes({ presetData });
      }
    }
  }, [
    presetID,
    presets,
    attributes.presetUpdateKey,
  ]);

  const blockProps = useBlockProps();

  return (
    <div {...blockProps}>
      <Settings {...props} isPremium={isPremium} />
      <Preset {...props} />
      <Styles
        key={JSON.stringify(preset) + attributes.presetUpdateKey}
        attributes={attributes}
        setAttributes={setAttributes}
        preset={preset}
      />
      <div style={{ pointerEvents: props.isSelected ? "auto" : "none" }}>
        {preset ? (
          <YouTube
            preset={preset}
            attributes={attributes}
            className={props.className}
          />
        ) : (
          <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', minHeight: '300px' }}>
            <RotatingLines
              visible={true}
              height="96"
              width="96"
              strokeColor="#00b2ff"
              strokeWidth="5"
              animationDuration="0.75"
              ariaLabel="rotating-lines-loading"
              wrapperStyle={{}}
              wrapperClass=""
            />
          </div>
        )}
      </div>
    </div>
  );
};

export default Edit;
