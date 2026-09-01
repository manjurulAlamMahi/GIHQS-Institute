import { AddCeActivityForm } from "./components/AddCeActivityForm"
import { CeActivityHistory } from "./components/CeActivityHistory"
import { ContinuationCycles } from "./components/ContinuationCycles"

const CeTrackerPage = () => {
  return (
    <section id="ce-tracker-top" className="min-h-full bg-[#f4f6f7] px-5 py-5">
      <div className="space-y-6">
        <ContinuationCycles />
        <AddCeActivityForm />
        <CeActivityHistory />
      </div>
    </section>
  )
}

export default CeTrackerPage
