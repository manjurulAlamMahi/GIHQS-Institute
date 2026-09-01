import { Link } from "react-router"

import { ROUTES } from "@/routes/routes.constants"

import { useGetDashboardOverviewQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"

export function DashboardAccreditationCard() {
  const { data, isLoading } = useGetDashboardOverviewQuery()
  const accreditation = data?.data?.accreditation

  if (isLoading) {
    return (
      <section className="rounded-[10px] border border-border bg-white p-7 shadow-sm">
        <Skeleton className="h-6 w-1/3 mb-6" />
        <Skeleton className="h-24 w-full" />
      </section>
    )
  }

  return (
    <section className="rounded-[10px] border border-border bg-white p-7 shadow-sm flex flex-col h-full">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h2 className="text-[16px] font-medium leading-none text-[#14392f]">
            Accreditation
          </h2>
          <p className="mt-3 text-[14px] text-muted-foreground">
            Track accreditation applications, reviewer feedback, and decisions.
          </p>
        </div>
        <Link
          to={ROUTES.DASHBOARD_ACCREDITATION}
          className="text-[14px] font-medium text-[#14392f] hover:underline"
        >
          View All
        </Link>
      </div>

      <div className="mt-7 flex-1 space-y-4">
        {!accreditation ? (
          <div className="rounded-[10px] border border-border bg-neutral-50 p-6 text-center text-sm text-neutral-500">
            No accreditations found.
          </div>
        ) : (
          <div className="flex min-h-[73px] items-center gap-5 rounded-[10px] border border-border bg-[#fbfcfc] px-6 py-4">
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-3">
                <h3 className="text-[16px] font-medium text-[#111827] truncate">
                  {accreditation.program_name}
                </h3>
                <Badge
                  variant={
                    accreditation.status.toLowerCase() === "approved" ? "default" :
                    accreditation.status.toLowerCase() === "pending" ? "secondary" :
                    "outline"
                  }
                  className="capitalize px-2 py-0 text-[12px]"
                >
                  {accreditation.status}
                </Badge>
              </div>
              <div className="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-[14px] text-muted-foreground">
                <span>Ref: {accreditation.reference_number}</span>
                <span>Submitted: {accreditation.submission_date}</span>
              </div>
              {accreditation.admin_notes && (
                <p className="mt-2 text-[14px] text-amber-600 bg-amber-50 p-2 rounded truncate border border-amber-100">
                  {accreditation.admin_notes}
                </p>
              )}
            </div>
          </div>
        )}
      </div>
    </section>
  )
}
