import bannerSVG from '../../../assets/img/banner.svg';

const slug = "yt-player";

// ─── Identity & shared props ──────────────────────────────────────────────────
export const dashboardInfo = (info) => {
  const { version, isPremium, hasPro, licenseActiveNonce, adminUrl = '' } = info;

  const proSuffix = isPremium ? ' Pro' : '';

  return {
    adminUrl,
    name: `YT Player${proSuffix}`,
    displayName: `YT Player${proSuffix} - Embed and Customize Video Players`,
    description:
      "YT Player is a modern, lightweight, and fully customizable YouTube video player built for WordPress. Whether you’re adding a single video or creating a video-rich experience across posts, pages, or widget areas, this plugin makes it easy to embed YouTube videos with a sleek, accessible interface powered by HTML5 and the Plyr framework.",
    slug,
    version,
    isPremium,
    hasPro,
    displayOurPlugins: true,
    media: {
      logo: `https://ps.w.org/${slug}/assets/icon-128x128.png`,
      banner: `https://ps.w.org/${slug}/assets/banner-772x250.png`,
      thumbnail: bannerSVG,
      // proThumbnail: `https://bplugins.com/wp-content/themes/b-technologies/assets/images/products/${slug}-pro.png`,
      video: "https://youtu.be/NGvVtSXcZK4",
      isYoutube: true,
    },
    pages: {
      org: `https://wordpress.org/plugins/${slug}/`,
      landing: `https://bplugins.com/products/${slug}/`,
      docs: `https://bplugins.com/docs/${slug}/`,
      pricing: `https://bplugins.com/products/${slug}/pricing`,
    },
    freemius: {
      product_id: 5836,
      plan_id: 9545,
      public_key: "pk_829fc74e7bb67d3d555c68048933d",
    },

    licenseActiveNonce,

    changelogs: [
      {
        version: "2.1.2 - 22 Sep, 2026",
        type: "fix",
        list: [
          "<strong>Security:</strong> Added a missing capability check to the preset read endpoint so only administrators can retrieve saved preset configurations, matching the create/delete actions.",
        ],
      },
      {
        version: "2.1.1 - 6 Sep, 2026",
        type: "fix",
        list: [
          "<strong>Security:</strong> Hardened player data attributes, shortcode parameters, and AJAX endpoints against XSS with enhanced sanitization and escaping.",
          "<strong>Fix:</strong> Aligned Gutenberg Preset modal controls with Admin Settings — locked Pro controls (Restart, Rewind, Fast Forward, Play Large) in Free mode and displayed upgrade notices.",
          "<strong>Fix:</strong> Cleaned up default player control schema to exclude Pro controls by default on Free player instances.",
          "<strong>Fix:</strong> Resolved hideYoutubeUI boolean evaluation (\"false\" / \"0\" string handling) to prevent accidental YouTube UI hiding and iframe cropping.",
          "<strong>Fix:</strong> Enforced strict server-side and client-side Pro feature filtering across player preview, block editor, shortcode, and frontend rendering.",
          "<strong>Fix:</strong> Separated preset overlay background color priority from global admin brand color for accurate custom background rendering.",
          "<strong>Update:</strong> Enhanced dynamic license status checking to seamlessly unlock Pro controls upon activation without requiring page reloads.",
          "<strong>UI/UX:</strong> Reorganized Block Editor inspector sidebar panel titles with intuitive naming (\"Basic Settings\", \"Player Controls\", \"Preset Controls\").",
          "<strong>UI/UX:</strong> Standardized PRO badge styling and feature notice card layouts across settings panels and preset modals.",
          "<strong>Maintenance:</strong> Cleaned up default preset schemas and removed deprecated source files.",
        ],
      },
      {
        version: "2.1.0 - 29 Aug, 2026",
        type: "fix",
        list: [
          "<strong>Security:</strong> Hardened preset data handling and AJAX endpoint security.",
          "<strong>Security:</strong> Improved data sanitization and permission verification.",
          "<strong>Fix:</strong> Resolved quick shortcode URL parsing and player style enqueuing.",
          "<strong>Fix:</strong> Improved clipboard copy compatibility across HTTP and HTTPS environments.",
          "<strong>Fix:</strong> Resolved block editor player controls rendering and SVG icon display.",
          "<strong>Update:</strong> Enhanced UI option consistency and Pro field controls.",
        ],
      }
    ],

    proFeatures: [
      "Keep the video visible in a small floating player while visitors scroll down the page.",
      "Display your custom logo as an overlay on the video with adjustable position, size, and transparency.",
      "Replace the default YouTube preview with your own thumbnail image to better match your content.",
      "Automatically start video playback when the page loads, with the option to begin muted.",
      "Customize the player’s appearance, including colors, controls, and layout, to match your website design.",
      "Hide the default YouTube interface and branding for a cleaner, distraction-free video experience.",
    ],
    startButton: {
      label: "Start Now",
      url: `${adminUrl}post-new.php?post_type=ytplayer`,
    },
  };
}

