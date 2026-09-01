import { ScheduleCalendar } from "./components/ScheduleCalendar"
import { SelectedExamCard } from "./components/SelectedExamCard"

const MySchedulesPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-6">
      <div className="grid items-start gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.75fr)]">
        <ScheduleCalendar />
        <SelectedExamCard />
      </div>
    </section>
  )
}

export default MySchedulesPage
