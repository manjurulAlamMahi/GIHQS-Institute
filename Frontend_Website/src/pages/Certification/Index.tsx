import CertAbout from "./_components/CertAbout"
import CertificationApply from "./_components/CertificationApply"
import CertHero from "./_components/CertHero"
import CertificationResources from "./_components/CertificationResources"
import CertTarget from "./_components/CertTarget"
import Domains from "./_components/Domain"
import ExamDeepDive from "./_components/ExamDeepDive"

export default function CertificationPage() {
  return (
    <main className="mx-auto w-full px-4 py-0 md:py-8 md:container md:px-8">
      <CertHero />
      <CertAbout />
      <CertTarget />
      <Domains />
      <ExamDeepDive />
      <CertificationResources />
      <CertificationApply />
    </main>
  )
}
