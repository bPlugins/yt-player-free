const { __ } = wp.i18n;
const { PanelBody, PanelRow, FormToggle } = wp.components;
import { Notice } from "../../../../bpl-tools/Components";

const BControls = ({ setPreset, preset, isPremium }) => {
  const { controls = [] } = preset?.preset?.options || [];

  const proControlsList = [
    { label: "Large Play Button", key: "play-large" },
    { label: "Restart", key: "restart", isPro: true },
    { label: "Rewind", key: "rewind", isPro: true },
    { label: "Play", key: "play" },
    { label: "Fast Forward", key: "fast-forward", isPro: true },
    { label: "Progress", key: "progress" },
    { label: "Current Time", key: "current-time" },
    { label: "Duration", key: "duration" },
    { label: "Mute", key: "mute" },
    { label: "Volume", key: "volume" },
    { label: "Fullscreen", key: "fullscreen" },
  ];

  const controlsList = isPremium
    ? proControlsList
    : proControlsList.filter((item) => !item.isPro);

  const controlsSchema = proControlsList.map((item) => item.key);

  const handleControl = (control) => {
    let newControls = [...controls];
    if (controls.includes(control)) {
      newControls.splice(newControls.indexOf(control), 1);
    } else {
      newControls = [...controls, control];
    }
    const tempControls = [...controlsSchema];
    controlsSchema.forEach((item) => {
      if (!newControls.includes(item)) {
        const index = tempControls.indexOf(item);
        if (index > -1) {
          tempControls.splice(index, 1);
        }
      }
    });

    setPreset({
      options: { ...preset?.preset?.options, controls: tempControls },
    });
  };

  return (
    <PanelBody title={__("Controls", "yt-player")} initialOpen={false}>
      {controlsList.map((item) => {
        const { label, key } = item;
        return (
          <PanelRow key={key}>
            <label htmlFor={key} className="label">
              {label}
            </label>
            <FormToggle
              id={key}
              checked={controls.includes(key) || false}
              onChange={() => handleControl(key)}
            />
          </PanelRow>
        );
      })}
      {!isPremium && (
        <Notice status="premium" isIcon={true}>
          {__(
            "Restart, Rewind, and Fast Forward controls are available in the Pro version.",
            "yt-player"
          )}
        </Notice>
      )}
    </PanelBody>
  );
};

export default BControls;
