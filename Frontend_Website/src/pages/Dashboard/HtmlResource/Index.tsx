import { useCallback, useEffect, useState } from "react"
import { Link, useParams } from "react-router"
import { ArrowLeft, KeyRound, Loader2, Lock, ShieldX } from "lucide-react"
import {
  useGetPurchasedCatalogueByIdQuery,
  useGetHtmlResourceTicketMutation,
  useRedeemHtmlResourceKeyMutation,
} from "@/features/profile/api/profileApi"
import HtmlResourceViewer from "@/components/shared/HtmlResourceViewer"
import { DocumentSkeleton } from "@/components/shared/DocumentSkeleton"
import { ROUTES } from "@/routes/routes.constants"

type Blocker = "license_required" | "license_expired" | "license_revoked" | "no_access" | null

/**
 * Viewer page for one uploaded HTML document.
 *
 * The document is never linked to directly. On mount the page exchanges the
 * user's token for a single-use ticket and points the iframe at that, which is
 * both how an iframe authenticates at all and why a copied URL is useless.
 */
export default function DashboardHtmlResourcePage() {
  const { id, resourceId } = useParams()
  const { data, isLoading, isError } = useGetPurchasedCatalogueByIdQuery(id as string, { skip: !id })

  const [requestTicket, { isLoading: minting }] = useGetHtmlResourceTicketMutation()
  const [redeemKey, { isLoading: redeeming }] = useRedeemHtmlResourceKeyMutation()

  const [ticketUrl, setTicketUrl] = useState<string | null>(null)
  const [blocker, setBlocker] = useState<Blocker>(null)
  const [keyValue, setKeyValue] = useState("")
  const [keyError, setKeyError] = useState<string | null>(null)

  const catalogue = data?.data?.catalogue
  const resource = catalogue?.html_resources?.find((r) => String(r.id) === resourceId)
  const backTo = ROUTES.DASHBOARD_COURSE_DETAIL.replace(":id", String(id))

  const openDocument = useCallback(async () => {
    if (!resourceId) return
    setKeyError(null)

    try {
      const response = await requestTicket(resourceId).unwrap()
      setTicketUrl(response.data.url)
      setBlocker(null)
    } catch (error) {
      const reason = (error as { data?: { data?: { reason?: string } } })?.data?.data?.reason
      setTicketUrl(null)
      setBlocker(
        reason === "license_required" ||
          reason === "license_expired" ||
          reason === "license_revoked"
          ? reason
          : "no_access"
      )
    }
  }, [requestTicket, resourceId])

  useEffect(() => {
    if (resource) void openDocument()
  }, [resource?.id, openDocument]) // eslint-disable-line react-hooks/exhaustive-deps

  const submitKey = async (event: React.FormEvent) => {
    event.preventDefault()
    if (!resourceId || !keyValue.trim()) return

    try {
      await redeemKey({ resourceId, key: keyValue.trim() }).unwrap()
      setKeyValue("")
      await openDocument()
    } catch (error) {
      const message = (error as { data?: { message?: string } })?.data?.message
      setKeyError(message || "That access key was not accepted.")
    }
  }

  if (isLoading) {
    return (
      <section className="min-h-full bg-white">
        <DocumentSkeleton />
      </section>
    )
  }

  if (isError || !catalogue) {
    return <section className="p-8 text-center text-neutral-500">This course could not be loaded.</section>
  }

  if (!resource) {
    return (
      <section className="p-8 text-center">
        <p className="mb-4 text-neutral-500">That document is not part of this course.</p>
        <Link to={backTo} className="font-semibold text-[#14392f] underline">
          Back to course
        </Link>
      </section>
    )
  }

  const header = (
    <header className="flex items-center gap-4 border-b border-border bg-white px-5 py-3">
      <Link to={backTo} className="inline-flex items-center gap-2 text-[14px] font-semibold text-[#14392f]">
        <ArrowLeft className="h-4 w-4" />
        Back to course
      </Link>
      <h1 className="truncate font-serif text-[18px] font-semibold text-[#14392f]">{resource.title}</h1>
    </header>
  )

  /* ---------- Blocked states ---------- */
  if (blocker && blocker !== "license_required" && blocker !== "license_expired") {
    return (
      <section className="flex min-h-full flex-col bg-[#f4f6f7]">
        {header}
        <div className="flex flex-1 items-center justify-center px-6 py-16">
          <div className="max-w-md text-center">
            <ShieldX className="mx-auto mb-4 h-10 w-10 text-[#c62828]" />
            <h2 className="mb-2 text-xl font-semibold text-[#14392f]">
              {blocker === "license_revoked" ? "Access withdrawn" : "You cannot open this document"}
            </h2>
            <p className="text-neutral-500">
              {blocker === "license_revoked"
                ? "Your access to this document has been withdrawn. Contact the course administrator if you think this is a mistake."
                : "This document is part of a course you do not have access to."}
            </p>
          </div>
        </div>
      </section>
    )
  }

  if (blocker === "license_required" || blocker === "license_expired") {
    return (
      <section className="flex min-h-full flex-col bg-[#f4f6f7]">
        {header}
        <div className="flex flex-1 items-center justify-center px-6 py-16">
          <form onSubmit={submitKey} className="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm">
            <div className="mb-5 text-center">
              {blocker === "license_expired" ? (
                <Lock className="mx-auto mb-3 h-9 w-9 text-[#ddb737]" />
              ) : (
                <KeyRound className="mx-auto mb-3 h-9 w-9 text-[#14392f]" />
              )}
              <h2 className="mb-1 text-xl font-semibold text-[#14392f]">
                {blocker === "license_expired" ? "Your access has expired" : "Enter your access key"}
              </h2>
              <p className="text-[14px] text-neutral-500">
                {blocker === "license_expired"
                  ? "Enter a current access key to open this document again."
                  : `${resource.title} is protected. Enter the access key you were given.`}
              </p>
            </div>

            <label htmlFor="access-key" className="mb-1.5 block text-[13px] font-semibold text-[#14392f]">
              Access key
            </label>
            <input
              id="access-key"
              value={keyValue}
              onChange={(event) => setKeyValue(event.target.value)}
              autoComplete="off"
              spellCheck={false}
              placeholder="e.g. RCA-TOOLKIT-2026"
              className={`mb-2 w-full rounded-lg border px-3 py-2.5 font-mono text-[14px] outline-none focus:ring-2 focus:ring-[#14392f]/30 ${
                keyError ? "border-[#c62828]" : "border-border"
              }`}
            />
            {keyError && <p className="mb-3 text-[13px] text-[#c62828]">{keyError}</p>}

            <button
              type="submit"
              disabled={redeeming || !keyValue.trim()}
              className="mt-2 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-[#14392f] font-semibold text-white disabled:opacity-60"
            >
              {redeeming && <Loader2 className="h-4 w-4 animate-spin" />}
              Unlock document
            </button>
          </form>
        </div>
      </section>
    )
  }

  /* ---------- Document ---------- */
  return (
    <section className="flex min-h-full flex-col bg-[#f4f6f7]">
      {header}
      {ticketUrl ? (
        <HtmlResourceViewer src={ticketUrl} title={resource.title} offset={114} />
      ) : (
        <div className="flex flex-1 items-center justify-center py-16 text-neutral-500">
          {minting ? (
            <span className="inline-flex items-center gap-2">
              <Loader2 className="h-4 w-4 animate-spin" /> Opening document…
            </span>
          ) : (
            <DocumentSkeleton />
          )}
        </div>
      )}
    </section>
  )
}
