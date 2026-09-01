import EcosystemHero from "./components/EcosystemHero"
import FlagshipCertifications from "./components/FlagshipCertifications"
import Hero from "./components/Hero"
import PathwayCTA from "./components/PathwayCTA"
import ProfessionalPathway from "./components/ProfessionalPathway"
import ServicesPathways from "./components/ServicesPathways"

const HomePage = () => {
  return (
    <main className="container mx-auto px-4 md:px-8">
        <Hero />
        <ServicesPathways />
        <EcosystemHero />
        <ProfessionalPathway />
        <FlagshipCertifications />
        <PathwayCTA />
    </main>
  )
}

export default HomePage