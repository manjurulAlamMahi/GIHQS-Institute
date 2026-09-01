import { useGetDashboardStatsQuery } from "@/features/profile/api/profileApi"

export function DashboardStatsGrid() {
  const { data } = useGetDashboardStatsQuery()
  const statsData = data?.data?.stats

  const stats = [
    {
      title: "Active Courses",
      value: statsData?.active_courses ?? 0,
      description: "Courses currently in progress or awaiting final action.",
      className: "border-[#1f6f63] bg-[#eef6f3]",
    },
    {
      title: "Completed Courses",
      value: statsData?.completed_courses ?? 0,
      description: "Completed learning records available for review.",
      className: "border-[#4978ff] bg-[#eef4ff]",
    },
    {
      title: "Exams Pending",
      value: statsData?.exams_pending ?? 0,
      description: "Courses requiring an exam attempt or scheduling action.",
      className: "border-[#ddb737] bg-[#fbf8ea]",
    },
    {
      title: "CE-Eligible Courses",
      value: statsData?.ce_eligible_courses ?? 0,
      description: "Courses eligible for continuing education credits.",
      className: "border-[#ef6868] bg-[#fff1f1]",
    },
  ]
  return (
    <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
      {stats.map((stat) => (
        <article
          key={stat.title}
          className={`min-h-[132px] rounded-[8px] border p-6 ${stat.className}`}
        >
          <h2 className="text-[15px] font-medium leading-none text-[#111827]">
            {stat.title}
          </h2>
          <p className="mt-4 text-[32px] font-normal leading-none text-[#111827]">
            {stat.value}
          </p>
          <p className="mt-4 max-w-[230px] text-[15px] leading-[18px] text-[#4b5563]">
            {stat.description}
          </p>
        </article>
      ))}
    </div>
  )
}
