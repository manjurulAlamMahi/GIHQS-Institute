import { StrictMode } from "react"
import { createRoot } from "react-dom/client"
import * as Sentry from "@sentry/react"
import "./index.css"
import AppProviders from "@/providers/AppProviders"

Sentry.init({
  dsn: "https://5ad14d0d2ba239b114c9e27c0f8e3b6e@o4511851559256064.ingest.us.sentry.io/4511851582521344",
  dataCollection: {
    // To disable sending user data and HTTP bodies, uncomment the lines below. For more info visit:
    // https://docs.sentry.io/platforms/javascript/guides/react/configuration/options/#dataCollection
    // userInfo: false,
    // httpBodies: []
  }
})

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <AppProviders />
  </StrictMode>
)