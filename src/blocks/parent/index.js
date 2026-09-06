import { useEffect } from "@wordpress/element";

// In your application's entrypoint
const { __ } = wp.i18n;
const { InnerBlocks, useBlockProps } = wp.blockEditor;
const { useSelect, dispatch } = wp.data;
const { Button, Placeholder } = wp.components;
const { registerBlockType } = wp.blocks;

import metadata from './block.json';

registerBlockType(metadata, {
  edit: (props) => {
    const blockProps = useBlockProps();
    const { clientId, isSelected } = props;
    const innerBlocks = useSelect((select) => select("core/block-editor")?.getBlock(clientId)?.innerBlocks || []);

    useEffect(() => {
      if (isSelected) {
        wp.data.dispatch("core/edit-post")?.openGeneralSidebar("edit-post/block");
      }
    }, [isSelected]);

    useEffect(() => {
      dispatch("core/block-editor")?.setTemplateValidity(true);
    }, []);

    const insertBlockType = (type) => {
      const block = wp.blocks.createBlock(`yt-player/${type}`);
      return dispatch("core/block-editor").insertBlock(block, 0, clientId);
    };

    const appenderToUse = () => {
      if (innerBlocks.length === 0) {
        return <InnerBlocks.ButtonBlockAppender />;
      } else {
        return false;
      }
    };

    if (!innerBlocks?.length) {
      return (
        <div {...blockProps}>
          <Placeholder
            icon={
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="html5-block-icon">
                <polygon points="23 7 16 12 23 17 23 7"></polygon>
                <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
              </svg>
            }
            instructions={__("Choose a video type to get started.", 'yt-player')}
            label={__("Choose a Video Type", 'yt-player')}
          >
            <Button
              isPrimary
              onClick={() => {
                insertBlockType("video");
              }}
            >
              {__("Single Video", 'yt-player')}
            </Button>
          </Placeholder>
          <InnerBlocks templateLock={false} allowedBlocks={["yt-player/video"]} renderAppender={() => false} />
        </div>
      );
    }

    return (
      <div {...blockProps}>
        <InnerBlocks templateLock={false} allowedBlocks={["yt-player/video"]} renderAppender={() => appenderToUse()} />
      </div>
    );
  },

  save: () => {
    const blockProps = useBlockProps.save();
    return (
      <div {...blockProps}>
        <InnerBlocks.Content />
      </div>
    );
  },
});
