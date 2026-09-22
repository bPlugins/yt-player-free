# YT Player — The Ultimate YouTube Video Player & Timeline for WordPress

![YT Player Banner](https://ps.w.org/yt-player/assets/banner-772x250.png)

[![WordPress Support](https://img.shields.io/badge/WordPress-6.5+-blue.svg?style=flat-square&logo=wordpress)](https://wordpress.org/plugins/yt-player/)
[![PHP Support](https://img.shields.io/badge/PHP-7.1+-777bb4.svg?style=flat-square&logo=php)](./readme.txt)
[![GPLv3 License](https://img.shields.io/badge/License-GPLv3-green.svg?style=flat-square)](./readme.txt)
[![Stable Version](https://img.shields.io/badge/Version-2.1.2-blue.svg?style=flat-square)](./readme.txt)

**YT Player** is a modern, lightweight, and fully customizable YouTube video player plugin for WordPress. Powered by HTML5 and the Plyr framework, it lets you embed YouTube videos, video playlists, and timeline galleries seamlessly into posts, pages, widget areas, or custom templates using shortcodes or native Gutenberg blocks—no coding required!

---

## 🎬 Video Walkthrough: Quick Start & Feature Tour

Watch our comprehensive video guide exploring setup, custom player presets, layout configurations, and player controls:

[![YT Player Video Walkthrough](https://img.youtube.com/vi/NGvVtSXcZK4/hqdefault.jpg)](https://www.youtube.com/watch?v=NGvVtSXcZK4)

<p align="center">
  <a href="https://www.youtube.com/watch?v=NGvVtSXcZK4" target="_blank" rel="noopener noreferrer">
    <img src="https://img.shields.io/badge/YouTube-Watch%20Quick%20Start%20Guide-red?style=for-the-badge&logo=youtube" alt="Watch Quick Start Guide on YouTube" />
  </a>
</p>

---

## 🚀 Key Features

### 💎 Core Functionality (Free)
Everything you need to build a high-performance YouTube video playback experience on your site:
- **Gutenberg Block Editor Support**: Native 'Video Player for YouTube' block for seamless, drag-and-drop page building.
- **Flexible Shortcode Embedding**: Embed videos instantly using `[ytplayer id="123"]` or quick shortcode `[ytp]https://www.youtube.com/watch?v=...[/ytp]`.
- **URL & Video ID Support**: Accepts full YouTube URLs or 11-character Video IDs effortlessly.
- **Custom Player Presets**: Save and apply reusable player control configurations across multiple pages.
- **Full Player Control Customization**: Selectively show or hide playback controls (Play, Pause, Progress Bar, Volume, Fullscreen, Mute).
- **Responsive Layout**: Player automatically adjusts to fit desktops, tablets, and mobile screens seamlessly.
- **Keyboard Navigation & Fullscreen**: Enables native full-screen mode and keyboard shortcut accessibility.
- **Modern React Admin Dashboard**: Easy-to-use backend dashboard built with React and WP-Utils for fast setup.
- **Fast & Lightweight Performance**: High-efficiency loading powered by the Plyr.io library.

### 👑 Premium Power (Pro)
Unlock advanced video player customization, custom branding, and interactive layouts:
- **Floating Mini Player (Sticky Video)**: Keeps videos playing in a customizable floating corner window as visitors scroll down the page.
- **Custom Brand Overlay Logo**: Add your company logo as an overlay on the video with position, size, radius, and opacity settings.
- **Custom Video Cover Thumbnails**: Replace default YouTube cover images with custom high-resolution banner thumbnails.
- **Auto Play & Muted Playback**: Automatically start video playback when the page loads, with automatic muted start options to comply with browser autoplay policies.
- **Hide Controls on Pause**: Maintain a clean player screen by automatically hiding playback controls when paused.
- **Distraction-Free YouTube UI**: Hide native YouTube interface elements, titles, and branding.
- **Timeline Video Block & Gallery**: Build multi-video timeline showcases with interactive video item navigation.
- **Custom Seek Time Intervals**: Customize forward and rewind skip durations in seconds.
- **Unlimited Presets & Direct Support**: Access unlimited saved preset configurations and priority customer support.

---

## 📸 Visual Showcase

### Interactive Gutenberg Block
Easily insert YouTube video players using the native Gutenberg block editor. Configure controls, toggle brand logos, and preview your player live in the backend editor.

### Universal Compatibility
Fully compatible with Chrome, Firefox, Safari, Edge, and optimized for modern iOS (iPhone/iPad) and Android mobile browsers.

### Sleek Floating Mini Player
Keep your audience engaged! The Pro floating player seamlessly transitions into a docked corner player when users scroll down long articles.

---

## 🛠 Technical Stack

This project is built using modern web development practices:

- **Frontend Framework:** [React](https://reactjs.org/) (Powers Gutenberg editor blocks and the WP React admin dashboard).
- **Core Video Engine:** [Plyr.io](https://plyr.io/) for accessible, responsive HTML5 media playback.
- **Build System:** `@wordpress/scripts` (Webpack) and Gulp task automation.
- **Styling:** Vanilla CSS and modular SASS/SCSS.
- **Backend:** PHP OOP architecture with REST API endpoints and Custom Post Types (`ytplayer`).
- **Licensing & SDK:** Integrated **Freemius SDK** for licensing, updates, and opt-in telemetry.

---

## 📚 Third-Party Libraries

YT Player incorporates the following open-source and third-party libraries:

- **[Plyr](https://github.com/sampotts/plyr)** (MIT License): Core accessible HTML5 media player framework.
- **[React](https://reactjs.org/)** (MIT License): Interactive Gutenberg blocks and admin dashboard UI.
- **[SweetAlert2](https://github.com/sweetalert2/sweetalert2)** (MIT License): Beautiful modal alerts and confirm dialogs.
- **[Freemius SDK](https://freemius.com/)**: Plugin licensing and update management framework.

---

## 💻 Developer Guide

### Directory Structure
- **`/src`**: Source JavaScript and SCSS files for editor blocks and dashboard.
  - **`/blocks`**: Block definitions (`video`, `timeline`, `Preset`, `parent`).
  - **`/admin-dashboard`**: React application source code for the plugin dashboard.
- **`/inc`**: PHP core logic, REST controllers, database tables, and model classes.
- **`/public`**: Enqueued frontend CSS and JS assets (`plyr.js`, `player-style.css`).
- **`/build`**: Compiled assets (generated automatically via Webpack build pipeline).
- **`yt-player.php`**: Primary WordPress plugin entry file.
- **`youtube-player.php`**: Core block registration and localized script bindings.

### Development Workflow
1. **Clone the repository** into your local WordPress `wp-content/plugins/` directory:
   ```bash
   git clone https://github.com/bPlugins/yt-player.php.git yt-player
   ```
2. **Install node dependencies**:
   ```bash
   npm install
   ```
3. **Start watch compiler**:
   ```bash
   npm start
   ```
4. **Build production bundle**:
   ```bash
   npm run build
   ```

### Data Flow & Lifecycle
1. **Editor:** Block attributes are managed inside React components (`src/blocks/video/edit.js`).
2. **Database:** Post metadata and preset settings are saved via AJAX / REST API into custom database tables (`wp_ytp_presets`) and custom post types (`ytplayer`).
3. **Frontend (PHP):** `render.php` fetches saved preset configurations and renders target HTML containers with `data-attributes`.
4. **Frontend (JS):** `frontend.js` initializes the Plyr instance and applies brand overlay and player control options.

---

## 🔌 Developer API

### Shortcode Examples

Embed a saved player configuration by ID:
```html
[ytplayer id="123"]
```

Quick embed shortcode with direct YouTube URL:
```html
[ytp]https://www.youtube.com/watch?v=dQw4w9WgXcQ[/ytp]
```

---

## 🔗 Useful Links

- [Live Demo](https://bplugins.com/products/yt-player/#demos)
- [Documentation](https://bplugins.com/docs/yt-player)
- [Upgrade to Pro](https://bplugins.com/products/yt-player/pricing)
- [Support Forum](https://wordpress.org/support/plugin/yt-player/)

---
*Developed with ❤️ by [bPlugins](https://bplugins.com)*
