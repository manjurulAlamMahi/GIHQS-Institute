import { useCallback, useEffect, useMemo, useRef, useState } from "react"
import { Menu, X } from "lucide-react"
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton"

/** One navigable section reported by the document. */
interface Section {
  id: string
  label: string
}

interface Props {
  /** Viewer URL on the API origin, e.g. https://api.example.com/api/html/12 */
  src: string
  title?: string
  /** Vertical space taken by surrounding chrome. */
  offset?: number
}

/**
 * Renders an uploaded HTML document.
 *
 * The document runs in an iframe on the API origin, so its CSS and scripts are
 * sealed away from the platform: uploaded styles cannot reach the dashboard and
 * dashboard styles cannot reach the document.
 *
 * Documents that ship their own navigation are served untouched and simply
 * appear as-is. Documents that do not are served with a small injected script
 * that reports their sections over postMessage; when that happens this component
 * renders the sidebar itself, outside the iframe, so it stays put while the
 * document scrolls.
 */
export default function HtmlResourceViewer({ src, title, offset = 114 }: Props) {
  const iframeRef = useRef<HTMLIFrameElement>(null)
  const [sections, setSections] = useState<Section[]>([])
  const [activeId, setActiveId] = useState<string | null>(null)
  const [isLoaded, setIsLoaded] = useState(false)
  const [mobileNavOpen, setMobileNavOpen] = useState(false)

  /** Only messages from the origin serving the document are trusted. */
  const documentOrigin = useMemo(() => {
    try {
      return new URL(src, window.location.href).origin
    } catch {
      return null
    }
  }, [src])

  useEffect(() => {
    setSections([])
    setActiveId(null)
    setIsLoaded(false)
  }, [src])

  useEffect(() => {
    const onMessage = (event: MessageEvent) => {
      if (!documentOrigin || event.origin !== documentOrigin) return
      if (event.source !== iframeRef.current?.contentWindow) return

      const data = event.data
      if (!data || typeof data !== "object") return

      if (data.type === "gihqs:sections" && Array.isArray(data.sections)) {
        const received: Section[] = data.sections
          .filter((s: unknown): s is Section =>
            !!s && typeof (s as Section).id === "string" && typeof (s as Section).label === "string"
          )
          .map((s: Section) => ({ id: s.id, label: s.label }))
        setSections(received)
        setActiveId((current) => current ?? received[0]?.id ?? null)
      }

      if (data.type === "gihqs:active" && typeof data.id === "string") {
        setActiveId(data.id)
      }
    }

    window.addEventListener("message", onMessage)
    return () => window.removeEventListener("message", onMessage)
  }, [documentOrigin])

  const goTo = useCallback(
    (id: string) => {
      setActiveId(id)
      setMobileNavOpen(false)
      iframeRef.current?.contentWindow?.postMessage(
        { type: "gihqs:scrollTo", id },
        documentOrigin ?? "*"
      )
    },
    [documentOrigin]
  )

  const hasSidebar = sections.length > 0
  const frameHeight = `calc(100vh - ${offset}px)`

  const navList = (
    <nav className="flex flex-col gap-1" aria-label={title ? `${title} sections` : "Document sections"}>
      {sections.map((section, index) => {
        const isActive = section.id === activeId
        return (
          <button
            key={section.id}
            type="button"
            onClick={() => goTo(section.id)}
            aria-current={isActive ? "true" : undefined}
            className={`flex items-start gap-2.5 rounded-lg px-3 py-2.5 text-left text-[14px] font-semibold transition-colors focus-visible:ring-2 focus-visible:ring-[#14392f] focus-visible:outline-none ${
              isActive
                ? "bg-[#edf5f1] text-[#14392f]"
                : "text-[#4b5a53] hover:bg-neutral-100 hover:text-[#14392f]"
            }`}
          >
            <span className="mt-0.5 min-w-[1.25rem] text-[12px] font-bold tabular-nums text-[#9aa8a1]">
              {index + 1}
            </span>
            <span className="flex-1">{section.label}</span>
          </button>
        )
      })}
    </nav>
  )

  return (
    <div className="flex w-full flex-col md:flex-row" style={{ height: frameHeight }}>
      {hasSidebar && (
        <>
          {/* Mobile: the sidebar collapses to a toggle so it never eats the screen. */}
          <div className="border-b border-border bg-white px-4 py-2 md:hidden">
            <button
              type="button"
              onClick={() => setMobileNavOpen((open) => !open)}
              className="inline-flex items-center gap-2 rounded-lg px-2 py-1.5 text-[14px] font-semibold text-[#14392f]"
              aria-expanded={mobileNavOpen}
            >
              {mobileNavOpen ? <X className="h-4 w-4" /> : <Menu className="h-4 w-4" />}
              Sections
            </button>
            {mobileNavOpen && <div className="mt-2 max-h-[45vh] overflow-y-auto pb-2">{navList}</div>}
          </div>

          <aside className="hidden w-[260px] shrink-0 overflow-y-auto border-r border-border bg-white p-4 md:block">
            {title && (
              <h2 className="mb-3 px-3 text-[11px] font-bold tracking-[0.12em] text-[#9aa8a1] uppercase">
                {title}
              </h2>
            )}
            {navList}
          </aside>
        </>
      )}

      <div className="relative min-w-0 flex-1 bg-white">
        {!isLoaded && (
          <div className="absolute inset-0 z-10 overflow-hidden bg-white">
            <DocumentSkeleton />
          </div>
        )}
        <iframe
          ref={iframeRef}
          src={src}
          onLoad={() => setIsLoaded(true)}
          title={title ?? "Course document"}
          scrolling="auto"
          className={`h-full w-full border-none transition-opacity duration-300 ${
            isLoaded ? "opacity-100" : "opacity-0"
          }`}
        />
      </div>
    </div>
  )
}
