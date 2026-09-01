import ApplicationForm from "./_components/ApplicationForm"
import ApplyAccreditationHero from "./_components/ApplyAccreditationHero"

export default function ApplyAccreditationPage() {
  return (
    <main className="w-full container mx-auto px-4 py-0 md:py-8 md:px-8 space-y-10">
        <ApplyAccreditationHero />
        <ApplicationForm />
    </main>
  )
}
