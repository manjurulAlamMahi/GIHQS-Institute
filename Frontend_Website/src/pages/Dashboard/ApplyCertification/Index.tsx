import { ApplicationHero } from "./components/ApplicationHero"
import { CertificationApplicationForm } from "./components/CertificationApplicationForm"

const ApplyCertificationPage = () => {
  return (
    <section className="min-h-full container mx-auto bg-[#f4f6f7] px-5 py-5 md:py-10">
      <div className="space-y-6">
        <ApplicationHero />
        <CertificationApplicationForm />
      </div>
    </section>
  )
}

export default ApplyCertificationPage
