import { useEffect, useRef, useState } from "@wordpress/element";
import { useStyleRenderer } from "../../../../wp-utils/v1/hooks";
// eslint-disable-next-line no-unused-vars
let i = 1;
const Preview = ({ uniqueId, preset }) => {
  const [player, setPlayer] = useState(null);
  const { CSS } = useStyleRenderer({ ...preset.preset }, `#${uniqueId}`);

  const domRef = useRef(null);

  useEffect(() => {
    if (domRef.current) {
      const targetDoc = domRef.current.ownerDocument || document;
      if (!targetDoc.querySelector("#plyr-sprite-container")) {
        fetch("https://cdn.plyr.io/3.7.8/plyr.svg")
          .then((res) => res.text())
          .then((svgText) => {
            if (!targetDoc.querySelector("#plyr-sprite-container")) {
              const div = targetDoc.createElement("div");
              div.id = "plyr-sprite-container";
              div.style.display = "none";
              div.innerHTML = svgText;
              targetDoc.body.appendChild(div);
            }
          })
          .catch(() => {});
      }
    }

    if (player) {
      player.destroy();
    }

    const isPremium = typeof window.ytpPlayer !== "undefined" && window.ytpPlayer !== null ? Boolean(window.ytpPlayer.is_premium) : false;
    const rawOptions = preset?.preset?.options || {};
    const proControls = ["restart", "rewind", "fast-forward"];
    const rawControls = rawOptions.controls || [];
    const filteredControls = isPremium
      ? rawControls
      : rawControls.filter((c) => !proControls.includes(c));

    const options = {
      ...rawOptions,
      controls: filteredControls,
    };

    const newPlayer = new Plyr(
      domRef.current?.querySelector("#player"),
      options,
    );
    newPlayer.volume = 0.5;
    setPlayer(newPlayer);
  }, [preset?.preset?.options?.controls]);

  return (
    <>
      {/* <Watermark player={player} domRef={domRef} watermark={preset?.preset.watermark} uniqueId={uniqueId} /> */}
      <style dangerouslySetInnerHTML={{ __html: CSS }}></style>
      <div
        style={{
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          width: "100%",
        }}
      >
        <div
          id={uniqueId}
          ref={domRef}
          className={`ytPreview ytWrapper ytPlayer ${
            preset?.preset?.hideControlsWhenPause ? "hideControlsWhenPause" : ""
          }`}
        >
          <video
            id="player"
            data-poster="https://picsum.photos/536/354"
          ></video>
        </div>

        <div
          style={{
            padding: "10px",
            background: "#f0f6fc",
            border: "1px solid #c9d1d9",
            borderRadius: "6px",
            marginTop: "15px",
            color: "#586069",
            fontSize: "14px",
            textAlign: "center",
            display: "block",
          }}
        >
          <strong>Note:</strong> This is not a real video, it&apos;s a random
          thumbnail image. This is a preview to show the controls and will not
          play here.
        </div>
      </div>
    </>
  );
};

export default Preview;
