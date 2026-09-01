import CatalogueFilters from "./components/CatalogueFilters"
import CatalogueHero from "./components/CatalogueHero"
import OfferingGrid from "./components/OfferingGrid";

const ProfessionalDevelopmentCataloguePage = () => {
  return (
    <main className="container mx-auto px-4 md:px-8 py-0 md:py-8">
        <CatalogueHero />
        <CatalogueFilters />
        <OfferingGrid />
    </main>
  )
}

export default ProfessionalDevelopmentCataloguePage;