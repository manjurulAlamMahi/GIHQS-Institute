import { Link } from "react-router"
import { useState } from "react"
import { ROUTES } from "@/routes/routes.constants"
import { useGetDashboardOverviewQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"
import { ApplicationDetailsModal } from "@/pages/Dashboard/MyCertifications/components/ApplicationDetailsModal"

export function DashboardActivityCards() {
  const { data, isLoading } = useGetDashboardOverviewQuery()
  const certification = data?.data?.certification
  const course = data?.data?.course

  const [selectedApplicationId, setSelectedApplicationId] = useState<string | number | null>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleViewDetails = (id: number) => {
    setSelectedApplicationId(id)
    setIsModalOpen(true)
  }

  const closeModal = () => {
    setIsModalOpen(false)
    setTimeout(() => setSelectedApplicationId(null), 300)
  }

  if (isLoading) {
    return (
      <div className="grid gap-7 xl:grid-cols-2">
        <div className="rounded-[10px] border border-border bg-white p-7 shadow-sm space-y-4">
          <Skeleton className="h-6 w-1/3" />
          <Skeleton className="h-32 w-full" />
        </div>
        <div className="rounded-[10px] border border-border bg-white p-7 shadow-sm space-y-4">
          <Skeleton className="h-6 w-1/3" />
          <Skeleton className="h-32 w-full" />
        </div>
      </div>
    )
  }

  return (
    <div className="grid gap-7 xl:grid-cols-2">
      <section className="rounded-[10px] border border-border bg-white p-7 shadow-sm flex flex-col">
        <div className="flex items-start justify-between gap-4">
          <div>
            <h2 className="text-[16px] font-medium leading-none text-[#14392f]">
              My Certifications
            </h2>
            <p className="mt-3 text-[14px] text-muted-foreground">
              Your certification applications and next actions.
            </p>
          </div>
          <Link to={ROUTES.DASHBOARD_CERTIFICATIONS} className="text-[14px] font-medium text-[#14392f] hover:underline">
            View All
          </Link>
        </div>

        <div className="mt-9 flex-1 space-y-4">
          {!certification ? (
            <div className="rounded-[10px] border border-border bg-neutral-50 p-6 text-center text-sm text-neutral-500">
              No certifications found.
            </div>
          ) : (
            <div className="rounded-[10px] border border-border bg-white p-6 transition-colors hover:bg-neutral-50">
              <Badge
                variant={
                  certification.status === "APPROVED" ? "default" :
                  certification.status === "PENDING" ? "secondary" :
                  "outline"
                }
                className="mb-3 uppercase"
              >
                {certification.status}
              </Badge>
              <h3 className="text-[17px] font-medium text-[#111827]">
                {certification.title}
              </h3>
              <div className="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-[14px] text-muted-foreground">
                <span>Applied: {certification.applied_date}</span>
                <span>Ref: {certification.reference_number}</span>
              </div>
              <button 
                onClick={() => handleViewDetails(certification.id)}
                className="mt-5 h-8 rounded-full bg-[#14392f] px-5 text-[15px] font-medium text-white hover:bg-[#0f2f26]"
              >
                {certification.action_text || "View Details"}
              </button>
            </div>
          )}
        </div>
      </section>

      <section className="rounded-[10px] border border-border bg-white p-7 shadow-sm flex flex-col">
        <div className="flex items-start justify-between gap-4">
          <div>
            <h2 className="text-[16px] font-medium leading-none text-[#14392f]">
              My Courses
            </h2>
            <p className="mt-3 text-[14px] text-muted-foreground">
              Your learning progress and course activity.
            </p>
          </div>
          <Link to={ROUTES.DASHBOARD_COURSES} className="text-[14px] font-medium text-[#14392f] hover:underline">
            View All
          </Link>
        </div>

        <div className="mt-9 flex-1 space-y-4">
          {!course ? (
            <div className="rounded-[10px] border border-border bg-neutral-50 p-6 text-center text-sm text-neutral-500">
              No active courses found.
            </div>
          ) : (
            <div className="rounded-[10px] border border-border bg-white p-5 transition-colors hover:bg-neutral-50">
              <div className="flex items-center justify-between gap-4">
                <div className="flex-1 min-w-0">
                  <h3 className="text-[17px] font-medium text-[#111827] truncate">{course.title}</h3>
                  <p className="mt-1 text-[14px] text-muted-foreground">
                    {course.status_label}
                  </p>
                  <div className="mt-3 h-1 w-full max-w-[132px] rounded-full bg-[#eef0f2] overflow-hidden">
                    <div 
                      className="h-full bg-[#14392f]" 
                      style={{ width: `${course.progress_percentage}%` }}
                    />
                  </div>
                </div>
                <Link 
                  to={ROUTES.DASHBOARD_COURSE_DETAIL.replace(':id', course.id.toString())}
                  className="flex shrink-0 h-8 items-center justify-center rounded-full bg-[#14392f] px-6 text-[15px] font-medium text-white hover:bg-[#0f2f26]"
                >
                  {course.action_text || "Open"}
                </Link>
              </div>
            </div>
          )}
        </div>
      </section>

      <ApplicationDetailsModal
        applicationId={selectedApplicationId}
        isOpen={isModalOpen}
        onClose={closeModal}
      />
    </div>
  )
}
