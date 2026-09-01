const timelineItems = [
  {
    step: "1",
    title: "Application & Eligibility Review",
    description:
      "Initial application submitted, eligibility reviewed, and accreditation file opened.",
    meta: "Initial checked on Feb 18, 2026",
    status: "Completed",
    statusClass: "bg-[#dcfce7] text-[#16a34a]",
  },
  {
    step: "2",
    title: "Documentation & Standards Review",
    description:
      "Standards mapping, evidence files, policies, assessments, and supporting materials are being reviewed.",
    meta: "In review since Feb 22, 2026",
    status: "In Review",
    statusClass: "bg-[#dbeafe] text-[#2563eb]",
  },
  {
    step: "3",
    title: "Final Review & Accreditation Decision",
    description:
      "Final findings, decision outcome, accreditation term, and award details will appear here after stage 2 is complete.",
    meta: "Pending",
    status: "Pending",
    statusClass: "bg-[#f1f3f5] text-[#667085]",
  },
]

export function ReviewTimeline() {
  return (
    <section className="rounded-[10px] border border-border bg-white p-6 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#111827]">Review Timeline</h2>
      <p className="mt-1 text-[13px] text-muted-foreground">
        Detailed workflow view for the active accreditation cycle.
      </p>

      <div className="mt-6 space-y-6">
        {timelineItems.map((item) => (
          <div key={item.step} className="flex items-start gap-4">
            <span className="flex size-8 shrink-0 items-center justify-center rounded-full bg-[#f1f3f5] text-[14px] font-medium text-[#667085]">
              {item.step}
            </span>

            <div className="min-w-0 flex-1">
              <div className="flex items-start justify-between gap-4">
                <h3 className="text-[16px] font-semibold text-[#111827]">
                  {item.title}
                </h3>
                <span
                  className={`inline-flex h-6 shrink-0 items-center rounded-full px-3 text-[12px] font-medium ${item.statusClass}`}
                >
                  {item.status}
                </span>
              </div>
              <p className="mt-1 text-[14px] leading-5 text-[#475467]">
                {item.description}
              </p>
              <p className="mt-1 text-[13px] text-muted-foreground">{item.meta}</p>
            </div>
          </div>
        ))}
      </div>
    </section>
  )
}
