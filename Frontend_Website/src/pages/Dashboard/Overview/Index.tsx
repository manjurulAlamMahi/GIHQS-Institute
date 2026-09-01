import { DashboardAccreditationCard } from "./components/DashboardAccreditationCard"
import { DashboardActivityCards } from "./components/DashboardActivityCards"
import { DashboardStatsGrid } from "./components/DashboardStatsGrid"

const DashboardOverviewPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-6 py-6">
      <div className="mx-auto space-y-7">
        <DashboardStatsGrid />
        <DashboardActivityCards />
        <DashboardAccreditationCard />
      </div>
    </section>
  )
}

export default DashboardOverviewPage
