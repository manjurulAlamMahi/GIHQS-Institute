import { useGetCeActivitiesQuery } from "@/features/profile/api/profileApi"
import { Skeleton } from "@/components/ui/skeleton"

export function CeActivityHistory() {
  const { data, isLoading } = useGetCeActivitiesQuery()
  const activities = data?.data?.activities || []
  return (
    <section className="rounded-[12px] bg-white p-6 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#111827]">CE Activity History</h2>
      <p className="mt-1 text-[15px] text-[#667085]">
        Compact table with certification and domain included. Show activity history across all certifications or filter later in Laravel.
      </p>

      <div className="mt-7 overflow-x-auto">
        <table className="w-full min-w-[880px] border-collapse text-left">
          <thead>
            <tr className="border-b border-[#d0d5dd] text-[14px] uppercase text-[#667085]">
              <th className="py-3 font-medium">Activity</th>
              <th className="py-3 font-medium">Certification</th>
              <th className="py-3 font-medium">Domain</th>
              <th className="py-3 font-medium">Type</th>
              <th className="py-3 font-medium">Date</th>
              <th className="py-3 font-medium">Status</th>
              <th className="py-3 text-right font-medium">Evidence</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              Array.from({ length: 4 }).map((_, rowIndex) => (
                <tr key={rowIndex} className="border-b border-[#f2f4f7]">
                  {Array.from({ length: 7 }).map((__, cellIndex) => (
                    <td key={cellIndex} className="py-4">
                      <Skeleton className={`h-5 ${cellIndex === 0 ? "w-36" : cellIndex === 6 ? "ml-auto w-20" : "w-24"}`} />
                    </td>
                  ))}
                </tr>
              ))
            ) : activities.length === 0 ? (
              <tr>
                <td colSpan={7} className="py-8 text-center text-[15px] text-[#667085]">
                  No CE activities found.
                </td>
              </tr>
            ) : (
              activities.map((row) => (
                <tr key={row.id} className="text-[15px] text-[#667085]">
                  <td className="max-w-[140px] py-4">{row.activity_title}</td>
                  <td className="py-4">{row.certification_short}</td>
                  <td className="max-w-[130px] py-4">{row.domain}</td>
                  <td className="py-4">{row.activity_type}</td>
                  <td className="py-4">{row.completion_date}</td>
                  <td className="py-4">
                    <span className={`inline-flex h-6 items-center rounded-full px-3 text-[14px] font-medium capitalize ${
                      row.status === 'approved' 
                        ? 'bg-[#e2f0e9] text-[#14392f]' 
                        : 'bg-[#ffe9e0] text-[#d45122]'
                    }`}>
                      {row.status}
                    </span>
                  </td>
                  <td className="py-4 text-right">
                    {row.evidence_file ? (
                      <a href={row.evidence_file} target="_blank" rel="noreferrer" className="text-[15px] font-medium text-blue-600 hover:underline">
                        View File
                      </a>
                    ) : (
                      <span className="text-[#98a2b3]">-</span>
                    )}
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </section>
  )
}
