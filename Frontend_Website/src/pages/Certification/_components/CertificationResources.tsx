import { ArrowRight } from "lucide-react"

const resources = [
  {
    tag: "PREPARATION",
    title: "AIHQSP Study Guide",
    desc: "Earn professional credentials in healthcare quality, patient safety, standards, compliance, and responsible AI.",
    link: "View Study Guide",
  },
  {
    tag: "EXAM STRUCTURE",
    title: "Examination Blueprint",
    desc: "Access courses, toolkits, and structured learning designed for real healthcare system improvement.",
    link: "Download Blueprint",
  },
  {
    tag: "DEEP REFERENCE",
    title: "Exam Content Outline",
    desc: "Apply for accreditation for healthcare education programs and training providers through structured review.",
    link: "Download Eco",
  },
]

export default function CertificationResources() {
  return (
    <section className="py-10">
      <div className="mb-8">
        <span className="text-[10px] font-bold tracking-[0.2em] text-[#A57C1B] uppercase">
          Certification Resources
        </span>
        <h2 className="mt-2 font-serif text-3xl font-normal text-[#0F2F26] md:text-4xl">
          Twelve Domains of{" "}
          <span className="text-[#A57C1B] italic">Professional Mastery</span>
        </h2>
      </div>

      <div className="grid grid-cols-1 overflow-hidden rounded-md border border-[#d1dddb] bg-white md:grid-cols-3">
        {resources.map((res, i) => (
          <div
            key={i}
            className={`group flex min-h-64 flex-col p-8 transition-colors duration-200 hover:bg-[#f8faf9] ${
              i > 0 ? "border-t border-[#d1dddb] md:border-t-0 md:border-l" : ""
            }`}
          >
            <span className="mb-4 text-[9px] font-bold tracking-[0.2em] text-[#5b8276]">
              {res.tag}
            </span>
            <h3 className="mb-3 font-serif text-xl leading-tight font-semibold text-[#0F2F26]">
              {res.title}
            </h3>
            <p className="mb-8 grow text-sm leading-relaxed font-normal text-[#3A5A50]">
              {res.desc}
            </p>
            <button className="flex items-center gap-2 text-xs font-bold text-[#006045] transition-colors group-hover:text-[#A57C1B]">
              {res.link}{" "}
              <ArrowRight className="h-3 w-3 transition-transform group-hover:translate-x-1" />
            </button>
          </div>
        ))}
      </div>
    </section>
  )
}
