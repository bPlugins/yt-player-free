import React from "react-dom";
import "./admin.scss";
import App from "./Components/App";
import { dashboardInfo } from "./utils/data";
document.addEventListener("DOMContentLoaded", () => {
  const adminEl = document.getElementById("ytPlayerDashboard");
  let info = {};

  try {
    if (adminEl?.dataset?.info !== undefined || adminEl?.dataset?.info !== "") {
      info = JSON.parse(adminEl?.dataset?.info);
    }
  } catch {
    // eslint-disable-next-line no-console
    console.warn("Not Info");
  }

  React.createRoot(adminEl).render(<App {...dashboardInfo(info)} />);
});
