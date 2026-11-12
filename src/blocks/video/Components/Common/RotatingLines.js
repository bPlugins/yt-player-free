// A small dependency-free stand-in for react-loader-spinner's <RotatingLines>.
// That package ships its dist build pre-compiled with the automatic JSX
// runtime, which @wordpress/dependency-extraction-webpack-plugin can't
// externalize, so it gets inlined into the build output (flagged by the
// Plugin Check tool as a future React-19 compatibility risk). This renders
// the same visual (a spinning ring) with plain SVG + CSS, keeping the same
// prop names so call sites didn't need to change.
const RotatingLines = ({
  visible = true,
  height = "96",
  width = "96",
  strokeColor = "#00b2ff",
  strokeWidth = "5",
  animationDuration = "0.75",
  ariaLabel = "rotating-lines-loading",
  wrapperStyle = {},
  wrapperClass = "",
}) => {
  if (!visible) return null;

  return (
    <div
      className={wrapperClass}
      style={{ display: "inline-block", ...wrapperStyle }}
      aria-label={ariaLabel}
    >
      <svg
        height={height}
        width={width}
        viewBox="0 0 100 100"
        style={{
          animation: `ytp-rotating-lines-spin ${animationDuration}s linear infinite`,
        }}
      >
        <circle
          cx="50"
          cy="50"
          r="35"
          fill="none"
          stroke={strokeColor}
          strokeWidth={strokeWidth}
          strokeDasharray="165"
          strokeDashoffset="55"
          strokeLinecap="round"
        />
      </svg>
      <style>{`
        @keyframes ytp-rotating-lines-spin {
          100% { transform: rotate(360deg); }
        }
      `}</style>
    </div>
  );
};

export default RotatingLines;