// ─── Welcome page content ─────────────────────────────────────────────────────
export const welcomeInfo = adminUrl => ({
  keywords: ['Video Player', 'Custom Controls', 'Responsive', 'Lightweight', 'Accessible'],
  keywordsLabel: 'Key Features',
  gettingStarted: {
    tabs: [
      {
        key: 'gutenberg',
        label: 'Gutenberg',
        icon: (
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="7" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            <rect x="14" y="14" width="7" height="7" rx="1" />
          </svg>
        ),
        steps: [
          {
            num: 1,
            title: 'Insert the Block',
            body: 'Click <strong>+</strong> in the editor and search for <strong>YT Player</strong> block.',
            link: { url: `${adminUrl}post-new.php?post_type=page`, label: 'Add New Page' },
          },
          {
            num: 2,
            title: 'Video URL',
            body: 'Paste your YouTube video URL or Video ID in the block settings.',
          },
          {
            num: 3,
            title: 'Configure Player',
            body: 'Customize the player controls, colors, and layout in the sidebar panel.',
          },
          {
            num: 4,
            title: 'Publish Page',
            body: 'Publish or update the page to see your customized video player live!',
          },
        ],
      },
      {
        key: 'shortcode',
        label: 'ShortCode',
        icon: (
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round">
            <polyline points="16 18 22 12 16 6" />
            <polyline points="8 6 2 12 8 18" />
          </svg>
        ),
        steps: [
          {
            num: 1,
            title: 'Create Player Post',
            body: 'Go to <strong>YT Player › Add New</strong> in your admin menu.',
            link: { url: `${adminUrl}post-new.php?post_type=ytplayer`, label: 'Create New' },
          },
          {
            num: 2,
            title: 'Build & Configure',
            body: 'Provide the YouTube URL and customize the player options.',
          },
          {
            num: 3,
            title: 'Copy Shortcode',
            body: 'Publish the post and copy the generated shortcode (e.g. <code>[ytplayer id=123]</code>).',
            link: { url: `${adminUrl}edit.php?post_type=ytplayer`, label: 'All Players' },
          },
          {
            num: 4,
            title: 'Paste Anywhere',
            body: 'Paste the shortcode into any post, page, widget, or page builder text element!',
          },
        ],
      },
      {
        key: 'elementor',
        label: 'Elementor',
        icon: (
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
            <line x1="9" y1="3" x2="9" y2="21" />
            <path d="M13 8h4M13 12h4M13 16h4" />
          </svg>
        ),
        steps: [
          {
            num: 1,
            title: 'Create Player Post',
            body: 'First, go to <strong>YT Player › Add New</strong> to build your player and copy its shortcode.',
            link: { url: `${adminUrl}post-new.php?post_type=ytplayer`, label: 'Create New' },
          },
          {
            num: 2,
            title: 'Edit with Elementor',
            body: 'Open your desired page or post and click <strong>Edit with Elementor</strong>.',
            link: { url: `${adminUrl}edit.php?post_type=page`, label: 'All Pages' },
          },
          {
            num: 3,
            title: 'Add Shortcode Widget',
            body: 'In the Elementor sidebar, search for the <strong>Shortcode</strong> widget and drag it to your layout.',
          },
          {
            num: 4,
            title: 'Paste Shortcode',
            body: 'Paste the YT Player shortcode (e.g. <code>[ytplayer id=123]</code>) into the widget and click Update!',
          },
        ],
      },
    ],
  },
});

// ─── Demos page ───────────────────────────────────────────────────────────────
export const demoInfo = {
  allInOneLabel: "See All Demos",
  allInOneLink: "https://bplugins.com/products/yt-player/#demos",
  demos: [
    {
      title: "Default Preview",
      url: "https://bblockswp.com/demo/default-preview-2/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
    {
      title: "Preview With Play Button",
      url: "https://bblockswp.com/demo/preview-play-button/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
    {
      title: "Custom Logo and Thumbnail",
      url: "https://bblockswp.com/demo/custom-logo-and-thumbnail/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
    {
      title: "Play Button Hide Preview",
      url: "https://bblockswp.com/demo/play-button-hide-preview/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
    {
      title: "Youtube UI Open",
      url: "https://bblockswp.com/demo/youtube-ui-open-hide/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
    {
      title: "Timeline Video Preview",
      url: "https://bblockswp.com/demo/timeline-video-player-preview/",
      icon: (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
          <path d="M64 0C28.7 0 0 28.7 0 64V352c0 35.3 28.7 64 64 64H240l-10.7 32H160c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H346.7L336 416H512c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H64zM512 64V288H64V64H512z"></path>
        </svg>
      ),
      type: "iframe",
    },
  ],
};

// ─── Pricing page ─────────────────────────────────────────────────────────────
export const pricingInfo = {
  logo: `https://ps.w.org/${slug}/assets/icon-128x128.png`, // Optional
  pluginId: 5836,
  planId: 9545,
  licenses: [
    1,
    3,
    null
  ],
  button: {
    label: 'Buy Now ➜'
  },
  featured: {
    selected: 3, // choose from licenses item
  }
};