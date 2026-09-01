import { ProfessionalDevelopmentFilters } from "./components/ProfessionalDevelopmentFilters"
import { ProfessionalDevelopmentGrid } from "./components/ProfessionalDevelopmentGrid"

const ProfessionalDevelopmentPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-5">
      <div className="space-y-6">
        <ProfessionalDevelopmentFilters />
        <ProfessionalDevelopmentGrid />
      </div>
    </section>
  )
}

export default ProfessionalDevelopmentPage
