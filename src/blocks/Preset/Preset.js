/* eslint-disable no-console */
import { Button, Modal, TextControl } from "@wordpress/components";
import { useState, useMemo } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { produce } from "immer";
import Swal from "sweetalert2";

import Options from "./Options";
import Preview from "./Preview";

import "./style.scss";
import { useEffect } from "@wordpress/element";
import presetSchema from "./presetSchema.json";
import { useWPAjaxMutation, useWPAjaxQuery } from "../../../../wp-utils/v1";


const Preset = ({ attributes, setAttributes }) => {
  const { uniqueId, presetOpened, editingPresetID, presets: myPresets } = attributes;
  // eslint-disable-next-line no-unused-vars
  const [isPremium, setIsPremium] = useState(ytpPlayer.is_premium);
  //  const { isPremium=false } = usePremiumInEditor("ytpUtils", "ytpPremiumChecker");

  const {saveData,data,isLoading: isSaving} = useWPAjaxMutation(ytpPlayer?.ajaxURL, { nonce: ytpPlayer?.nonce, method: "createOrUpdate", action: "ytp_ajax", model: "Presets" });

  const { data: presets, isLoading } = useWPAjaxQuery(ytpPlayer?.ajaxURL, { nonce: ytpPlayer?.nonce, method: "fetchPresets", action: "ytp_ajax", model: "Presets" });

  const [notice, setNotice] = useState({});
  const [preset, setPreset] = useState({});


  const schema = useMemo(() => {
    const schema = {};
    Object.keys(presetSchema).map((key) => {
      schema[key] = presetSchema[key]?.default;
    });
    return schema;
  }, []);

  useEffect(() => {
    if (!isLoading && presets && Array.isArray(presets) && presets.length > 0) {
      if (JSON.stringify(myPresets) !== JSON.stringify(presets)) {
        setAttributes({ presets });
      }
    }
  }, [isLoading, presets]);

  useEffect(() => {
    console.log(data);
  }, [isSaving]);

  useEffect(() => {
    const presetData = myPresets?.find((item) => item.id == editingPresetID) || { preset: schema };
    setPreset(presetData);
  }, [editingPresetID, presetOpened]);

  const updatePreset = (option) => {
    const key = Object.keys(option)?.[0];
    if (key) {
      const newPreset = produce(preset, (draft) => {
        draft.preset[key] = option[key];
      });
      setPreset(newPreset);
    }
  };

  const handleCreatePreset = async () => {
    setNotice({});

    if (!preset.name) {
      return setNotice({ type: "error", message: "Name field is required" });
    }

    try {
      const response = await saveData(preset);
      if (response) {
        let updatedPresets;
        if (myPresets.find((item) => item?.id === preset.id)) {
          updatedPresets = produce(myPresets, (draft) => {
            const index = draft.findIndex((item) => item.id === preset.id);
            draft.splice(index, 1, preset);
          });
        } else {
          updatedPresets = [...myPresets, { ...preset, id: response }];
        }

        // Update attributes and trigger re-render
        setAttributes({
          presets: updatedPresets,
          editingPresetID: response,
          presetData: preset,
          presetUpdateKey: Date.now(), // Unique key to force re-render
        });

        setAttributes({ presetOpened: false });

        setTimeout(() => {
          Swal.fire({
            title: "Success!",
            text: "Preset has been updated successfully.",
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
          });
        }, 300);
      }
    } catch (error) {
      setNotice({ type: "error", message: "Failed" });
    }
  };

  return (
    <>
      {presetOpened && (
        <Modal title={__("Preset", 'yt-player')} onRequestClose={() => setAttributes({ presetOpened: false })} className="ytpPresetModal" shouldCloseOnClickOutside={false}>
          <TextControl value={preset.name || ""} placeholder={__("Type Preset Name", 'yt-player')} onChange={(name) => setPreset({ ...preset, name })} />
          <div className="ytpPreset">
            <div className="options">
              <Options isPremium={isPremium} setPreset={updatePreset} preset={preset} />
            </div>
            <div className="preview">
              <Preview isPreset={true} uniqueId={uniqueId} preset={preset} />
            </div>
          </div>

          <div className="actionsWrapper">
            <div className="actions">
              <div></div>
              <div>
                {notice?.message && <span className={`notice ${notice.type}`}>{notice.message}</span>}
                {isSaving && <div class="ytp_loader"></div>}
                <Button disabled={isSaving} variant="primary" onClick={handleCreatePreset}>
                  {preset?.id ? __("Update", 'yt-player') : __("Create", 'yt-player')}
                </Button>
              </div>
            </div>
          </div>
        </Modal>
      )}
    </>
  );
};

export default Preset;
