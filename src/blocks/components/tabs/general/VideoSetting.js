import { __experimentalBorderControl as BorderControl, PanelRow, SelectControl, TextControl, __experimentalUnitControl as UnitControl, TextareaControl, } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import { InlineMediaUpload } from "../../../../../../bpl-tools/Components";
import { updateData } from "../../../../../../bpl-tools/utils/functions";
// import { BorderControl } from "../../../../../../bpl-tools/Components/Deprecated";

const VideoSetting = ({ attributes, setAttributes, index }) => {
  const { videos } = attributes;
  const video = videos[index];


  return (
    <>
      <TextControl
        value={video.videoSource}
        label={__("Video Source", "yt-player")}
        placeholder={__("Video Source", "yt-player")}
        onChange={(value) =>
          setAttributes({
            videos: updateData(videos, value, index, "videoSource"),
          })
        }
      />

      <InlineMediaUpload
        types={["images"]}
        className="mt10"
        label={__("Video Thumbnail", "yt-player")}
        placeholder={__("Youtube Video Thumbnail", "yt-player")}
        value={video?.videoThumb}
        onChange={(val) =>
          setAttributes({
            videos: updateData(videos, val, index, "videoThumb"),
          })
        }
      />

      <SelectControl
        label={__("Video Position", "yt-player")}
        className="mt10"
        options={[
          { label: "left", value: "left" },
          { label: "Right", value: "right" },
        ]}
        value={video.videoPosition}
        onChange={(value) =>
          setAttributes({
            videos: updateData(videos, value, index, "videoPosition"),
          })
        }
      />

      <PanelRow>
        <BorderControl
          value={video.border}
          onChange={(newVal) =>
            setAttributes({
              videos: updateData(videos, newVal, index, "border"),
            })
          }
          label={__("Border", "yt-player")}
        />
      </PanelRow>

      <UnitControl
        className="mt10"
        label={__("Border Radius", "yt-player")}
        value={video.borderRadius}
        onChange={(val) =>
          setAttributes({
            videos: updateData(videos, val, index, "borderRadius"),
          })
        }
      />

      <TextControl
        value={video.heading}
        className="mt10"
        label={__("Title", "yt-player")}
        placeholder={__("Enter Title", "yt-player")}
        onChange={(value) =>
          setAttributes({
            videos: updateData(videos, value, index, "heading"),
          })
        }
      />

      <TextareaControl
        value={video.content}
        className="mt10"
        label={__("Content", "yt-player")}
        placeholder={__("Enter Content", "yt-player")}
        onChange={(value) =>
          setAttributes({
            videos: updateData(videos, value, index, "content"),
          })
        }
      />
    </>
  );
};

export default VideoSetting;
