export function ApplicationHero() {
  return (
    <section
      className="overflow-hidden rounded-[18px] p-8 text-white shadow-sm md:p-12"
      style={{
        background:
          "radial-gradient(86.09% 205.22% at 86.22% 113.54%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(100deg, #0F2F26 0%, #1C4B3D 100%)",
      }}
    >
      <div className="inline-flex h-7 items-center rounded-full border border-[#d4aa3a]/60 px-4 text-[12px] font-semibold uppercase tracking-[0.18em] text-[#f0d070]">
        GIHQS Certification Application
      </div>
      <h1 className="mt-6 font-serif text-4xl font-normal leading-tight md:text-5xl">
        Apply for <span className="italic text-[#f0d070]">Certification</span>
      </h1>
      <p className="mt-5 max-w-4xl text-[15px] leading-6 text-white">
        Complete the application below to begin your certification journey.
        Applications are reviewed through a structured eligibility process before
        payment and exam scheduling.
      </p>
    </section>
  )
}
