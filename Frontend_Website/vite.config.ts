/// <reference types="vitest" />
import path from "path"
import tailwindcss from "@tailwindcss/vite"
import react from "@vitejs/plugin-react"
import { defineConfig } from "vitest/config"
import svgr from "vite-plugin-svgr"
// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss(), svgr()],
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: "./src/setupTests.ts",
  },
  server: {
    headers: {
      "Content-Security-Policy":
        "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; frame-src https: 'self';",
      "X-Frame-Options": "SAMEORIGIN",
      "X-Content-Type-Options": "nosniff",
      "Referrer-Policy": "strict-origin-when-cross-origin",
      "Permissions-Policy": "camera=(), microphone=(), geolocation=()",
      "X-XSS-Protection": "1; mode=block",
      "Cross-Origin-Opener-Policy": "same-origin",
      "Cross-Origin-Resource-Policy": "cross-origin",
      "Cross-Origin-Embedder-Policy": "unsafe-none",
    },
    proxy: {
      "/uploads": {
        target: "https://ramzi113.thenightowl.team",
        changeOrigin: true,
      },
    },
  },
})
