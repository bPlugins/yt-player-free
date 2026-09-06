/* eslint-disable no-console */
// Re-trigger build
document.addEventListener("DOMContentLoaded", () => {
  window.allYTPlyrInstances = window.allYTPlyrInstances || [];

  document.querySelectorAll(".ytp-player").forEach((el) => {
    let extraOptions = {};
    try {
      const dataOptions = el.getAttribute("data-options");
      if (dataOptions) {
        extraOptions = JSON.parse(dataOptions);
      }
    } catch (e) {
      console.error("Failed to parse Plyr data-options", e);
    }

    const isPremium = typeof window.ytpPlayer !== "undefined" && window.ytpPlayer !== null ? Boolean(window.ytpPlayer.is_premium) : false;
    if (!isPremium && Array.isArray(extraOptions.controls)) {
      const proControls = ["restart", "rewind", "fast-forward"];
      extraOptions.controls = extraOptions.controls.filter((c) => !proControls.includes(c));
    }

    const player = new Plyr(el.querySelector(".plyr__video-embed"), {
      ...extraOptions,
      youtube: {
        noCookie: false,
        rel: 0,
        showinfo: 0,
        iv_load_policy: 3,
        modestbranding: 1,
        start: 0,
      },
    });

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
  });
});
