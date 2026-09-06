import { setAutoFreeze } from "immer";
import { useEffect, useRef, useState } from "react";

setAutoFreeze(false);

const YouTube = ({ attributes, className, preset }) => {
  const {
    align = "",
    uniqueId,
    brandLogo = {},
    source,
    thumbnail,
    customBanner = {},
  } = attributes;
  const { isBrandLogo = false, brandUrl = "" } = brandLogo;
  const { isCustomBanner = false, bannerUrl = "" } = customBanner;

  const [isFloating, setIsFloating] = useState(false);
  const [isClosed, setIsClosed] = useState(false);
  const [showPoster, setShowPoster] = useState(false);
  const isPremium = typeof window.ytpPlayer !== "undefined" && window.ytpPlayer !== null ? Boolean(window.ytpPlayer.is_premium) : false;

  const containerRef = useRef(null);
  const playerRef = useRef(null);

  // ----------------------------
  // ✅ Helper to parse YouTube source (works for URL or ID)
  // ----------------------------
  function parseYouTubeSource(input) {
    const trimmed = (input || "").trim();
    if (!trimmed) return { id: null, embedUrl: "" };

    // Try parse as URL
    try {
      const url = new URL(trimmed);
      let id = null;

      if (url.hostname.includes("youtu.be")) {
        id = url.pathname.replace(/^\//, "");
      } else if (url.hostname.includes("youtube.com")) {
        if (url.searchParams.get("v")) {
          id = url.searchParams.get("v");
        } else {
          const parts = url.pathname.split("/").filter(Boolean);
          if (parts.length) id = parts[parts.length - 1];
        }
      }

      if (id) {
        return { id, embedUrl: `https://www.youtube.com/embed/${id}` };
      }

      const embedMatch = trimmed.match(/\/embed\/([0-9A-Za-z_-]{6,})/);
      if (embedMatch) {
        return {
          id: embedMatch[1],
          embedUrl: `https://www.youtube.com/embed/${embedMatch[1]}`,
        };
      }

      return { id: null, embedUrl: trimmed };
    } catch (e) {
      if (/^[0-9A-Za-z_-]{11}$/.test(trimmed)) {
        return {
          id: trimmed,
          embedUrl: `https://www.youtube.com/embed/${trimmed}`,
        };
      }
      if (/^[0-9A-Za-z_-]{6,}$/.test(trimmed)) {
        return {
          id: trimmed,
          embedUrl: `https://www.youtube.com/embed/${trimmed}`,
        };
      }
      return { id: null, embedUrl: trimmed };
    }
  }

  // ----------------------------
  // ✅ Compute videoId + embedUrl + thumbnail
  // ----------------------------
  const { id: videoId, embedUrl } = parseYouTubeSource(source || "");
  const finalThumbnail =
    thumbnail ||
    (videoId ? `https://img.youtube.com/vi/${videoId}/hqdefault.jpg` : "");

  // Helper for adding params to URL
  function appendParams(urlStr, params) {
    try {
      const u = new URL(urlStr, window.location.origin);
      Object.keys(params).forEach((k) => u.searchParams.set(k, params[k]));
      return u.toString();
    } catch (e) {
      const sep = urlStr.indexOf("?") === -1 ? "?" : "&";
      const q = Object.entries(params)
        .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`)
        .join("&");
      return `${urlStr}${sep}${q}`;
    }
  }

  const iframeSrc = embedUrl
    ? appendParams(embedUrl, {
      origin: window.location.origin,
      iv_load_policy: 3,
      modestbranding: 1,
      playsinline: 1,
      showinfo: 0,
      rel: 0,
      enablejsapi: 1,
    })
    : "";

  // ----------------------------
  // ✅ Plyr initialize
  // ----------------------------
  useEffect(() => {
    if (containerRef.current) {
      const targetDoc = containerRef.current.ownerDocument || document;
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
          .catch(() => { });
      }
    }

    const rawOptions = preset?.preset?.options || attributes?.options || {};
    const proControls = ["restart", "rewind", "fast-forward"];
    const rawControls = rawOptions.controls || [];
    const filteredControls = isPremium
      ? rawControls
      : rawControls.filter((c) => !proControls.includes(c));

    const options = {
      ...rawOptions,
      controls: filteredControls,
      seekTime: parseInt(rawOptions.seekTime) || 10,
      hideControls: rawOptions.autoHideControl !== false,
    };

    const player = new Plyr(
      containerRef.current?.querySelector("#player"),
      options,
    );
    playerRef.current = player;

    window.allYTPlyrInstances = window.allYTPlyrInstances || [];
    window.allYTPlyrInstances.push(player);

    player.on("play", () => {
      window.allYTPlyrInstances.forEach((p) => {
        if (p !== player && p.playing) {
          try {
            p.pause();
          } catch (e) {
            // ignore
          }
        }
      });
    });

    player.on("ready", () => {
      player.currentTime = attributes.startTime || 0;
      const speed = rawOptions?.speed?.selected || 1;
      player.speed = speed;
    });

    return () => {
      try {
        player.destroy();
      } catch (e) {
        /* ignore */
      }
      window.allYTPlyrInstances = window.allYTPlyrInstances.filter(
        (p) => p !== player,
      );
    };
  }, [attributes?.options, preset?.preset?.options]);

  // ----------------------------
  // ✅ Poster show/hide on pause/play
  // ----------------------------
  useEffect(() => {
    const player = playerRef.current;
    if (!player || !(preset?.preset?.thumbInPause ?? attributes.thumbInPause))
      return;

    const handlePause = () => {
      if (preset?.preset?.thumbInPause ?? attributes.thumbInPause) {
        setShowPoster(true);
      }
    };
    const handlePlay = () => {
      setShowPoster(false);
    };

    player.on("pause", handlePause);
    player.on("play", handlePlay);

    return () => {
      try {
        player.off("pause", handlePause);
        player.off("play", handlePlay);
      } catch (e) {
        /* ignore */
      }
    };
  }, [preset?.preset?.thumbInPause]);

  useEffect(() => {
    if (preset?.preset?.options?.autoplay) {
      setShowPoster(false);
    }
  }, []);

  // ----------------------------
  // ✅ Repeat or Reset on End
  // ----------------------------
  useEffect(() => {
    const player = playerRef.current;
    if (!player) return;

    const handleEnded = () => {
      if (preset?.preset?.options?.repeat || attributes.options?.repeat) {
        player.currentTime = 0;
        player.play();
      } else if (preset?.preset?.options?.resetOnEnd) {
        player.currentTime = 0;
      }
    };

    player.on("ended", handleEnded);
    return () => {
      try {
        player.off("ended", handleEnded);
      } catch (e) {
        /* ignore */
      }
    };
  }, [preset?.preset?.options?.repeat, preset?.preset?.options?.resetOnEnd]);

  // ----------------------------
  // ✅ Set Plyr source dynamically
  // ----------------------------
  useEffect(() => {
    if (!playerRef.current || !source) return;

    if (!videoId) return;

    playerRef.current.source = {
      type: "video",
      sources: [
        {
          src: videoId,
          provider: "youtube",
        },
      ],
    };
  }, [source]);

  // ----------------------------
  // ✅ Floating mini player
  // ----------------------------
  useEffect(() => {
    const wrapper = containerRef.current?.querySelector(".ytWrapper");
    const handleScroll = () => {
      if (!containerRef.current || isClosed) return;

      const rect = containerRef.current.getBoundingClientRect();
      if (!playerRef.current?.playing) return;

      if (rect.top < -100) {
        if (!isFloating) {
          containerRef.current.style.height = `${containerRef.current.offsetHeight}px`;
        }
        setIsFloating(true);
        wrapper.classList.add("floating-player");
      } else {
        setIsFloating(false);
        wrapper.classList.remove("floating-player");
        containerRef.current.style.height = '';
      }
    };

    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, [isClosed, isFloating]);

  const closeMiniPlayer = () => {
    setIsFloating(false);
    setIsClosed(true);
  };

  const rawHideYoutubeUI = preset?.preset?.hideYoutubeUI !== undefined ? preset.preset.hideYoutubeUI : attributes.hideYoutubeUI;
  const isHideYoutubeUI = isPremium && (rawHideYoutubeUI === true || rawHideYoutubeUI === "true" || rawHideYoutubeUI == "1");

  if (!source) return null;

  // ----------------------------
  // ✅ JSX Output
  // ----------------------------
  return (
    <div
      ref={containerRef}
      id={uniqueId}
      className={`ytPlayer ${align} ${className}`}
    >
      <style>{attributes.CSS}</style>

      <div
        className={`ytWrapper ${isFloating ? "floating-player" : ""} ${isHideYoutubeUI ? "hideYoutubeUI" : ""} ${(preset?.preset?.hideControlsWhenPause !== undefined ? (preset.preset.hideControlsWhenPause == "1" || preset.preset.hideControlsWhenPause === true) : attributes.hideControlsWhenPause)
            ? "hideControlsWhenPause"
            : ""
          } `}
        style={{
          borderRadius:
            preset?.preset?.plyrStyle?.borderRadius ||
            attributes.plyrStyle?.borderRadius,
        }}
      >
        <div
          className="plyr__video-embed"
          data-unique-id={uniqueId}
          id="player"
        >
          <iframe
            src={iframeSrc}
            allowFullScreen
            allowTransparency
            allow="autoplay"
          ></iframe>

          <div
            className={`plyr__poster ${showPoster ? "show" : ""}`}
            style={{
              backgroundImage: `url(${isCustomBanner ? bannerUrl : finalThumbnail
                })`,
            }}
          >
          </div>
        </div>

        {isBrandLogo && brandUrl && (
          <div className="custom-overlay">
            <img src={brandUrl} alt="Brand Logo" />
          </div>
        )}

        {isFloating && (
          <button
            className="close-mini-player"
            onClick={closeMiniPlayer}
            aria-label="Close"
          >
            <svg
              width="12"
              height="12"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="3"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        )}
      </div>
    </div>
  );
};

export default YouTube;
