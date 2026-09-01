import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
import { Skeleton } from "@/components/ui/skeleton"
import {
  useGetAccreditationApplicationDetailsQuery,
  useGetAccreditationApplicationsQuery,
} from "@/features/accreditation/api/accreditationApi"
import type { ApplicationData } from "@/types/accreditation.types"
import { useState } from "react"

export function AccreditationTable() {
  const { data: response, isLoading } = useGetAccreditationApplicationsQuery()
  const [selectedApp, setSelectedApp] = useState<ApplicationData | null>(null)

  const { data: detailsResponse, isFetching: isFetchingDetails } =
    useGetAccreditationApplicationDetailsQuery(selectedApp?.id as number, {
      skip: !selectedApp,
    })

  const applications = response?.data?.applications || []
  const applicationDetails = detailsResponse?.data?.application

  const getStatusColor = (status: string) => {
    const s = status.toLowerCase()
    if (s === "accepted" || s === "approved") {
      return "bg-[#ccfbf1] text-[#14b8a6]"
    } else if (s === "rejected" || s === "canceled" || s === "cancelled") {
      return "bg-[#fee2e2] text-[#f87171]"
    }
    return "bg-gray-100 text-gray-700"
  }

  return (
    <>
      <div className="overflow-hidden rounded-[10px] bg-white shadow-sm">
        <div className="overflow-x-auto">
          <table className="w-full text-left text-[15px]">
            <thead>
              <tr className="border-b border-[#f3f4f6]">
                <th className="px-6 py-4 text-[12px] font-medium tracking-wider text-[#8b929f] uppercase">
                  Accreditation ID
                </th>
                <th className="px-6 py-4 text-[12px] font-medium tracking-wider text-[#8b929f] uppercase">
                  Program Name
                </th>
                <th className="px-6 py-4 text-[12px] font-medium tracking-wider text-[#8b929f] uppercase">
                  Submission Date
                </th>
                <th className="px-6 py-4 text-[12px] font-medium tracking-wider text-[#8b929f] uppercase">
                  Status
                </th>
                <th className="px-6 py-4 text-[12px] font-medium tracking-wider text-[#8b929f] uppercase">
                  Action
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#f3f4f6]">
              {isLoading ? (
                Array.from({ length: 4 }).map((_, index) => (
                  <tr key={index}>
                    {Array.from({ length: 5 }).map((__, cellIndex) => (
                      <td key={cellIndex} className="px-6 py-5">
                        <Skeleton
                          className={`h-5 ${cellIndex === 1 ? "w-48" : "w-28"}`}
                        />
                      </td>
                    ))}
                  </tr>
                ))
              ) : applications.length === 0 ? (
                <tr>
                  <td
                    colSpan={5}
                    className="py-8 text-center text-sm text-gray-500"
                  >
                    No applications found.
                  </td>
                </tr>
              ) : (
                applications.map((row) => (
                  <tr key={row.id} className="group hover:bg-[#f9fafb]">
                    <td className="px-6 py-4 font-medium whitespace-nowrap text-[#4b5563]">
                      {row.reference_number}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-[#4b5563]">
                      {row.program_name}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-[#4b5563]">
                      {row.submission_date}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span
                        className={`inline-flex items-center rounded-md px-2.5 py-1 text-[13px] font-medium capitalize ${getStatusColor(
                          row.status
                        )}`}
                      >
                        {row.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <button
                        className="text-[15px] font-bold text-[#1f2937] hover:text-black"
                        onClick={() => setSelectedApp(row)}
                      >
                        Details
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      <Dialog
        open={!!selectedApp}
        onOpenChange={(open) => !open && setSelectedApp(null)}
      >
        <DialogContent className="border-none p-6 sm:max-w-100">
          <DialogHeader className="mb-4">
            <DialogTitle className="text-2xl font-bold tracking-tight text-[#111827]">
              {selectedApp?.reference_number}
            </DialogTitle>
          </DialogHeader>
          <div className="rounded-xl bg-[#f4f6f7] p-5 text-sm leading-relaxed text-[#4b5563]">
            <span className="text-base font-semibold text-[#111827]">
              Admin Notes:{" "}
            </span>
            {isFetchingDetails ? (
              <span className="text-gray-500">Loading details...</span>
            ) : (
              applicationDetails?.admin_notes ||
              selectedApp?.admin_notes ||
              "No admin notes available."
            )}
          </div>
        </DialogContent>
      </Dialog>
    </>
  )
}
