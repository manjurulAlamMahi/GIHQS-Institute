import ReactGA from "react-ga4"

export const initGoogleAnalytics = () => {
  const trackingId = import.meta.env.VITE_GA_MEASUREMENT_ID

  if (trackingId) {
    ReactGA.initialize(trackingId)
    // Send initial pageview
    ReactGA.send({ hitType: "pageview", page: window.location.pathname })
    // console.log("Analytics initialized.");
  } else {
    console.warn("Analytics tracking ID not found in environment variables.")
  }
}
