import { useState, useEffect } from "@wordpress/element";

import { blockStyleGenerator } from "../../../../wp-utils/v1";

const Style = ({ attributes }) => {
  const { preset, uniqueId } = attributes;
  const [CSS, setCSS] = useState(null);

  useEffect(() => {
    const CSS = blockStyleGenerator(preset);
    setCSS(CSS);
  }, [preset, uniqueId]);

  return <style>{CSS}</style>;
};

export default Style;
