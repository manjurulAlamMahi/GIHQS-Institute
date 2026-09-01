import { useQueryModal } from "@/hooks/useQueryModal"

const weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]

function getMarchDateLabel(day: number) {
  const normalizedDay = Number.isFinite(day) && day >= 1 && day <= 31 ? day : 18
  const weekDay = weekDays[new Date(2026, 2, normalizedDay).getDay()]

  return `${weekDay}, March ${normalizedDay}, 2026`
}

export function SelectedExamCard() {
  const dateQuery = useQueryModal("date", "18")
  const timeQuery = useQueryModal("time", "9:00 AM")

  const selectedDay = Number(dateQuery.currentValue ?? "18")
  const selectedTime = timeQuery.currentValue ?? "9:00 AM"

  return (
    <aside className="rounded-[10px] border border-border bg-white p-8 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#14392f]">Selected exam</h2>

      <dl className="mt-12 space-y-5 text-[15px]">
        <div>
          <dt className="text-[13px] text-muted-foreground">Exam</dt>
          <dd className="mt-1 font-medium text-[#14392f]">CFP - Tax Planning</dd>
        </div>
        <div>
          <dt className="text-[13px] text-muted-foreground">Date</dt>
          <dd className="mt-1 font-medium text-[#14392f]">
            {getMarchDateLabel(selectedDay)}
          </dd>
        </div>
        <div>
          <dt className="text-[13px] text-muted-foreground">Time</dt>
          <dd className="mt-1 font-medium text-[#14392f]">{selectedTime} EST</dd>
        </div>
        <div>
          <dt className="text-[13px] text-muted-foreground">Format</dt>
          <dd className="mt-1 font-medium text-[#14392f]">Online proctored</dd>
        </div>
        <div>
          <dt className="text-[13px] text-muted-foreground">Duration</dt>
          <dd className="mt-1 font-medium text-[#14392f]">3 hours</dd>
        </div>
      </dl>

      <button className="mt-12 h-11 w-full rounded-[7px] bg-[#14392f] text-[15px] font-medium text-white hover:bg-[#0f2f26]">
        Confirm booking
      </button>
    </aside>
  )
}
