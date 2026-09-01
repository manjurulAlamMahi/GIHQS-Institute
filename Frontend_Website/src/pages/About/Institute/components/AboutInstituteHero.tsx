import type { AboutInstituteData } from "@/types/about.types";

interface AboutInstituteHeroProps {
  data: AboutInstituteData;
}

export default function AboutInstituteHero({ data }: AboutInstituteHeroProps) {
  // Split description by newlines into paragraphs
  const paragraphs = data.description
    .split(/\n+/)
    .map(p => p.trim())
    .filter(p => p.length > 0);

  return (
    <section className="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
      <div
        className="grid overflow-hidden rounded-3xl px-6 py-10 shadow-[0_22px_60px_rgba(15,47,38,0.16)] md:grid-cols-[1fr_0.95fr] md:gap-10 md:px-12 md:py-14 lg:px-16"
        style={{
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
        }}
      >
        <div className="flex flex-col justify-center">
          <div className="w-fit rounded-full border border-[#D4AA3A]/70 bg-[#D4AA3A]/10 px-3.5 py-2 text-[0.68rem] font-semibold uppercase leading-none tracking-[0.16em] text-[#D4AA3A]">
            {data.tag_line}
          </div>

          <h1 className="mt-6 max-w-xl font-serif text-4xl font-medium leading-tight text-white md:text-5xl">
            {data.title1}{" "}
            <span className="italic text-[#D4AA3A]">
              {data.title2}
            </span>
          </h1>

          <div className="mt-7 max-w-xl space-y-5 text-sm leading-relaxed text-[#D8E6E1]">
            {paragraphs.map((para, idx) => (
              <p key={idx}>{para}</p>
            ))}
          </div>
        </div>

        <div className="mt-10 flex items-center justify-center md:mt-0">
          <div className="rounded-[6rem] border-[10px] border-white bg-white p-0 shadow-[0_18px_52px_rgba(0,0,0,0.20)]">
            <img
              src={data.image}
              alt={data.tag_line}
              className="aspect-square w-full max-w-[28rem] object-cover rounded-[5.5rem]"
            />
          </div>
        </div>
      </div>
    </section>
  );
}
