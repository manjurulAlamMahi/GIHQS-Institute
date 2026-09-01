import { useGetCeTrackingsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"

export function ContinuationCycles() {
  const { data, isLoading } = useGetCeTrackingsQuery()
  const trackings = data?.data?.trackings || []
  return (
    <section className="rounded-[12px] bg-white p-6 shadow-sm">
      <h2 className="text-[22px] font-semibold text-[#111827]">
        Continuation Required Cycles
      </h2>

      <div className="mt-6 space-y-6">
        {isLoading ? (
          Array.from({ length: 2 }).map((_, i) => (
            <article key={i} className="rounded-[12px] border border-border bg-white p-7">
              <div className="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div className="min-w-0 flex-1 space-y-4">
                  <Skeleton className="h-6 w-3/4 rounded bg-gray-200" />
                  <div className="mt-7 grid gap-5 md:grid-cols-4">
                    {Array.from({ length: 4 }).map((_, j) => (
                      <div key={j} className="space-y-2">
                        <Skeleton className="h-3 w-16 bg-gray-200" />
                        <Skeleton className="h-4 w-24 bg-gray-200" />
                      </div>
                    ))}
                  </div>
                  <Skeleton className="h-10 w-32 rounded-full bg-gray-200" />
                </div>
                <Skeleton className="h-11 min-w-[125px] rounded-full bg-gray-200" />
              </div>
            </article>
          ))
        ) : trackings.length === 0 ? (
          <div className="rounded-[12px] border border-border bg-gray-50 py-12 text-center">
            <p className="text-[15px] text-[#667085]">No active certification cycles found.</p>
          </div>
        ) : (
          trackings.map((cycle) => (
            <article
              key={cycle.catalogue_id}
              className="rounded-[12px] border border-border bg-white p-7"
            >
              <div className="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div className="min-w-0 flex-1">
                  <h3 className="text-[15px] font-semibold text-[#111827]">
                    {cycle.certification_title}
                  </h3>

                  <div className="mt-7 grid gap-5 text-[13px] md:grid-cols-4">
                    <div>
                      <p className="text-[#667085]">{cycle.renewal_date}</p>
                      <p className="mt-1 font-medium text-[#344054]">Renewal Certification</p>
                    </div>
                    <div>
                      <p className="text-[#667085]">{cycle.expiration_date}</p>
                      <p className="mt-1 font-medium text-[#344054]">Expiration</p>
                    </div>
                    <div>
                      <p className="text-[#667085]">{cycle.ce_window}</p>
                      <p className="mt-1 font-medium text-[#344054]">CE Window</p>
                    </div>
                    <div>
                      <p className="text-[#667085]">{cycle.submission_due}</p>
                      <p className="mt-1 font-medium text-[#344054]">Submission Due</p>
                    </div>
                  </div>

                  <p className="mt-5 text-[13px] text-[#667085]">
                    Additional domains and/or activity types may be required for domain distribution rules
                  </p>

                  <button
                    type="button"
                    onClick={() => {
                      const target = document.getElementById("add-ce-activity")
                      const scrollContainer = document.querySelector<HTMLElement>("[data-dashboard-scroll]")
                      if (target && scrollContainer) {
                        scrollContainer.scrollTo({ top: target.offsetTop - 24, behavior: "smooth" })
                      }
                    }}
                    className="mt-4 inline-flex h-10 items-center rounded-full bg-[#14392f] px-12 text-[15px] font-medium text-white hover:bg-[#0f2f26]"
                  >
                    Manage
                  </button>
                </div>

                <div className="inline-flex h-11 min-w-[125px] items-center justify-center rounded-full bg-[#e8eeee] px-8 text-[18px] font-medium text-[#14392f]">
                  {cycle.completed_credits} / {cycle.required_credits}
                </div>
              </div>
            </article>
          ))
        )}
      </div>
    </section>
  )
}
