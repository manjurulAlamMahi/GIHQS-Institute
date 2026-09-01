import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { useGetAdvisoryRequestDetailsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"

interface AdvisoryRequestDetailsModalProps {
  requestId: string | number | null
  isOpen: boolean
  onClose: () => void
}

export function AdvisoryRequestDetailsModal({
  requestId,
  isOpen,
  onClose,
}: AdvisoryRequestDetailsModalProps) {
  const { data, isLoading } = useGetAdvisoryRequestDetailsQuery(requestId!, {
    skip: !requestId,
  })

  const request = data?.data?.advisory_request

  return (
    <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
      <DialogContent className="max-w-2xl bg-white p-0 overflow-hidden border-none shadow-xl gap-0 max-h-[90vh] flex flex-col">
        <DialogHeader className="px-6 py-4 border-b bg-[#f8faf9]">
          <DialogTitle className="text-xl font-bold text-[#14392f] font-['Outfit']">
            Advisory Request Details
          </DialogTitle>
        </DialogHeader>

        <div className="p-6 overflow-y-auto">
          {isLoading ? (
            <div className="space-y-6">
              <Skeleton className="h-24 w-full rounded-xl" />
              <div className="grid md:grid-cols-2 gap-8">
                <div className="space-y-4">
                  <Skeleton className="h-6 w-1/3" />
                  <Skeleton className="h-4 w-3/4" />
                  <Skeleton className="h-4 w-2/3" />
                </div>
                <div className="space-y-4">
                  <Skeleton className="h-6 w-1/3" />
                  <Skeleton className="h-4 w-3/4" />
                  <Skeleton className="h-4 w-2/3" />
                </div>
              </div>
            </div>
          ) : request ? (
            <div className="space-y-8">
              {/* Header Card */}
              <div className="bg-[#f8faf9] rounded-xl p-5 border border-neutral-100 flex flex-wrap gap-6 items-center justify-between">
                <div className="space-y-1">
                  <p className="text-xs font-medium text-neutral-500 uppercase tracking-wider">Reference Number</p>
                  <p className="text-sm font-bold text-[#14392f]">{request.reference_number}</p>
                </div>
                <div className="space-y-1">
                  <p className="text-xs font-medium text-neutral-500 uppercase tracking-wider">Status</p>
                  <Badge
                    variant={
                      request.status === "completed" ? "default" :
                      request.status === "pending" ? "secondary" :
                      "outline"
                    }
                    className="capitalize px-3"
                  >
                    {request.status}
                  </Badge>
                </div>
              </div>

              <div className="grid md:grid-cols-2 gap-8">
                {/* Contact Info */}
                <div className="space-y-4">
                  <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2">Contact Info</h4>
                  <ul className="space-y-3 text-[15px]">
                    <li className="grid grid-cols-[90px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Name:</span>
                      <span className="text-neutral-800 font-medium">{request.full_name}</span>
                    </li>
                    <li className="grid grid-cols-[90px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Email:</span>
                      <span className="text-neutral-800 break-all">{request.work_email}</span>
                    </li>
                    <li className="grid grid-cols-[90px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Phone:</span>
                      <span className="text-neutral-800">{request.phone_number}</span>
                    </li>
                    <li className="grid grid-cols-[90px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Country:</span>
                      <span className="text-neutral-800">{request.country}</span>
                    </li>
                  </ul>
                </div>

                {/* Organization & Service */}
                <div className="space-y-4">
                  <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2">Organization & Service</h4>
                  <ul className="space-y-3 text-[15px]">
                    <li className="grid grid-cols-[100px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Organization:</span>
                      <span className="text-neutral-800 font-medium">{request.organization_name}</span>
                    </li>
                    <li className="grid grid-cols-[100px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Type:</span>
                      <span className="text-neutral-800">{request.organization_type}</span>
                    </li>
                    <li className="grid grid-cols-[100px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Service:</span>
                      <span className="text-neutral-800">{request.service_of_interest}</span>
                    </li>
                    <li className="grid grid-cols-[100px_1fr] gap-2">
                      <span className="text-neutral-500 font-medium">Timeline:</span>
                      <span className="text-neutral-800">{request.desired_timeline}</span>
                    </li>
                  </ul>
                </div>
              </div>

              {/* Needs Description */}
              {request.description_of_needs && (
                <div className="space-y-3">
                  <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2">Description of Needs</h4>
                  <div className="bg-neutral-50 p-4 rounded-lg text-[15px] text-neutral-700 leading-relaxed border border-neutral-100 whitespace-pre-wrap">
                    {request.description_of_needs}
                  </div>
                </div>
              )}

              {/* Admin Notes */}
              {request.admin_notes && (
                <div className="space-y-3">
                  <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2">Admin Notes</h4>
                  <div className="bg-amber-50 p-4 rounded-lg text-[15px] text-amber-900 leading-relaxed border border-amber-100">
                    {request.admin_notes}
                  </div>
                </div>
              )}

            </div>
          ) : (
            <div className="py-8 text-center text-neutral-500">
              Failed to load details.
            </div>
          )}
        </div>
        
        {/* Footer */}
        {request && (
          <div className="px-6 py-4 bg-neutral-50 border-t flex justify-end">
            <p className="text-[13px] text-neutral-400 font-medium">
              Submitted: {request.submission_date}
            </p>
          </div>
        )}
      </DialogContent>
    </Dialog>
  )
}
