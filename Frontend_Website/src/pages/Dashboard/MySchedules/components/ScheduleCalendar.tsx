import { useQueryModal } from "@/hooks/useQueryModal"

const weekDays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]

const days = Array.from({ length: 31 }, (_, index) => index + 1)

const mutedDays = new Set([1, 2, 3, 4, 5, 6, 7, 8, 9, 13, 14, 15, 21, 22, 28, 29])

const timeSlots = ["8:00 AM", "9:00 AM", "10:30 AM", "1:00 PM", "2:30 PM", "4:00 PM"]

export function ScheduleCalendar() {
  const dateQuery = useQueryModal("date", "18")
  const timeQuery = useQueryModal("time", "9:00 AM")

  const selectedDay = Number(dateQuery.currentValue ?? "18")
  const selectedTime = timeQuery.currentValue ?? "9:00 AM"

  return (
    <section className="rounded-[10px] border border-border bg-white p-8 shadow-sm">
      <h2 className="text-[16px] font-semibold text-[#14392f]">
        Available dates -- March 2026
      </h2>

      <div className="mt-10 grid grid-cols-7 text-center text-[13px] text-muted-foreground">
        {weekDays.map((day) => (
          <div key={day}>{day}</div>
        ))}
      </div>

      <div className="mt-9 grid grid-cols-7 gap-y-8 text-center">
        {days.map((day) => {
          const isSelected = selectedDay === day
          const isMuted = mutedDays.has(day)

          return (
            <button
              key={day}
              type="button"
              onClick={() => dateQuery.open(String(day))}
              className={`mx-auto flex size-16 items-center justify-center rounded-[12px] text-[14px] transition-colors ${
                isSelected
                  ? "bg-[#14392f] text-white"
                  : isMuted
                    ? "text-[#c4c9cf] hover:bg-muted"
                    : "text-[#14392f] hover:bg-muted"
              }`}
            >
              {day}
            </button>
          )
        })}
      </div>

      <div className="mt-10 border-t border-border pt-6">
        <h3 className="text-[15px] font-semibold text-[#14392f]">
          Available time slots -- March {selectedDay}
        </h3>

        <div className="mt-5 grid gap-3 sm:grid-cols-3">
          {timeSlots.map((slot) => {
            const isSelected = selectedTime === slot

            return (
              <button
                key={slot}
                type="button"
                onClick={() => timeQuery.open(slot)}
                className={`h-11 rounded-[10px] border text-[15px] font-medium transition-colors ${
                  isSelected
                    ? "border-[#14392f] bg-[#14392f] text-white"
                    : "border-border bg-white text-[#111827] hover:bg-muted"
                }`}
              >
                {slot}
              </button>
            )
          })}
        </div>
      </div>
    </section>
  )
}
