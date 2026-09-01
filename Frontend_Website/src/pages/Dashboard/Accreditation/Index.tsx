// import { AccreditationOverview } from "./components/AccreditationOverview"
// import { EvidenceChecklist } from "./components/EvidenceChecklist"
// import { ReviewTimeline } from "./components/ReviewTimeline"
// import { UploadedDocuments } from "./components/UploadedDocuments"
import { AccreditationTable } from "./components/AccreditationTable"

const DashboardAccreditationPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-5">
      <div className="space-y-6">
        <AccreditationTable />
        {/* <AccreditationOverview />
        <ReviewTimeline />
        <EvidenceChecklist />
        <UploadedDocuments /> */}
      </div>
    </section>
  )
}

export default DashboardAccreditationPage
