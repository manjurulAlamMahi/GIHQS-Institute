export default function CertTarget() {
  return (
    <div className="w-full pb-12 font-sans antialiased selection:bg-[#0F2F26]/10">
      <div
        style={{
          borderRadius: "24px",
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
        }}
        className="grid grid-cols-1 items-start gap-8 p-8 text-white shadow-xl md:grid-cols-12 md:p-12"
      >
        {/* Left Layout Grid: Target Audience Section */}
        <div className="space-y-8 md:col-span-7">
          {/* Header Title block */}
          <div className="max-w-xl space-y-4">
            <span className="text-[10px] font-bold tracking-widest text-neutral-400 uppercase md:text-xs">
              Who it's for
            </span>
            <h2 className="font-serif text-3xl leading-tight font-normal tracking-tight md:text-5xl">
              Designed for professionals at the intersection of{" "}
              <span className="font-serif font-normal text-[#F0D070] italic">
                AI and healthcare
              </span>
            </h2>
          </div>

          {/* Flexible Pill Tags wrapped flow layout */}
          <div className="flex flex-wrap gap-x-4 gap-y-3 pt-2">
            {[
              "Healthcare Quality Professionals",
              "Patient Safety Officers",
              "Clinical Informaticists",
              "Digital Health Specialists",
              "Healthcare Administrators",
              "Risk Management Professionals",
              "AI Governance Leaders",
              "Healthcare Educators",
            ].map((role) => (
              <div
                key={role}
                className="rounded-xl border border-white/10 bg-white/5 px-5 py-3 shadow-none backdrop-blur-sm"
              >
                <span className="text-xs font-medium tracking-wide text-neutral-200 md:text-sm">
                  {role}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Right Layout Grid: Prerequisites and Eligibility Card */}
        <div className="flex w-full flex-col items-center gap-6 pt-1 md:col-span-5 md:items-end">
          <div className="max-w-[320px] space-y-4 text-center md:text-right">
            <span className="text-[10px] font-bold tracking-widest text-neutral-400 uppercase">
              Eligibility
            </span>
            <div className="space-y-1">
              <span className="block font-serif text-xl font-medium text-white md:text-2xl">
                No formal prerequisites.
              </span>
              <span className="block font-serif text-xl font-medium text-[#F0D070] italic md:text-2xl">
                Open to all healthcare professionals.
              </span>
            </div>
          </div>

          {/* Information Panel Card utilizing backdrop blur effects and exact bullet details */}
          <div className="w-full max-w-105 rounded-2xl border border-white/5 bg-white/5 p-6 shadow-xl backdrop-blur-sm md:p-8">
            <span className="mb-8 block text-[10px] font-bold tracking-widest text-[#F0D070]/90 uppercase">
              Recommended Background
            </span>

            {/* Stats list rows using dash bullets */}
            <div className="space-y-5">
              {[
                "Professional experience in healthcare quality, patient safety, clinical practice, or health informatics",
                "Familiarity with healthcare operations, clinical workflows, or governance structures",
                "Interest in or exposure to AI, digital health, or clinical decision support systems",
                "Commitment to advancing safer, more reliable healthcare through responsible AI adoption",
              ].map((point, index) => (
                <div
                  key={index}
                  className="flex items-start gap-4 last:border-none"
                >
                  {/* Decorative bullet indicator using orange tint colors matching dashboards */}
                  <span className="-mt-0.5 shrink-0 font-mono text-lg leading-none text-[#A57C1B] opacity-80 select-none">
                    —
                  </span>
                  <p className="text-xs leading-relaxed font-normal text-neutral-300 md:text-sm">
                    {point}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
