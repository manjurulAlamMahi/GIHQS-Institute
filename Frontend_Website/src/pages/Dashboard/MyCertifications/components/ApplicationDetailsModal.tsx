import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { useGetCertificationApplicationDetailsQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"
import { Badge } from "@/components/ui/badge"
import { Link } from "react-router"
import { ROUTES } from "@/routes/routes.constants"

interface ApplicationDetailsModalProps {
  applicationId: string | number | null
  isOpen: boolean
  onClose: () => void
}

export function ApplicationDetailsModal({
  applicationId,
  isOpen,
  onClose,
}: ApplicationDetailsModalProps) {
  const { data, isLoading } = useGetCertificationApplicationDetailsQuery(
    applicationId || "",
    { skip: !applicationId || !isOpen }
  )

  const application = data?.data?.application

  return (
    <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
      <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="text-xl font-serif text-[#14392f]">
            Application Details
          </DialogTitle>
        </DialogHeader>

        {isLoading ? (
          <div className="space-y-4 py-4">
            <Skeleton className="h-4 w-3/4" />
            <Skeleton className="h-4 w-1/2" />
            <Skeleton className="h-32 w-full" />
            <Skeleton className="h-4 w-5/6" />
          </div>
        ) : !application ? (
          <div className="py-8 text-center text-neutral-500">
            Failed to load application details.
          </div>
        ) : (
          <div className="space-y-6 py-4">
            <div className="grid grid-cols-2 gap-4 rounded-lg bg-neutral-50 p-4 border border-border">
              <div>
                <p className="text-xs text-neutral-500">Reference Number</p>
                <p className="font-semibold text-neutral-900">{application.reference_number}</p>
              </div>
              <div>
                <p className="text-xs text-neutral-500">Status</p>
                <Badge
                  variant={
                    application.status === "accepted" ? "default" :
                    application.status === "pending" ? "secondary" :
                    "destructive"
                  }
                  className="mt-1 capitalize"
                >
                  {application.status}
                </Badge>
              </div>
              <div className="col-span-2">
                <p className="text-xs text-neutral-500">Certification</p>
                <p className="font-medium text-neutral-900">{application.certification_title}</p>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
              <div>
                <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2 mb-3">Applicant Info</h4>
                <div className="space-y-2 text-sm">
                  <p><span className="text-neutral-500">Name:</span> {application.applicant_name}</p>
                  <p><span className="text-neutral-500">Email:</span> {application.email}</p>
                  <p><span className="text-neutral-500">Phone:</span> {application.phone}</p>
                  <p><span className="text-neutral-500">Location:</span> {application.city}, {application.country}</p>
                </div>
              </div>

              <div>
                <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2 mb-3">Professional Info</h4>
                <div className="space-y-2 text-sm">
                  <p><span className="text-neutral-500">Organization:</span> {application.organization}</p>
                  <p><span className="text-neutral-500">Job Title:</span> {application.current_job_title}</p>
                  <p><span className="text-neutral-500">Role:</span> {application.professional_role}</p>
                  <p><span className="text-neutral-500">Experience:</span> {application.years_of_experience} years</p>
                  <p><span className="text-neutral-500">Primary Area:</span> {application.primary_area_of_experience}</p>
                </div>
              </div>
            </div>

            <div className="space-y-4">
              <h4 className="text-sm font-semibold text-[#14392f] border-b pb-2">Documents & Links</h4>
              <div className="flex flex-wrap gap-4">
                {application.resume_cv && (
                  <a
                    href={application.resume_cv}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center gap-2 text-sm text-[#14392f] font-medium hover:underline bg-neutral-100 px-3 py-1.5 rounded-md"
                  >
                    View Resume/CV
                  </a>
                )}
                <Link
                  to={`${ROUTES.DASHBOARD_PROFESSIONAL_DEVELOPMENT}?catalogue_id=${application.catalogue_id}`}
                  className="inline-flex items-center gap-2 text-sm text-[#14392f] font-medium hover:underline bg-neutral-100 px-3 py-1.5 rounded-md"
                >
                  View Certification
                </Link>
                {application.linkedin_profile && (
                  <a
                    href={application.linkedin_profile}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center gap-2 text-sm text-[#14392f] font-medium hover:underline bg-neutral-100 px-3 py-1.5 rounded-md"
                  >
                    LinkedIn Profile
                  </a>
                )}
              </div>
            </div>

            {application.admin_notes && (
              <div className="rounded-lg bg-blue-50 border border-blue-100 p-4">
                <h4 className="text-sm font-semibold text-blue-900 mb-2">Admin Notes</h4>
                <p className="text-sm text-blue-800 whitespace-pre-wrap">{application.admin_notes}</p>
              </div>
            )}
            
            <div className="text-xs text-neutral-400 text-right pt-4 border-t">
              Submitted: {application.submission_date}
            </div>
          </div>
        )}
      </DialogContent>
    </Dialog>
  )
}
