import { useRef, useState, useEffect } from "react";
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton";

export default function AutoIframe({ src, fixedHeight = false }: { src: string; fixedHeight?: boolean }) {
  const iframeRef = useRef<HTMLIFrameElement>(null);
  const [isLoaded, setIsLoaded] = useState(false);
  const [proxySrc, setProxySrc] = useState(src);

  useEffect(() => {
    let iframeUrl = src;
    // Route it through the Vite proxy during local development so we can access iframe contents
    if (iframeUrl.startsWith('https://ramzi113.thenightowl.team/uploads')) {
      iframeUrl = iframeUrl.replace('https://ramzi113.thenightowl.team', '');
    }
    setProxySrc(iframeUrl);
  }, [src]);

  const handleLoad = () => {
    const iframe = iframeRef.current;
    if (iframe && iframe.contentWindow && !fixedHeight) {
      try {
        const body = iframe.contentWindow.document.body;
        const html = iframe.contentWindow.document.documentElement;

        const updateHeight = () => {
          const height = Math.max(
            body.scrollHeight,
            body.offsetHeight,
            html.clientHeight,
            html.scrollHeight,
            html.offsetHeight
          );
          iframe.style.height = `${height}px`;
        };

        // Update height initially
        updateHeight();

        // Listen for internal DOM changes (like opening FAQ accordion)
        const observer = new MutationObserver(updateHeight);
        observer.observe(body, { childList: true, subtree: true, attributes: true });

        // Ensure we update on resize events
        const resizeObserver = new ResizeObserver(updateHeight);
        resizeObserver.observe(body);
      } catch (err) {
        console.error("Cannot resize iframe due to cross-origin constraints:", err);
      } finally {
        setIsLoaded(true);
      }
    } else {
      setIsLoaded(true);
    }
  };

  return (
    <div 
      className={`relative w-full bg-white ${fixedHeight ? "" : "min-h-screen"}`}
      style={fixedHeight ? { height: 'calc(100vh - 114px)' } : {}}
    >
      {!isLoaded && (
        <div className={`absolute inset-x-0 top-0 z-10 w-full bg-white ${fixedHeight ? "h-full" : ""}`}>
          <DocumentSkeleton />
        </div>
      )}
      <iframe
        ref={iframeRef}
        src={proxySrc}
        onLoad={handleLoad}
        className={`w-full border-none transition-opacity duration-300 ${isLoaded ? "opacity-100" : "opacity-0"}`}
        title="Injected Content"
        scrolling={fixedHeight ? "auto" : "no"}
        style={fixedHeight ? { height: "100%" } : { minHeight: "100vh", overflow: "hidden" }}
      />
    </div>
  );
}
