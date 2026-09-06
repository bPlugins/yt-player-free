// ✅ Make sure it's globally accessible
window.copyBPlAdminShortcode = async function (postID) {
  const wrapper = document.querySelector(`#bPlAdminShortcode-${postID}`);
  if (!wrapper) return;

  const input = wrapper.querySelector("input");
  const tooltip = wrapper.querySelector(".tooltip");
  const text = input.value;

  try {
    await navigator.clipboard.writeText(text);
    tooltip.textContent = "Copied!";
    tooltip.style.opacity = 1;
    tooltip.style.visibility = "visible";

    setTimeout(() => {
      tooltip.style.opacity = 0;
      tooltip.style.visibility = "hidden";
      tooltip.textContent = "Copy to Clipboard";
    }, 1500);
  } catch (err) {
    fallbackCopy(text, tooltip);
  }
};

function fallbackCopy(text, tooltip) {
  const temp = document.createElement("textarea");
  temp.value = text;
  document.body.appendChild(temp);
  temp.select();
  temp.setSelectionRange(0, 99999);

  try {
    document.execCommand("copy");
    tooltip.textContent = "Successful Copied!";
  } catch (err) {
    tooltip.textContent = "Failed!";
  }

  tooltip.style.opacity = 1;
  tooltip.style.visibility = "visible";
  tooltip.style.width = "120px";
  tooltip.style.textAlign = 'center';
  tooltip.style.fontWeight = '400';
  tooltip.style.fontSize = '14px'
  tooltip.style.padding = "3px 5px";

  setTimeout(() => {
    tooltip.style.opacity = 0;
    tooltip.style.visibility = "hidden";
    tooltip.textContent = "Copy to Clipboard";
  }, 1500);

  document.body.removeChild(temp);
}


document.addEventListener('DOMContentLoaded', function () {
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.ytp_shortcode_copy_btn');
    if (!btn) return;
    
    e.preventDefault();
    const shortcode = btn.getAttribute('data-shortcode');
    if (shortcode) {
      const span = btn.querySelector('.copy-text') || (btn.parentElement && btn.parentElement.querySelector('.copy-text'));
      const originalText = span ? span.textContent : '';

      const showSuccess = () => {
        if (span) {
          span.textContent = 'Copied!';
          setTimeout(() => { span.textContent = originalText; }, 1500);
        }
      };

      let success = false;
      
      // Try synchronous execCommand first (to prevent losing user gesture in iframes/Promises)
      try {
        const temp = document.createElement("textarea");
        temp.value = shortcode;
        document.body.appendChild(temp);
        temp.select();
        temp.setSelectionRange(0, 99999);
        success = document.execCommand("copy");
        document.body.removeChild(temp);
      } catch (err) {
        success = false;
      }

      if (success) {
        showSuccess();
      } else if (navigator.clipboard && window.isSecureContext) {
        // Fallback to modern clipboard API if execCommand fails
        navigator.clipboard.writeText(shortcode).then(showSuccess).catch(err => {
          // eslint-disable-next-line no-console
          console.error('Copy failed', err);
        });
      }
    }
  });
});
