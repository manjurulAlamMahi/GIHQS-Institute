import { CheckCircle2, CircleAlert } from "lucide-react"

const evidenceItems = [
  {
    text: "Program policy manual uploaded and accepted.",
    status: "Accepted",
    accepted: true,
  },
  {
    text: "Faculty qualification records need clarification and updated CV documentation.",
    status: "Needed",
    accepted: false,
  },
  {
    text: "Assessment methodology for Domain 3 requires revised mapping and scoring explanation.",
    status: "Needed",
    accepted: false,
  },
  {
    text: "One sample learner completion file is requested for evidence validation.",
    status: "Needed",
    accepted: false,
  },
]

export function EvidenceChecklist() {
  return (
    <section className="rounded-[10px] border border-border bg-white p-6 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#111827]">
        Requested Evidence Checklist
      </h2>
      <p className="mt-1 text-[13px] text-muted-foreground">
        Specific items requested by the reviewer.
      </p>

      <div className="mt-6 space-y-3">
        {evidenceItems.map((item) => {
          const Icon = item.accepted ? CheckCircle2 : CircleAlert

          return (
            <div
              key={item.text}
              className={`flex min-h-12 items-center justify-between gap-4 rounded-[7px] px-4 ${
                item.accepted ? "bg-[#e8f8ef]" : "bg-[#fff5e8]"
              }`}
            >
              <div className="flex items-center gap-3">
                <Icon
                  className={`size-4 ${item.accepted ? "text-[#16a34a]" : "text-[#f97316]"}`}
                  aria-hidden="true"
                />
                <p className="text-[14px] font-medium text-[#344054]">{item.text}</p>
              </div>
              <span
                className={`inline-flex h-6 shrink-0 items-center rounded-[5px] px-3 text-[12px] font-medium ${
                  item.accepted
                    ? "bg-[#d7f8e5] text-[#16a34a]"
                    : "bg-[#ffe5c7] text-[#f97316]"
                }`}
              >
                {item.status}
              </span>
            </div>
          )
        })}
      </div>
    </section>
  )
}
