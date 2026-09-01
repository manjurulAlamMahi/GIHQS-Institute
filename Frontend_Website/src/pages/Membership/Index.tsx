import AccreditationCTA from "@/components/shared/AccreditationCTA";
import MembershipComparison from "./components/MembershipComparison";
import PaymentResultModal from "./components/PaymentResultModal";

const Membership = () => {
  return (
    <main className="bg-[#F7FAF9]">
      <MembershipComparison />
      <AccreditationCTA />
      <PaymentResultModal />
    </main>
  );
};

export default Membership;
