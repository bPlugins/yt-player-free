import {
  Button,
  ClipboardButton,
  Dashicon,
  PanelBody,
  PanelRow,
  SelectControl,
  TabPanel,
  TextControl,
  TextareaControl,
  ToggleControl,
  __experimentalUnitControl as UnitControl,
} from "@wordpress/components";

import { InspectorControls } from "@wordpress/block-editor";
import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import Swal from "sweetalert2";

import { BUnitControl } from "../../../../../../../wp-utils/v1/components";

import { useWPAjaxMutation } from "../../../../../../../wp-utils/v1/hooks";

import { RotatingLines } from 'react-loader-spinner';
import { Device, InlineMediaUpload, Label, Notice } from "../../../../../../../bpl-tools/Components";
import { tabController, updateData } from "../../../../../../../bpl-tools/utils/functions";
import { generalStyleTabs } from "../../../utils/options";

import { AdvertiseCard, ProModal } from "../../../../../../../bpl-tools/ProControls";
import { pricingPage } from "../../../utils/functions";

const Settings = ({ setAttributes, attributes, isPremium }) => {
  const { source, presets = [], brandLogo = {}, brandWidth = {}, brandBorder = {}, customBanner = {} } = attributes;
  const { isBrandLogo = false, brandUrl = "", brandPosition = "" } = brandLogo;
  const { isCustomBanner = false, bannerUrl = "" } = customBanner;

  const [device, setDevice] = useState("desktop");

  const [isProModalOpen, setIsProModalOpen] = useState(false);

  const [copied, setCopied] = useState(false);

  const { saveData: deletePreset, isLoading: isDeleting } = useWPAjaxMutation(ytpPlayer?.ajaxURL, {
    nonce: ytpPlayer?.nonce,
    method: "deletePreset",
    action: "ytp_ajax",
    model: "Presets",
  });

  const postId = wp.data.select("core/editor")?.getCurrentPostId();
  const postType = wp.data.select("core/editor")?.getCurrentPostType();

  const { additionalCSS, additionalID } = attributes;

  /*
    Delete preset
    @param {*} id
   */

  const handleDeletePreset = async (id) => {
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!",
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          await deletePreset({ id });
          setAttributes({ presets: presets.filter((item) => item.id !== id) });

          // Success alert
          Swal.fire("Deleted!", "Your preset has been deleted.", "success");
        } catch (error) {
          // eslint-disable-next-line no-console
          console.log(error.message);
        }
      }
    });
  };

  const setStyle = (key, value) => {
    setAttributes({ [key]: { ...attributes[key], ...value } });
  };
  const getStyle = (selector, property) => {
    return attributes[selector]?.[property];
  };

  return <>
    <InspectorControls>
      {postType === "ytplayer" && (
        <PanelBody>
          <PanelRow>
            <div className="ytp_front_shortcode" style={{ display: 'flex', width: '100%' }}>
              <ClipboardButton
                title={copied ? __("Copied", 'yt-player') : ""}
                variant="primary"
                className="is-primary"
                style={{ width: '100%', justifyContent: 'center' }}
                text={`[ytplayer id=${postId}]`}
                onCopy={() => setCopied(true)}
                onFinishCopy={() => setCopied(false)}
              >
                {/* <input value={`[ytplayer id=${postId}]`} /> */}
                {copied ? __("Copied!", 'yt-player') : __("Copy Shortcode", 'yt-player')}
              </ClipboardButton>
            </div>
          </PanelRow>
        </PanelBody>
      )}

      <TabPanel className="bPlTabPanel" activeClass="activeTab" tabs={generalStyleTabs} onSelect={tabController}>
        {(tab) => {
          return (
            <>
              {tab.name == "settings" && (
                <>
                  <PanelBody
                    title={__("Video Settings", "yt-player")}
                    initialOpen={true}
                  >
                    <TextControl
                      label={__("Video source", "yt-player")}
                      value={source}
                      onChange={(source) => setAttributes({ source })}
                      placeholder="Youtube video link"
                    />

                    {isPremium ? (
                      <>
                        <ToggleControl
                          className="mt20"
                          label={__("Enable Brand Logo", "yt-player")}
                          checked={isBrandLogo}
                          onChange={(value) =>
                            setAttributes({
                              brandLogo: updateData(
                                brandLogo,
                                value,
                                "isBrandLogo",
                              ),
                            })
                          }
                        />

                        {isBrandLogo && (
                          <InlineMediaUpload
                            types={["images"]}
                            className="mt10"
                            label={__("Brand Logo URL", "yt-player")}
                            placeholder={__(
                              "Insert or upload brand logo",
                              "yt-player",
                            )}
                            value={
                              brandUrl ??
                              "https://bplugins.com/wp-content/themes/b-technologies/assets/images/logo/logo-2.svg"
                            }
                            onChange={(v) =>
                              setAttributes({
                                brandLogo: updateData(brandLogo, v, "brandUrl"),
                              })
                            }
                          />
                        )}

                        <ToggleControl
                          className="mt20"
                          label={__("Enable Custom Thumbnail", "yt-player")}
                          checked={isCustomBanner}
                          onChange={(value) =>
                            setAttributes({
                              customBanner: updateData(
                                customBanner,
                                value,
                                "isCustomBanner",
                              ),
                            })
                          }
                        />

                        {isCustomBanner && (
                          <InlineMediaUpload
                            types={["images"]}
                            className="mt10"
                            label={__("Thumbnail URL", "yt-player")}
                            placeholder={__(
                              "Insert or upload Thumbnail",
                              "yt-player",
                            )}
                            value={
                              bannerUrl ??
                              "https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg"
                            }
                            onChange={(v) =>
                              setAttributes({
                                customBanner: updateData(
                                  customBanner,
                                  v,
                                  "bannerUrl",
                                ),
                              })
                            }
                          />
                        )}
                      </>
                    ) : (
                      <Notice status="premium" isIcon={true}>
                        {__(
                          "Brand Logo Overlay, Custom Thumbnail Banner & Advanced Video Controls are available in the Pro version.",
                          "yt-player"
                        )}
                      </Notice>
                    )}
                  </PanelBody>

                  <PanelBody title={__("Presets", "yt-player")}>
                    <div
                      className={`ytp-presets ${isDeleting ? "presetDeleting" : ""
                        }`}
                    >
                      {presets?.length ? (
                        presets?.map((item) => (
                          <>
                            <div className={`ytp-preset-item`}>
                              <div
                                className="box"
                                onClick={() => {
                                  setAttributes({
                                    presetID: item?.id + "",
                                    presetData: item,
                                  });
                                }}
                              >
                                <span>{item.name?.[0]?.toUpperCase()}</span>
                              </div>
                              <div className="actions">
                                <Dashicon
                                  icon="edit"
                                  title={__("Edit", "yt-player")}
                                  onClick={() => {
                                    setAttributes({
                                      editingPresetID: item.id,
                                      presetOpened: true,
                                    });
                                  }}
                                />
                                {presets?.length > 1 && (
                                  <Dashicon
                                    style={{ backgroundColor: "red" }}
                                    icon="trash"
                                    title={__("Delete", "yt-player")}
                                    onClick={() =>
                                      handleDeletePreset(item.id)
                                    }
                                  />
                                )}
                              </div>
                              <p>
                                {item?.name ||
                                  __("Unknown Name", "yt-player")}
                              </p>
                            </div>
                          </>
                        ))
                      ) : (
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
                      )}
                    </div>
                    <Button
                      onClick={() =>
                        setAttributes({
                          editingPresetID: null,
                          presetOpened: true,
                        })
                      }
                      style={{
                        width: "100%",
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "stretch",
                      }}
                      variant="primary"
                    >
                      <p style={{ fontWeight: "bold", fontSize: "14px" }}>
                        Create New Preset
                      </p>
                    </Button>
                  </PanelBody>

                  <PanelBody
                    title={__("Additional", "yt-player")}
                    initialOpen={false}
                  >
                    <TextControl
                      label={__("Additional ID", "yt-player")}
                      value={additionalID}
                      onChange={(additionalID) =>
                        setAttributes({ additionalID })
                      }
                    />
                    <TextareaControl
                      label={__("Additional CSS", "yt-player")}
                      value={additionalCSS}
                      onChange={(additionalCSS) =>
                        setAttributes({ additionalCSS })
                      }
                    />
                  </PanelBody>
                </>
              )}

              {tab.name == "style" && (
                <>
                  <PanelBody
                    title={__("Player Styles", "yt-player")}
                    initialOpen={true}
                  >
                    <BUnitControl
                      onChange={(width) =>
                        setStyle("ytWrapperStyle", { width })
                      }
                      value={getStyle("ytWrapperStyle", "width")}
                      label={__("Width", "yt-player")}
                      labelPosition="side"
                      em="30"
                    />
                  </PanelBody>

                  {isBrandLogo && isPremium && (
                    <PanelBody
                      title={__("Brand Logo Styles", "yt-player")}
                      initialOpen={true}
                    >
                      <SelectControl
                        label="Brand Logo Position"
                        value={brandPosition}
                        options={[
                          { label: "Top Right", value: "top-right" },
                          { label: "Top Left", value: "top-left" },
                          { label: "Bottom Right", value: "bottom-right" },
                          { label: "Bottom Left", value: "bottom-left" },
                          { label: "Center", value: "center" },
                        ]}
                        onChange={(value) =>
                          setAttributes({
                            brandLogo: updateData(
                              brandLogo,
                              value,
                              "brandPosition",
                            ),
                          })
                        }
                      />

                      <PanelRow>
                        <Label className="mb5">
                          {__("Brand Logo Size:", "yt-player")}
                        </Label>
                        <Device onChange={(val) => setDevice(val)} />
                      </PanelRow>

                      <UnitControl
                        className="mb10"
                        value={brandWidth?.width[device]}
                        onChange={(val) =>
                          setAttributes({
                            brandWidth: updateData(
                              brandWidth,
                              val,
                              "width",
                              device,
                            ),
                          })
                        }
                        beforeIcon="grid-view"
                        step={1}
                        max={100}
                      />

                      <UnitControl
                        className="mt10"
                        label="Logo Radius"
                        value={brandBorder?.radius}
                        onChange={(val) =>
                          setAttributes({
                            brandBorder: updateData(
                              brandBorder,
                              val,
                              "radius",
                            ),
                          })
                        }
                        beforeIcon="grid-view"
                        step={1}
                        max={50}
                      />
                    </PanelBody>
                  )}
                </>
              )}
            </>
          );
        }}
      </TabPanel>

      {!isPremium && (
        <AdvertiseCard planLink={pricingPage || 'https://bplugins.com/products/yt-player/pricing'} />
      )}
    </InspectorControls>

    <ProModal isProModalOpen={isProModalOpen} setIsProModalOpen={setIsProModalOpen} link={pricingPage || 'https://bplugins.com/products/yt-player/pricing/'} title={__('Unlock More with<br/>YT Player Pro!')} description={__(`The free features of Plugin do a lot-still, without PRO, you're holding yourself back from getting more.`)} features={[
      'Keep the video visible in a small floating player while visitors scroll down the page.',
      'Display your custom logo as an overlay on the video with adjustable position, size, and transparency.',
      'Replace the default YouTube preview with your own thumbnail image to better match your content.',
      'Automatically start video playback when the page loads, with the option to begin muted.',
      'Customize the player’s appearance, including colors, controls, and layout, to match your website design.',
      'Hide the default YouTube interface and branding for a cleaner, distraction-free video experience.'
    ]} />

  </>
};

export default Settings;
