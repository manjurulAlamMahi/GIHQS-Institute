export default function CatalogueHero() {
  return (
    <div className="w-full pt-6">
      <div
        className="relative overflow-hidden p-8 md:p-12 text-white shadow-md"
        style={{
          borderRadius: "17px",
          background:
            "radial-gradient(86.09% 205.22% at 86.22% 113.54%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(100deg, #0F2F26 0%, #1C4B3D 100%)",
        }}
      >
        
        <div className="inline-block rounded-full bg-white/10 px-4 py-1.5 border border-white/5 backdrop-blur-sm mb-6">
          <span className="text-[10px] md:text-xs font-semibold tracking-widest uppercase text-yellow-primary">
            Global Institute for Healthcare Quality & Safety
          </span>
        </div>

        {/* Hero Headline */}
        <h1 className="text-3xl md:text-5xl font-serif font-normal tracking-tight max-w-4xl leading-tight">
          GIHQS Professional{" "}
          <span className="italic text-yellow-primary font-normal font-serif">
            Development Catalogue
          </span>
        </h1>

        {/* Subtitle Description */}
        <p className="mt-4 text-xs md:text-sm text-neutral-300 max-w-4xl leading-relaxed font-sans font-light">
          A central catalogue of GIHQS certifications, courses, learning modules,
          toolkits, and future professional development offerings designed to
          support healthcare quality, patient safety, and high-reliability
          healthcare systems.
        </p>
      </div>
    </div>
  );
}
