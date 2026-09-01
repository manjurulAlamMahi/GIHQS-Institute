import { Crown } from "lucide-react"

export function CurrentMembershipBanner() {
  return (
    <section className="rounded-[12px] bg-[#14392f] p-7 text-white shadow-sm">
      <div className="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
        <div>
          <div className="inline-flex h-6 items-center gap-1.5 rounded-full bg-[#f0d070] px-3 text-[13px] font-semibold text-[#14392f]">
            <Crown className="size-3.5" aria-hidden="true" />
            Premium · Active
          </div>
          <h1 className="mt-4 text-[26px] font-semibold leading-tight">
            You're on the Premium plan
          </h1>
          <p className="mt-2 text-[15px] text-white/70">
            Renews on January 3, 2027 · $199 / year billed to Visa ••4242
          </p>
        </div>

        <button className="h-10 rounded-[7px] bg-[#ddb737] px-5 text-[15px] font-semibold text-[#14392f] hover:bg-[#d0aa31] md:self-center">
          Upgrade plan
        </button>
      </div>
    </section>
  )
}
