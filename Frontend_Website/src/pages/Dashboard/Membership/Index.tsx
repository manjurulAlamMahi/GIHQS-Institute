import MembershipComparison from "@/pages/Membership/components/MembershipComparison"

import { CurrentMembershipBanner } from "./components/CurrentMembershipBanner"

const DashboardMembershipPage = () => {
  return (
    <section className="min-h-full bg-[#f4f6f7] px-5 py-5">
      <div className="space-y-5">
        <CurrentMembershipBanner />
        <div className="[&>section]:px-0 [&>section]:py-0">
          <MembershipComparison showIntro={false} />
        </div>
      </div>
    </section>
  )
}

export default DashboardMembershipPage
