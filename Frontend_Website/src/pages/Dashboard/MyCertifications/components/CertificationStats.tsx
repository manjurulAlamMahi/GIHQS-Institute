const certificationStats = [
  {
    title: "Active Courses",
    value: "3",
    description: "Courses currently in progress or awaiting final action.",
    className: "border-[#1f6f63] bg-[#eef6f3]",
  },
  {
    title: "In progress",
    value: "1",
    description: "Completed learning records available for review.",
    className: "border-[#4978ff] bg-[#eef4ff]",
  },
  {
    title: "Renewals due",
    value: "1",
    description: "Courses requiring an exam attempt or scheduling action.",
    className: "border-[#ddb737] bg-[#fbf8ea]",
  },
]

export function CertificationStats() {
  return (
    <div className="grid gap-6 lg:grid-cols-3">
      {certificationStats.map((item) => (
        <article
          key={item.title}
          className={`min-h-[132px] rounded-[8px] border p-6 ${item.className}`}
        >
          <h2 className="text-[15px] font-medium leading-none text-[#111827]">
            {item.title}
          </h2>
          <p className="mt-4 text-[30px] font-normal leading-none text-[#111827]">
            {item.value}
          </p>
          <p className="mt-4 max-w-[260px] text-[14px] leading-[18px] text-[#4b5563]">
            {item.description}
          </p>
        </article>
      ))}
    </div>
  )
}
