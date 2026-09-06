import { createRoot } from "react-dom/client";

import Styles from './Components/Common/Styles';
import YouTube from './Components/Common/YouTube';
import "./style.scss";
import { convertBooleansInObject } from './utils/functions';

document.addEventListener("DOMContentLoaded", () => {
  const blockNameEls = document.querySelectorAll(".wp-block-yt-player-video[data-attributes]");
  blockNameEls.forEach((blockNameEl) => {
    let attributes = {};
    let preset = {};

    try {
      if (blockNameEl.dataset.attributes) {
        attributes = JSON.parse(blockNameEl.dataset.attributes);
      }
    } catch (e) {
      console.warn("Failed to parse block attributes", e);
    }

    try {
      if (blockNameEl.dataset.preset) {
        preset = JSON.parse(blockNameEl.dataset.preset);
      }
    } catch (e) {
      console.warn("Failed to parse preset data", e);
    }

    const blockId = blockNameEl.id || attributes.uniqueId;
    const finalAttributes = { ...attributes, uniqueId: blockId };

    createRoot(blockNameEl).render(<>
      <Styles attributes={finalAttributes} preset={convertBooleansInObject(preset)} />
      <YouTube attributes={finalAttributes} className="ytWrapper-view" preset={convertBooleansInObject(preset)} />
    </>);
    blockNameEl?.removeAttribute("data-attributes");
  });
});

