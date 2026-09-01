
import AccreditationCTA from "@/components/shared/AccreditationCTA";
import AccreditationDomains from "./components/AccreditationDomains";
import AccreditationEligibility from "./components/AccreditationEligibility";
import AccreditationFees from "./components/AccreditationFees";
import AccreditationHero from "./components/AccreditationHero";
import AccreditationInsights from "./components/AccreditationInsights";
import AccreditationProcess from "./components/AccreditationProcess";

const Accreditation = () => {
  return (
    <main className="bg-[#F7FAF9]">
      <AccreditationHero />
      <AccreditationEligibility />
      <AccreditationProcess />
      <AccreditationDomains />
      <AccreditationInsights />
      <AccreditationFees />
      <AccreditationCTA />
    </main>
  );
};

export default Accreditation;
