import { Award } from "lucide-react"
import { useGetCeTrackingsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Link } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

type Props = { search: string; status: string }

export function CertificationList({ search, status }: Props) {
  const { data, isLoading } = useGetCeTrackingsQuery()
  const trackings = data?.data?.trackings || []

  if (isLoading) {
    return (
      <div className="space-y-4">
        {[1, 2].map((i) => (
          <div key={i} className="h-24 w-full rounded-lg bg-white border border-border p-5">
            <Skeleton className="h-full w-full" />
          </div>
        ))}
      </div>
    )
  }

  if (trackings.length === 0) {
    return (
      <div className="py-12 text-center text-neutral-500 bg-white rounded-lg border border-border">
        You don't have any active certifications yet.
      </div>
    )
  }

  const normalizedSearch = search.trim().toLowerCase()
  const filteredTrackings = trackings
    .filter((tracking) => {
      const matchesSearch = tracking.certification_title.toLowerCase().includes(normalizedSearch)
      const isRenewalDue = new Date(tracking.renewal_date).getTime() <= Date.now()
      return matchesSearch && (status === "all" || status === "active" || (status === "renewal-due" && isRenewalDue))
    })

  if (filteredTrackings.length === 0) {
    return <div className="py-12 text-center text-neutral-500">No certifications match your filters.</div>
  }

  return (
    <div className="space-y-4">
      {filteredTrackings.map((tracking) => {
        const progress = tracking.required_credits > 0 
          ? Math.round((tracking.completed_credits / tracking.required_credits) * 100) 
          : 0;
          
        return (
          <article
            key={tracking.catalogue_id}
            className="flex flex-col gap-5 rounded-[10px] border border-border bg-white px-5 py-5 md:flex-row md:items-center md:justify-between transition-shadow hover:shadow-sm"
          >
            <div className="flex min-w-0 gap-5">
              <div className="flex size-14 shrink-0 items-center justify-center rounded-[10px] bg-[#fbf4dd] text-[#14392f]">
                <Award className="size-6 stroke-[1.8]" aria-hidden="true" />
              </div>

              <div className="min-w-0 flex-1">
                <div className="flex flex-wrap items-center gap-3">
                  <h3 className="text-[16px] font-semibold text-[#14392f]">
                    {tracking.certification_title}
                  </h3>
                  <span
                    className={`inline-flex h-5 items-center rounded-full px-3 text-[15px] font-medium bg-[#eef3d8] text-[#6f7d2a]`}
                  >
                    Active
                  </span>
                </div>

                <div className="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[15px] text-[#4b5563]">
                  <span>Expires: {tracking.expiration_date}</span>
                  <span>Renewal Date: {tracking.renewal_date}</span>
                  <span>CE Window: {tracking.ce_window}</span>
                  <span>CE: {tracking.completed_credits} / {tracking.required_credits}</span>
                </div>

                <div className="mt-4 flex max-w-90 items-center gap-3">
                  <div className="h-1.5 flex-1 rounded-full bg-[#ececef]">
                    <div
                      className="h-full rounded-full bg-[#05051f]"
                      style={{ width: `${progress > 100 ? 100 : progress}%` }}
                    />
                  </div>
                  <span className="text-[15px] text-[#4b5563]">
                    {progress}%
                  </span>
                </div>
              </div>
            </div>

            <div className="flex shrink-0 items-center gap-3 self-start md:self-center">
              <Link 
                to={ROUTES.DASHBOARD_CE_TRACKER}
                className="inline-flex h-9 items-center justify-center rounded-[8px] border border-border bg-white px-4 text-[16px] font-medium text-[#111827] hover:bg-neutral-50 transition-colors"
              >
                Track CE
              </Link>
            </div>
          </article>
        )
      })}
    </div>
  )
}
