const domains = [
  {
    id: "01",
    title: "Clinical AI Safety Science",
    desc: "Failure modes, drift, silent errors, edge cases",
  },
  {
    id: "02",
    title: "Algorithmic Accountability & Clinical Governance",
    desc: "Oversight, validation, escalation, accountability",
  },
  {
    id: "03",
    title: "Human-AI Teaming & Cognitive Safety",
    desc: "Trust calibration, alert fatigue, clinical judgment",
  },
  {
    id: "04",
    title: "Data Integrity, Bias & Representational Fairness",
    desc: "Dataset quality, equity impact, disparate outcomes",
  },
  {
    id: "05",
    title: "Real-World Validation & Post-Deployment Surveillance",
    desc: "Monitoring, drift detection, safety signals",
  },
  {
    id: "06",
    title: "AI-Enabled Diagnostic & Therapeutic Risk Management",
    desc: "CDS risk, triage tools, unintended consequences",
  },
  {
    id: "07",
    title: "Regulatory, Legal & Ethical Risk in AI-Driven Care",
    desc: "Compliance, liability, consent, transparency",
  },
  {
    id: "08",
    title: "Patient-Centered Transparency & Trust Design",
    desc: "Explainability, autonomy, shared decisions",
  },
  {
    id: "09",
    title: "Incident Investigation & Learning Systems",
    desc: "Root cause analysis, safety events, remediation",
  },
  {
    id: "10",
    title: "Workflow Integration & Clinical Process Safety",
    desc: "Human-AI orchestration, process mapping",
  },
  {
    id: "11",
    title: "Continuous Quality Improvement for AI Systems",
    desc: "Feedback loops, iterative refinement, outcomes",
  },
  {
    id: "12",
    title: "Organizational AI Readiness & Safety Culture",
    desc: "Leadership, competency, psychological safety",
  },
]

export default function Domains() {
  return (
    <section className="mx-auto w-full pb-10 font-sans">
      <div className="mb-8">
        <span className="text-[10px] font-bold tracking-[0.2em] text-[#A57C1B] uppercase">
          Examination Domains
        </span>
        <h2 className="mt-2 font-serif text-3xl font-normal text-[#0F2F26] md:text-4xl">
          Twelve Domains of{" "}
          <span className="text-[#A57C1B] italic">Professional Mastery</span>
        </h2>
      </div>

      <div className="grid grid-cols-1 overflow-hidden rounded-md border border-[#d1dddb] bg-white md:grid-cols-2 lg:grid-cols-3">
        {domains.map((item, index) => (
          <div
            key={item.id}
            className={`group min-h-36 p-6 transition-colors duration-200 hover:bg-[#f8faf9] ${
              index > 0 ? "border-t border-[#d1dddb]" : ""
            } ${index % 2 !== 0 ? "md:border-l md:border-[#d1dddb]" : ""} ${
              index >= 2 ? "md:border-t md:border-[#d1dddb]" : ""
            } ${
              index % 3 !== 0
                ? "lg:border-l lg:border-[#d1dddb]"
                : "lg:border-l-0"
            } ${index >= 3 ? "lg:border-t lg:border-[#d1dddb]" : "lg:border-t-0"}`}
          >
            <span className="font-serif text-3xl font-light tracking-tight text-[#8AA89C] transition-colors group-hover:text-[#A57C1B]">
              {item.id}
            </span>
            <h3 className="mt-1 font-serif text-lg leading-tight font-semibold text-[#0F2F26]">
              {item.title}
            </h3>
            <p className="mt-2 text-sm leading-relaxed font-normal text-[#3A5A50]">
              {item.desc}
            </p>
          </div>
        ))}
      </div>
    </section>
  )
}
