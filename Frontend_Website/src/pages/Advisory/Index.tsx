import AdvisoryHero from "./_components/AdvisoryHero"
import AdvisoryNeeds from "./_components/AdvisoryNeeds"
import AdvisoryScope from "./_components/AdvisoryScope"
import ServicePackages from "./_components/ServicePackages"
import TypicalDeliverables from "./_components/TypicalDeliverables"
import { useGetAdvisoryServicesQuery } from "@/features/advisory/api/advisoryApi"
import { UniversalPageSkeleton } from "@/components/shared/UniversalPageSkeleton"
import AutoIframe from "@/components/shared/AutoIframe"

export default function Advisory() {
  const { data: response, isLoading } = useGetAdvisoryServicesQuery()

  if (isLoading) {
    return (
      <main className="min-h-screen bg-[#F7FAF9]">
        <UniversalPageSkeleton />
      </main>
    )
  }

  const advisoryHeaders = response?.data?.advisory_headers

  if (advisoryHeaders?.injected_status && advisoryHeaders?.content_file) {
    return (
      <main className="min-h-screen bg-white">
        <AutoIframe src={advisoryHeaders.content_file} />
      </main>
    )
  }

  return (
    <div className="mx-auto my-10 w-full px-4 py-0 md:container md:px-8 md:py-8">
      <AdvisoryHero />
      <AdvisoryScope />
      <TypicalDeliverables />
      <ServicePackages />
      <AdvisoryNeeds />
    </div>
  )
}
