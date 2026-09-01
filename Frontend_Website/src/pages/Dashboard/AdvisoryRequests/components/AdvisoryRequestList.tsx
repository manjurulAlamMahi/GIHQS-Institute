import { useState } from "react"
import { useGetAdvisoryRequestsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"
import { MessageSquare, Eye } from "lucide-react"
import { AdvisoryRequestDetailsModal } from "./AdvisoryRequestDetailsModal"

export function AdvisoryRequestList() {
  const { data, isLoading } = useGetAdvisoryRequestsQuery()
  const requests = data?.data?.advisory_requests || []

  const [selectedRequestId, setSelectedRequestId] = useState<string | number | null>(null)
  const [isModalOpen, setIsModalOpen] = useState(false)

  const handleViewDetails = (id: number) => {
    setSelectedRequestId(id)
    setIsModalOpen(true)
  }

  const closeModal = () => {
    setIsModalOpen(false)
    setTimeout(() => setSelectedRequestId(null), 300)
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

  if (requests.length === 0) {
    return (
      <div className="py-12 text-center text-neutral-500 bg-white rounded-lg border border-border">
        You haven't submitted any advisory requests yet.
      </div>
    )
  }

  return (
    <>
      <div className="space-y-4">
        {requests.map((request) => (
          <article
            key={request.id}
            className="flex flex-col gap-4 rounded-[10px] border border-border bg-white px-5 py-5 md:flex-row md:items-center md:justify-between transition-shadow hover:shadow-sm"
          >
            <div className="flex min-w-0 gap-4 md:gap-5 items-start md:items-center">
              <div className="hidden md:flex size-12 shrink-0 items-center justify-center rounded-[10px] bg-[#f8faf9] text-[#14392f] border border-neutral-100">
                <MessageSquare className="size-5" />
              </div>

              <div className="min-w-0 flex-1">
                <div className="flex items-center gap-3 mb-1.5">
                  <h3 className="text-[16px] font-semibold text-[#14392f] truncate">
                    {request.service_of_interest}
                  </h3>
                  <Badge
                    variant={
                      request.status === "completed" ? "default" :
                      request.status === "pending" ? "secondary" :
                      "outline"
                    }
                    className="capitalize px-2 py-0 h-5 text-[12px]"
                  >
                    {request.status}
                  </Badge>
                </div>

                <div className="flex flex-wrap gap-x-4 gap-y-1.5 text-[15px] text-[#4b5563]">
                  <span><strong className="font-medium text-neutral-700">Ref:</strong> {request.reference_number}</span>
                  <span><strong className="font-medium text-neutral-700">Organization:</strong> {request.organization_name}</span>
                  <span><strong className="font-medium text-neutral-700">Submitted:</strong> {request.submission_date}</span>
                </div>
              </div>
            </div>

            <button
              onClick={() => handleViewDetails(request.id)}
              className="mt-2 md:mt-0 flex h-9 items-center justify-center gap-2 rounded-lg border border-border bg-white px-4 text-[16px] font-medium text-[#111827] hover:bg-neutral-50 transition-colors shrink-0"
            >
              <Eye className="w-4 h-4 text-neutral-500" />
              View Details
            </button>
          </article>
        ))}
      </div>

      <AdvisoryRequestDetailsModal
        requestId={selectedRequestId}
        isOpen={isModalOpen}
        onClose={closeModal}
      />
    </>
  )
}
