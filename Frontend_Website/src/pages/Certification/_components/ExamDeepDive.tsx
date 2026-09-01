export default function ExamDeepDive() {
    const specs = [
        { label: "Certification", value: "AI Healthcare Quality & Safety Professional (AIHQSP)" },
        { label: "Exam Format", value: "Multiple-choice — 4 options, 1 correct response" },
        { label: "Questions", value: "150 scored questions" },
        { label: "Time Allowed", value: "3 hours (180 minutes)" },
        { label: "Passing Score", value: "600 on a scale of 200–800" },
        { label: "Delivery", value: "Online proctored — secure examination technology" },
        { label: "Domains", value: "12 competency domains" },
        { label: "Retake Policy", value: "Per GIHQS Certification Policy" },
    ];

    return (
        <section className=" w-full">
            <div
                style={{
                    borderRadius: "24px",
                    background: "radial-gradient(82.99% 75.78% at 82.22% 52.37%, rgba(212, 170, 58, 0.20) 0%, rgba(12, 42, 31, 0.20) 100%), linear-gradient(115deg, #0F2F26 0%, #133A2F 56%, #0B241D 100%)"
                }}
                className="p-8 md:p-12 grid grid-cols-1 lg:grid-cols-12 gap-12 text-white shadow-none"
            >
                {/* Left Side: Specs Table */}
                <div className="lg:col-span-7">
                    <span className="text-[10px] uppercase tracking-widest text-neutral-400">Examination Details</span>
                    <h2 className="text-3xl md:text-4xl font-serif mt-2 mb-8">
                        Everything You Need to Know <br /><span className="italic text-[#F0D070]">About The Exam</span>
                    </h2>

                    <div className="bg-white rounded-xl overflow-hidden shadow-lg">
                        {specs.map((spec, i) => (
                            <div key={i} className="grid grid-cols-3 border-b border-neutral-100 last:border-0">
                                <div className="p-4 text-[11px] font-bold text-[#0F2F26] bg-neutral-50/50 uppercase tracking-tighter">
                                    {spec.label}
                                </div>
                                <div className="col-span-2 p-4 text-sm text-neutral-600 font-medium border-l border-neutral-100">
                                    {spec.value}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Right Side: Process */}
                <div className="lg:col-span-5 flex flex-col justify-center">
                    <div className="mb-8">
                        <span className="text-[10px] uppercase tracking-widest text-neutral-400">Certification Process</span>
                        <p className="text-xl font-serif mt-2">No formal prerequisites.</p>
                        <p className="text-xl font-serif italic text-[#F0D070]">Open to all healthcare professionals.</p>
                    </div>

                    <div className="space-y-4">
                        {[
                            { id: "01", title: "Review the Examination Blueprint", text: "Understand the 12 domains and competencies assessed." },
                            { id: "02", title: "Prepare Using Study Guide & ECO", text: "Study knowledge statements across all twelve domains." },
                            { id: "03", title: "Apply and Schedule Your Exam", text: "Submit application via GIHQS candidate portal." }
                        ].map((step) => (
                            <div key={step.id} className="flex gap-4 p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <span className="text-[#F0D070] font-bold font-mono">{step.id}</span>
                                <div>
                                    <h4 className="text-sm font-bold">{step.title}</h4>
                                    <p className="text-xs text-neutral-400 mt-1">{step.text}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}