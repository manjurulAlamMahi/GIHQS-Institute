export function AccreditationOverview() {
  return (
    <section className="rounded-[10px] border border-border bg-white p-6 shadow-sm">
      <div className="flex items-start justify-between gap-4">
        <div>
          <h2 className="text-[16px] font-semibold text-[#111827]">
            Accreditation Overview
          </h2>
          <p className="mt-1 text-[13px] text-muted-foreground">
            Primary record details for the active accreditation cycle.
          </p>
        </div>
        <span className="inline-flex h-7 items-center rounded-[7px] bg-[#fff2c7] px-4 text-[13px] font-medium text-[#a57c1b]">
          Stage 2 of 3
        </span>
      </div>

      <div className="mt-8">
        <h3 className="text-[18px] font-semibold text-[#111827]">
          Patient Safety Certificate Program
        </h3>

        <div className="mt-7 grid gap-5 md:grid-cols-4">
          {[
            ["Application ID", "ACC-2026-014"],
            ["Institution", "GIHQS Education Division"],
            ["Submission Date", "Feb 10, 2026"],
            ["Current Status", "Documentation Review"],
          ].map(([label, value]) => (
            <div key={label}>
              <p className="text-[12px] font-medium uppercase text-[#667085]">
                {label}
              </p>
              <p className="mt-1 text-[14px] font-semibold text-[#111827]">
                {value}
              </p>
            </div>
          ))}
        </div>

        <div className="mt-6 h-1.5 rounded-full bg-[#e5e7eb]">
          <div className="h-full w-[72%] rounded-full bg-[#d9aa2f]" />
        </div>
        <p className="mt-3 text-[13px] text-muted-foreground">
          2 of 3 stages completed or in progress
        </p>
      </div>
    </section>
  )
}
