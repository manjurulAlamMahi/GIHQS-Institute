import { Card, CardContent } from "@/components/ui/card"

// Structured data for the table section on the left
const examSpecs = [
  {
    label: "Certification",
    value: "AI Healthcare Quality & Safety Professional (AIHQSP)",
  },
  {
    label: "Exam Format",
    value: "Multiple-choice — 4 options, 1 correct response",
  },
  { label: "Questions", value: "150 scored questions" },
  { label: "Time Allowed", value: "3 hours (180 minutes)" },
  { label: "Passing Score", value: "600 on a scale of 200–800" },
  {
    label: "Delivery",
    value: "Online proctored — secure examination technology",
  },
  { label: "Domains", value: "12 competency domains" },
  {
    label: "Retake Policy",
    value: "Per GIHQS Certification Examination Policy",
  },
]

// Structured data for the vertical timeline cards on the right
const processSteps = [
  {
    step: "01",
    title: "Review the Examination Blueprint",
    desc: "Understand the 12 domains, their relative weights, and the competencies assessed.",
  },
  {
    step: "02",
    title: "Prepare Using the Study Guide & ECO",
    desc: "Study the knowledge and task statements across all twelve domains.",
  },
  {
    step: "03",
    title: "Apply and Schedule Your Examination",
    desc: "Submit your application through the GIHQS candidate portal and schedule the online proctored examination.",
  },
  {
    step: "04",
    title: "Maintain Through Continuing Education",
    desc: "Sustain your credential through renewal cycles and ongoing professional development.",
  },
]

export default function ExamOverview() {
  return (
    <div className="mx-auto w-full px-4 font-sans antialiased selection:bg-[#0F2F26]/10">
      {/* Main layout container utilizing your exact nested radial + linear gradient parameters.
        This provides the uniform background, color depth, and rounded corners matching your ecosystem.
      */}
      <div
        style={{
          borderRadius: "24px",
          background:
            "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)",
        }}
        className="relative grid grid-cols-1 items-start gap-8 p-6 text-white shadow-xl md:p-12 lg:grid-cols-12 lg:gap-12"
      >
        {/* LEFT COLUMN: Section Title + Clean Specifications Matrix Table */}
        <div className="w-full space-y-6 lg:col-span-6">
          <div className="space-y-2">
            <span className="text-[10px] font-bold tracking-widest text-neutral-400 uppercase md:text-xs">
              Examination Details
            </span>
            <h2 className="font-serif text-2xl leading-tight font-normal tracking-tight md:text-4xl">
              Everything You Need to Know{" "}
              <span className="font-serif font-normal text-[#F0D070] italic">
                About The Exam
              </span>
            </h2>
          </div>

          {/* Solid White Specifications Grid Matrix Card Box */}
          <div className="overflow-hidden rounded-2xl border border-neutral-100 bg-white text-neutral-800 shadow-xl">
            <div className="divide-y divide-neutral-100">
              {examSpecs.map((spec, i) => (
                <div
                  key={i}
                  className="grid min-h-14 grid-cols-3 items-center text-xs md:text-sm"
                >
                  {/* Left Label Identifier cell column */}
                  <div className="col-span-1 flex h-full items-center border-r border-neutral-100 bg-neutral-50/50 px-4 py-3 font-semibold text-[#133A2F]/90 md:px-6">
                    {spec.label}
                  </div>
                  {/* Right Description Value cell column */}
                  <div className="col-span-2 px-4 py-3 leading-relaxed font-normal text-neutral-600 md:px-6">
                    {spec.value}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* RIGHT COLUMN: Certification Process Title + Frosted Timeline Card Stack */}
        <div className="w-full space-y-6 lg:col-span-6">
          <div className="space-y-1">
            <span className="text-[10px] font-bold tracking-widest text-neutral-400 uppercase md:text-xs">
              Certification Process
            </span>
            <h3 className="font-serif text-lg font-normal tracking-wide text-white md:text-xl">
              No formal prerequisites.
            </h3>
            <p className="font-serif text-lg font-normal text-[#F0D070] italic md:text-xl">
              Open to all healthcare professionals.
            </p>
          </div>

          {/* Stackable interactive timeline indicator nodes list */}
          <div className="space-y-4">
            {processSteps.map((item, idx) => (
              <Card
                key={idx}
                className="overflow-hidden rounded-xl border border-white/5 bg-white/5 shadow-none backdrop-blur-sm transition-all duration-200 hover:bg-white/10"
              >
                <CardContent className="flex items-start gap-4 p-4 text-white md:p-5">
                  {/* Step ID badge marker */}
                  <span className="shrink-0 rounded-md border border-white/10 bg-white/5 px-2.5 py-1 font-mono text-sm font-bold text-[#F0D070] shadow-inner select-none md:text-base">
                    {item.step}
                  </span>
                  {/* Copy content block header + snippet descriptions */}
                  <div className="space-y-1 pt-0.5">
                    <h4 className="text-sm font-semibold tracking-tight text-white md:text-base">
                      {item.title}
                    </h4>
                    <p className="text-xs leading-relaxed font-light text-neutral-300 md:text-sm">
                      {item.desc}
                    </p>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
