import { useState } from "react"
import { useGetCertificationApplicationsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"
import { FileText, Eye } from "lucide-react"
import { ApplicationDetailsModal } from "./ApplicationDetailsModal"

export function ApplicationList() {
  const { data, isLoading } = useGetCertificationApplicationsQuery()
  const applications = data?.data?.applications || []

  const [selectedApplicationId, setSelectedApplicationId] = useState<string | number | null>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleViewDetails = (id: number) => {
    setSelectedApplicationId(id)
    setIsModalOpen(true)
  }

  const closeModal = () => {
    setIsModalOpen(false)
    setTimeout(() => setSelectedApplicationId(null), 300) // Clear after animation
  }

  if (isLoading) {
    return (
      <div className="space-y-4">
        {[1, 2, 3].map((i) => (
          <div key={i} className="h-24 w-full rounded-lg bg-white border border-border p-5">
            <Skeleton className="h-full w-full" />
          </div>
        ))}
      </div>
    )
  }

  if (applications.length === 0) {
    return (
      <div className="py-12 text-center text-neutral-500 bg-white rounded-lg border border-border">
        You haven't submitted any certification applications yet.
      </div>
    )
  }

  return (
    <>
      <div className="space-y-4">
        {applications.map((app) => (
          <article
            key={app.id}
            className="flex flex-col gap-4 rounded-[10px] border border-border bg-white px-5 py-5 md:flex-row md:items-center md:justify-between transition-shadow hover:shadow-sm"
          >
            <div className="flex min-w-0 gap-4 md:gap-5 items-start md:items-center">
              <div className="hidden md:flex size-12 shrink-0 items-center justify-center rounded-[10px] bg-[#f8faf9] text-[#14392f] border border-neutral-100">
                <FileText className="size-5" />
              </div>

              <div className="min-w-0 flex-1">
                <div className="flex items-center gap-3 mb-1.5">
                  <h3 className="text-[16px] font-semibold text-[#14392f] truncate">
                    {app.certification_title}
                  </h3>
                  <Badge
                    variant={
                      app.status === "accepted" ? "default" :
                      app.status === "pending" ? "secondary" :
                      "destructive"
                    }
                    className="capitalize px-2 py-0 h-5 text-[12px]"
                  >
                    {app.status}
                  </Badge>
                </div>

                <div className="flex flex-wrap gap-x-4 gap-y-1.5 text-[15px] text-[#4b5563]">
                  <span><strong className="font-medium text-neutral-700">Ref:</strong> {app.reference_number}</span>
                  <span><strong className="font-medium text-neutral-700">Name:</strong> {app.applicant_name}</span>
                  <span><strong className="font-medium text-neutral-700">Submitted:</strong> {app.submission_date}</span>
                </div>
              </div>
            </div>

            <button
              onClick={() => handleViewDetails(app.id)}
              className="mt-2 md:mt-0 flex h-9 items-center justify-center gap-2 rounded-lg border border-border bg-white px-4 text-[16px] font-medium text-[#111827] hover:bg-neutral-50 transition-colors shrink-0"
            >
              <Eye className="w-4 h-4 text-neutral-500" />
              View Details
            </button>
          </article>
        ))}
      </div>

      <ApplicationDetailsModal
        applicationId={selectedApplicationId}
        isOpen={isModalOpen}
        onClose={closeModal}
      />
    </>
  )
}
